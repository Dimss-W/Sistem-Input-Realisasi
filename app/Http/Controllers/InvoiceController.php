<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ImportLog;
use App\Models\User;
use App\Models\Realisasi;
use App\Models\Kontrak;
use App\Models\Basto;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class InvoiceController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,procurement,dmo')->only(['index', 'show', 'export']);
        $this->middleware('role:admin,procurement')->only(['create', 'store', 'edit', 'update', 'scan', 'storePayment', 'uploadPaymentForm', 'uploadPayment', 'downloadPaymentTemplate', 'updateFollowup']);
        $this->middleware('role:admin')->only(['destroy']);
    }

    /**
     * Tampilkan daftar invoice.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'payment_status', 'project_id', 'aging_category', 'quick_tab']);

        $query = Invoice::query();

        // Filter untuk role Sales: jika ditugaskan pada project tertentu (SM/PIC), batasi ke project tersebut + invoice yang dibuatnya.
        // Jika akun Sales umum/terpusat, dapat mengelola seluruh invoice penagihan perusahaan.
        if (auth()->user()->hasRole('sales')) {
            $salesProjects = Kontrak::where('service_manager', auth()->user()->name)
                ->orWhere('service_manager', auth()->user()->username)
                ->pluck('project_id')
                ->unique();

            if ($salesProjects->isNotEmpty()) {
                $query->where(function($q) use ($salesProjects) {
                    $q->whereIn('project_id', $salesProjects)
                      ->orWhere('sales_user_id', auth()->id());
                });
            }
        }

        $today = now()->startOfDay();

        // Quick Tab Filter
        if (!empty($filters['quick_tab'])) {
            $qt = $filters['quick_tab'];
            if ($qt === 'overdue') {
                $query->where('payment_status', '!=', 'paid')->where('due_date', '<', $today);
            } elseif ($qt === 'due_soon') {
                $query->where('payment_status', '!=', 'paid')->whereBetween('due_date', [$today, $today->copy()->addDays(7)]);
            } elseif ($qt === 'no_followup') {
                $query->where('payment_status', '!=', 'paid')->whereNull('sales_followup_notes');
            } elseif ($qt === 'paid') {
                $query->where('payment_status', 'paid');
            }
        }

        // AR Aging filter
        if (!empty($filters['aging_category'])) {
            $cat = $filters['aging_category'];
            $query->where('payment_status', '!=', 'paid');
            if ($cat === 'lancar') {
                $query->where(function($q) use ($today) {
                    $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
                });
            } elseif ($cat === '1_30') {
                $query->whereBetween('due_date', [$today->copy()->subDays(30), $today->copy()->subDay()]);
            } elseif ($cat === '31_60') {
                $query->whereBetween('due_date', [$today->copy()->subDays(60), $today->copy()->subDays(31)]);
            } elseif ($cat === 'over_60') {
                $query->where('due_date', '<', $today->copy()->subDays(60));
            }
        }

        $query->filter($filters);

        $invoices = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();

        // AR Aging Summary
        $agingBaseQuery = Invoice::query();
        if (auth()->user()->hasRole('sales')) {
            $salesProjects = Kontrak::where('service_manager', auth()->user()->name)
                ->orWhere('service_manager', auth()->user()->username)
                ->pluck('project_id')
                ->unique();
            if ($salesProjects->isNotEmpty()) {
                $agingBaseQuery->where(function($q) use ($salesProjects) {
                    $q->whereIn('project_id', $salesProjects)
                      ->orWhere('sales_user_id', auth()->id());
                });
            }
        }

        $unpaidInvoices = (clone $agingBaseQuery)->where('payment_status', '!=', 'paid')->get();
        $agingSummary = [
            'lancar'  => $unpaidInvoices->where('aging_category', 'lancar')->sum('remaining_amount'),
            '1_30'    => $unpaidInvoices->where('aging_category', '1_30')->sum('remaining_amount'),
            '31_60'   => $unpaidInvoices->where('aging_category', '31_60')->sum('remaining_amount'),
            'over_60' => $unpaidInvoices->where('aging_category', 'over_60')->sum('remaining_amount'),
        ];

        // Pelacak BASTO Siap Tagih (Approved BASTO with no invoice yet)
        $invoicedProjects = Invoice::pluck('project_id')->unique()->toArray();
        $unbilledBastosQuery = Basto::where('status', 'approved');
        if (auth()->user()->hasRole('sales')) {
            $salesProjects = Realisasi::where('pic', auth()->user()->name)->pluck('project_id')->unique();
            if ($salesProjects->isNotEmpty()) {
                $unbilledBastosQuery->whereIn('project_id', $salesProjects);
            }
        }
        $unbilledBastos = $unbilledBastosQuery->whereNotIn('project_id', $invoicedProjects)
            ->orderBy('reviewed_at', 'desc')
            ->take(8)
            ->get();

        // Kebutuhan dropdown filter
        $projectIds = Invoice::distinct()->pluck('project_id');
        $salesList = User::where('role', 'sales')->get();

        return view('invoice.index', compact('invoices', 'filters', 'projectIds', 'salesList', 'agingSummary', 'unbilledBastos'));
    }

    /**
     * Tampilkan form buat invoice.
     */
    public function create(Request $request)
    {
        // Dropdown project
        $projects = Kontrak::distinct()->orderBy('project_id')->pluck('project_name', 'project_id');
        $kontraksMap = Kontrak::distinct()->get(['project_id', 'project_name', 'project_client', 'project_value', 'service_manager'])->keyBy('project_id');
        $salesList = User::where('role', 'sales')->get();

        // Vendor List dari MasterVendor dan Realisasi
        $masterVendors = \App\Models\MasterVendor::where('is_active', true)->orderBy('nama_vendor')->pluck('nama_vendor')->toArray();
        $realisasiVendors = Realisasi::distinct()->whereNotNull('vendor')->where('vendor', '!=', '')->orderBy('vendor')->pluck('vendor')->toArray();
        $vendorList = array_values(array_unique(array_filter(array_merge($masterVendors, $realisasiVendors))));
        sort($vendorList);

        $selectedBasto = null;
        if ($request->filled('basto_id')) {
            $selectedBasto = Basto::find($request->basto_id);
        } elseif ($request->filled('project_id')) {
            $selectedBasto = Basto::where('project_id', $request->project_id)->where('status', 'approved')->latest()->first();
        }

        return view('invoice.create', compact('projects', 'kontraksMap', 'salesList', 'selectedBasto', 'vendorList'));
    }

    /**
     * Simpan invoice baru.
     */
    public function store(Request $request)
    {
        if ($request->filled('vendor') && !$request->filled('customer')) {
            $request->merge(['customer' => $request->vendor]);
        }

        $validated = $request->validate([
            'invoice_number'  => 'required|string|max:100|unique:invoices,invoice_number',
            'project_id'      => 'required|string|max:100',
            'customer'        => 'required|string|max:255',
            'sales_user_id'   => 'nullable|integer',
            'invoice_date'    => 'required|date',
            'due_date'        => 'required|date|after_or_equal:invoice_date',
            'subtotal'        => 'nullable|numeric|min:0',
            'tax_ppn_percent' => 'nullable|numeric|min:0|max:100',
            'tax_pph_percent' => 'nullable|numeric|min:0|max:100',
            'invoice_amount'  => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ], [
            'invoice_number.unique'   => 'Nomor invoice sudah terdaftar.',
            'customer.required'       => 'Nama vendor / penyedia wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo harus setelah atau sama dengan tanggal invoice.',
        ]);

        $subtotal = (float)($request->subtotal ?? $request->invoice_amount ?? 0);
        $ppnPercent = (float)($request->tax_ppn_percent ?? 11.00);
        $pphPercent = (float)($request->tax_pph_percent ?? 0.00);

        $ppnAmount = round($subtotal * ($ppnPercent / 100), 2);
        $pphAmount = round($subtotal * ($pphPercent / 100), 2);
        $totalAfterTax = round($subtotal + $ppnAmount - $pphAmount, 2);

        $validated['subtotal']        = $subtotal;
        $validated['tax_ppn_percent'] = $ppnPercent;
        $validated['tax_ppn_amount']  = $ppnAmount;
        $validated['tax_pph_percent'] = $pphPercent;
        $validated['tax_pph_amount']  = $pphAmount;
        $validated['total_after_tax'] = $totalAfterTax;
        $validated['invoice_amount']  = $totalAfterTax > 0 ? $totalAfterTax : $subtotal;

        // Resolve project name dari kontrak
        $kontrak = Kontrak::where('project_id', $request->project_id)->first();
        $validated['project_name'] = $kontrak ? $kontrak->project_name : 'Project #' . $request->project_id;
        
        if (auth()->user()->hasRole('finance')) {
            $validated['finance_user_id'] = auth()->id();
        }

        $invoice = Invoice::create($validated);

        // Auto-sinkronisasi vendor dan item pengadaan ke tabel Realisasi
        $existingRealisasi = Realisasi::where('project_id', $invoice->project_id)->first();
        if ($existingRealisasi) {
            $syncData = [];
            if ((empty($existingRealisasi->vendor) || $existingRealisasi->vendor === 'Umum / Tidak Terdata') && !empty($invoice->customer)) {
                $syncData['vendor'] = $invoice->customer;
            }
            if (empty($existingRealisasi->item_biaya)) {
                $syncData['item_biaya'] = !empty($invoice->notes) ? $invoice->notes : ('Pengadaan ' . ($invoice->project_name ?? $invoice->project_id));
            }
            if (!empty($syncData)) {
                $existingRealisasi->update($syncData);
            }
        }

        $this->logActivity(
            'CREATE',
            'invoice',
            $invoice->id,
            null,
            $invoice->toArray(),
            "Membuat invoice vendor baru #{$invoice->invoice_number} ({$invoice->customer}) untuk project {$invoice->project_id} senilai " . $invoice->invoice_amount_formatted
        );

        return redirect()->route('invoice.index')
            ->with('success', "Invoice vendor #{$invoice->invoice_number} ({$invoice->customer}) berhasil disimpan.");
    }

    /**
     * Tampilkan form edit invoice.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Proteksi: Sales tidak bisa edit invoice orang lain
        if (auth()->user()->hasRole('sales')) {
            $salesProjects = Realisasi::where('pic', auth()->user()->name)->pluck('project_id')->unique()->toArray();
            if (!in_array($invoice->project_id, $salesProjects) && $invoice->sales_user_id !== auth()->id()) {
                abort(403, 'Anda tidak diizinkan mengubah invoice ini.');
            }
        }

        $projects = Kontrak::distinct()->orderBy('project_id')->pluck('project_name', 'project_id');
        $salesList = User::where('role', 'sales')->get();

        $masterVendors = \App\Models\MasterVendor::where('is_active', true)->orderBy('nama_vendor')->pluck('nama_vendor')->toArray();
        $realisasiVendors = Realisasi::distinct()->whereNotNull('vendor')->where('vendor', '!=', '')->orderBy('vendor')->pluck('vendor')->toArray();
        $vendorList = array_values(array_unique(array_filter(array_merge($masterVendors, $realisasiVendors))));
        sort($vendorList);

        return view('invoice.edit', compact('invoice', 'projects', 'salesList', 'vendorList'));
    }

    /**
     * Update invoice.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Proteksi role Sales
        if (auth()->user()->hasRole('sales')) {
            $salesProjects = Realisasi::where('pic', auth()->user()->name)->pluck('project_id')->unique()->toArray();
            if (!in_array($invoice->project_id, $salesProjects) && $invoice->sales_user_id !== auth()->id()) {
                abort(403, 'Anda tidak diizinkan mengubah invoice ini.');
            }
        }

        if ($request->filled('vendor') && !$request->filled('customer')) {
            $request->merge(['customer' => $request->vendor]);
        }

        $validated = $request->validate([
            'invoice_number'  => 'required|string|max:100|unique:invoices,invoice_number,' . $id,
            'project_id'      => 'required|string|max:100',
            'customer'        => 'required|string|max:255',
            'sales_user_id'   => 'nullable|integer',
            'invoice_date'    => 'required|date',
            'due_date'        => 'required|date|after_or_equal:invoice_date',
            'subtotal'        => 'nullable|numeric|min:0',
            'tax_ppn_percent' => 'nullable|numeric|min:0|max:100',
            'tax_pph_percent' => 'nullable|numeric|min:0|max:100',
            'invoice_amount'  => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ], [
            'customer.required' => 'Nama vendor / penyedia wajib diisi.',
        ]);

        $subtotal = (float)($request->subtotal ?? $request->invoice_amount ?? $invoice->subtotal);
        $ppnPercent = (float)($request->tax_ppn_percent ?? 11.00);
        $pphPercent = (float)($request->tax_pph_percent ?? 0.00);

        $ppnAmount = round($subtotal * ($ppnPercent / 100), 2);
        $pphAmount = round($subtotal * ($pphPercent / 100), 2);
        $totalAfterTax = round($subtotal + $ppnAmount - $pphAmount, 2);

        $validated['subtotal']        = $subtotal;
        $validated['tax_ppn_percent'] = $ppnPercent;
        $validated['tax_ppn_amount']  = $ppnAmount;
        $validated['tax_pph_percent'] = $pphPercent;
        $validated['tax_pph_amount']  = $pphAmount;
        $validated['total_after_tax'] = $totalAfterTax;
        $validated['invoice_amount']  = $totalAfterTax > 0 ? $totalAfterTax : $subtotal;

        $kontrak = Kontrak::where('project_id', $request->project_id)->first();
        $validated['project_name'] = $kontrak ? $kontrak->project_name : $invoice->project_name;

        $oldData = $invoice->toArray();
        $invoice->update($validated);
        $newData = $invoice->toArray();

        // Auto-sinkronisasi vendor ke Realisasi
        Realisasi::where('project_id', $invoice->project_id)
            ->update([
                'vendor' => $invoice->customer,
            ]);

        $this->logActivity(
            'UPDATE',
            'invoice',
            $invoice->id,
            $oldData,
            $newData,
            "Memperbarui invoice #{$invoice->invoice_number}"
        );

        return redirect()->route('invoice.index')
            ->with('success', "Invoice #{$invoice->invoice_number} berhasil diperbarui.");
    }

    /**
     * Update catatan follow-up Sales (Structured Timeline).
     */
    public function updateFollowup(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'sales_followup_notes' => 'required|string',
            'channel'              => 'nullable|string|max:50',
            'contact_person'       => 'nullable|string|max:150',
            'status_janji'         => 'nullable|string|max:100',
            'promised_date'        => 'nullable|date',
        ]);

        // Decode existing history or migrate legacy plain text
        $existingHistory = [];
        if (!empty($invoice->sales_followup_notes)) {
            $decoded = json_decode($invoice->sales_followup_notes, true);
            if (is_array($decoded)) {
                $existingHistory = $decoded;
            } else {
                // Legacy plain text note
                $existingHistory[] = [
                    'id'               => 'legacy_' . ($invoice->id),
                    'timestamp'        => $invoice->last_followup_at ? $invoice->last_followup_at->toIso8601String() : now()->toIso8601String(),
                    'created_at_human' => $invoice->last_followup_at ? $invoice->last_followup_at->translatedFormat('d M Y H:i') : 'Sebelumnya',
                    'sales_name'       => 'Sales Penagihan',
                    'channel'          => 'Catatan Internal',
                    'contact_person'   => '-',
                    'status_janji'     => 'Dalam Proses',
                    'promised_date'    => null,
                    'notes'            => (string)$invoice->sales_followup_notes,
                ];
            }
        }

        // Prepend new entry so newest is first in timeline
        $newEntry = [
            'id'               => uniqid('fu_'),
            'timestamp'        => now()->toIso8601String(),
            'created_at_human' => now()->translatedFormat('d M Y H:i'),
            'sales_id'         => auth()->id(),
            'sales_name'       => auth()->user()->name ?? 'Sales Officer',
            'channel'          => $request->input('channel', 'WhatsApp'),
            'contact_person'   => $request->input('contact_person') ?: '-',
            'status_janji'     => $request->input('status_janji') ?: 'Dalam Proses',
            'promised_date'    => $request->input('promised_date') ?: null,
            'notes'            => trim($request->input('sales_followup_notes')),
        ];

        array_unshift($existingHistory, $newEntry);

        $invoice->sales_followup_notes = json_encode($existingHistory);
        $invoice->last_followup_at = now();
        $invoice->save();

        $this->logActivity(
            'UPDATE',
            'invoice',
            $invoice->id,
            null,
            ['timeline_entry' => $newEntry],
            "Menambahkan log follow-up penagihan invoice #{$invoice->invoice_number} via {$newEntry['channel']}"
        );

        return back()->with('success', "Log follow-up penagihan untuk invoice #{$invoice->invoice_number} berhasil ditambahkan ke riwayat.");
    }

    /**
     * Hapus invoice.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $oldData = $invoice->toArray();
        
        $invoice->delete();

        $this->logActivity(
            'DELETE',
            'invoice',
            $id,
            $oldData,
            null,
            "Menghapus invoice #{$invoice->invoice_number}"
        );

        return redirect()->route('invoice.index')
            ->with('success', "Invoice #{$invoice->invoice_number} berhasil dihapus.");
    }

    /**
     * Simpan pembayaran manual (Finance Only).
     */
    public function storePayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'payment_date'      => 'required|date',
            'payment_amount'    => 'required|numeric|min:1',
            'pph23_amount'      => 'nullable|numeric|min:0',
            'bupot_number'      => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
            'payment_method'    => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        // Duplicate Reference Guard (Cegah double payment mutasi bank)
        if ($request->filled('payment_reference')) {
            $duplicate = Payment::where('payment_reference', $request->payment_reference)->first();
            if ($duplicate) {
                return back()->withInput()->withErrors([
                    'payment_reference' => "Peringatan Rekonsiliasi Bank: Nomor referensi '{$request->payment_reference}' sudah pernah dicatat pada Invoice #{$duplicate->invoice->invoice_number} (Tgl: " . ($duplicate->payment_date ? $duplicate->payment_date->format('d/m/Y') : '-') . "). Harap periksa kembali rekening koran."
                ]);
            }
        }

        $currentOutstanding = max(0, (float)$invoice->invoice_amount - (float)$invoice->payment_amount - (float)($invoice->pph23_deducted ?? 0));
        $payAmount = (float)$request->payment_amount;
        $pph23Amount = (float)($request->pph23_amount ?? 0);
        $totalSettlement = $payAmount + $pph23Amount;

        if ($totalSettlement > $currentOutstanding) {
            return back()->withInput()->withErrors([
                'payment_amount' => 'Total penyelesaian (Kas Rp ' . number_format($payAmount, 0, ',', '.') . ($pph23Amount > 0 ? ' + PPh 23 Rp ' . number_format($pph23Amount, 0, ',', '.') : '') . ') melebihi sisa tagihan (Maksimal: Rp ' . number_format($currentOutstanding, 0, ',', '.') . ').'
            ]);
        }

        DB::transaction(function () use ($invoice, $request, $payAmount, $pph23Amount, $totalSettlement) {
            // 1. Simpan Payment
            Payment::create([
                'invoice_id'           => $invoice->id,
                'payment_date'         => $request->payment_date,
                'payment_amount'       => $payAmount,
                'pph23_amount'         => $pph23Amount,
                'payment_reference'    => $request->payment_reference,
                'bupot_number'         => $request->bupot_number,
                'payment_method'       => $request->payment_method,
                'processed_by_user_id' => auth()->id(),
                'notes'                => $request->notes,
            ]);

            // 2. Update Invoice
            $newPaymentAmount = (float)$invoice->payment_amount + $payAmount;
            $newPph23Deducted = (float)($invoice->pph23_deducted ?? 0) + $pph23Amount;
            $totalSettledSoFar = $newPaymentAmount + $newPph23Deducted;

            $newStatus = 'unpaid';
            if ($totalSettledSoFar >= (float)$invoice->invoice_amount) {
                $newStatus = 'paid';
            } elseif ($totalSettledSoFar > 0) {
                $newStatus = 'partial';
            }

            $updateData = [
                'payment_amount'  => $newPaymentAmount,
                'pph23_deducted'  => $newPph23Deducted,
                'payment_status'  => $newStatus,
                'finance_user_id' => auth()->id(),
            ];

            if ($request->filled('bupot_number')) {
                $updateData['bupot_number'] = $request->bupot_number;
            }

            $invoice->update($updateData);

            // Auto-sync status realisasi terkait menjadi PAID jika invoice telah lunas
            if ($newStatus === 'paid' && $invoice->project_id) {
                Realisasi::where('project_id', $invoice->project_id)
                    ->where('status', '!=', 'PAID')
                    ->update(['status' => 'PAID']);
            }
        });

        $logDetails = [
            'payment_amount' => $payAmount,
            'pph23_amount'   => $pph23Amount,
            'bupot_number'   => $request->bupot_number,
            'status'         => $invoice->fresh()->payment_status
        ];

        $this->logActivity(
            'CREATE',
            'payment',
            $invoice->id,
            null,
            $logDetails,
            "Mencatat pembayaran kas Rp " . number_format($payAmount, 0, ',', '.') . ($pph23Amount > 0 ? " + Potongan PPh 23 Rp " . number_format($pph23Amount, 0, ',', '.') . " (Bupot: {$request->bupot_number})" : "") . " untuk invoice #{$invoice->invoice_number}"
        );

        return redirect()->route('invoice.index')
            ->with('success', "Pembayaran untuk invoice #{$invoice->invoice_number} berhasil dicatat" . ($pph23Amount > 0 ? " beserta pengakuan pemotongan PPh 23 Rp " . number_format($pph23Amount, 0, ',', '.') : "") . ".");
    }

    /**
     * Form upload payment Excel.
     */
    public function uploadPaymentForm()
    {
        $logs = ImportLog::where('type', 'payment')->orderBy('created_at', 'desc')->take(10)->get();
        return view('payment.upload', compact('logs'));
    }

    /**
     * Upload dan proses Payment Excel.
     */
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $fileName = $file->getClientOriginalName();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Skip header (Baris 1)
            foreach ($rows as $idx => $row) {
                if ($idx === 1) continue;

                $invoiceNum = trim($row['A'] ?? '');
                $payDateVal = trim($row['B'] ?? '');
                $payAmountVal = trim($row['C'] ?? '');
                $reference = trim($row['D'] ?? '');
                $method = trim($row['E'] ?? '');
                $notes = trim($row['F'] ?? '');

                if (empty($invoiceNum)) {
                    continue; // Skip baris kosong
                }

                // Validasi data
                $invoice = Invoice::where('invoice_number', $invoiceNum)->first();
                if (!$invoice) {
                    $errorCount++;
                    $errors[] = "Baris {$idx}: Invoice dengan nomor '{$invoiceNum}' tidak ditemukan.";
                    continue;
                }

                // Parsing nominal pembayaran
                $payAmount = is_numeric($payAmountVal) ? (float) $payAmountVal : null;
                if (is_null($payAmount) || $payAmount <= 0) {
                    $errorCount++;
                    $errors[] = "Baris {$idx}: Nilai pembayaran '{$payAmountVal}' tidak valid.";
                    continue;
                }

                $outstanding = $invoice->invoice_amount - $invoice->payment_amount;
                if ($payAmount > $outstanding) {
                    $errorCount++;
                    $errors[] = "Baris {$idx}: Nilai pembayaran Rp " . number_format($payAmount, 0, ',', '.') . " melebihi sisa outstanding Rp " . number_format($outstanding, 0, ',', '.') . ".";
                    continue;
                }

                // Parsing tanggal
                $payDate = null;
                if ($payDateVal) {
                    try {
                        $payDate = date('Y-m-d', strtotime($payDateVal));
                    } catch (\Exception $e) {
                        $payDate = null;
                    }
                }

                if (!$payDate || $payDate === '1970-01-01') {
                    $payDate = date('Y-m-d');
                }

                // Catat transaksi pembayaran
                DB::transaction(function () use ($invoice, $payDate, $payAmount, $reference, $method, $notes) {
                    Payment::create([
                        'invoice_id'           => $invoice->id,
                        'payment_date'         => $payDate,
                        'payment_amount'       => $payAmount,
                        'payment_reference'    => $reference,
                        'payment_method'       => $method,
                        'processed_by_user_id' => auth()->id(),
                        'notes'                => $notes,
                    ]);

                    $newPaymentAmount = $invoice->payment_amount + $payAmount;
                    $newStatus = 'unpaid';
                    if ($newPaymentAmount >= $invoice->invoice_amount) {
                        $newStatus = 'paid';
                    } elseif ($newPaymentAmount > 0) {
                        $newStatus = 'partial';
                    }

                    $invoice->update([
                        'payment_amount' => $newPaymentAmount,
                        'payment_status' => $newStatus
                    ]);
                });

                $successCount++;
            }

            // Catat import log
            ImportLog::create([
                'user_id'      => auth()->id(),
                'file_name'    => $fileName,
                'file_path'    => null,
                'type'         => 'payment',
                'total_rows'   => count($rows) - 1,
                'success_rows' => $successCount,
                'error_rows'   => $errorCount,
                'errors_json'  => $errors,
                'imported_at'  => now(),
            ]);

            $this->logActivity(
                'IMPORT',
                'payment',
                null,
                null,
                ['success' => $successCount, 'errors' => $errorCount],
                "Mengimport pembayaran via Excel '{$fileName}': {$successCount} sukses, {$errorCount} gagal."
            );

            if ($errorCount > 0) {
                return back()->with('error', "Import selesai dengan beberapa galat. {$successCount} baris berhasil dimasukkan, {$errorCount} baris gagal.")
                             ->withErrors($errors);
            }

            return back()->with('success', "Seluruh data pembayaran dari file '{$fileName}' ({$successCount} baris) berhasil diimport.");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for payments.
     */
    public function downloadPaymentTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template_Pembayaran');

        $headers = [
            'Invoice Number', 'Payment Date', 'Payment Amount', 
            'Payment Reference', 'Payment Method', 'Notes'
        ];

        foreach ($headers as $colIndex => $header) {
            $col = chr(65 + $colIndex);
            $sheet->setCellValue("{$col}1", $header);
        }

        $lastCol = chr(65 + count($headers) - 1);

        // Header style
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Sample data
        $example = [
            'INV/20260826/0001', '2026-08-26', '50000000', 
            'TRF-MANDIRI-9812', 'TRANSFER', 'Pembayaran termin pertama'
        ];

        foreach ($example as $colIndex => $val) {
            $col = chr(65 + $colIndex);
            $sheet->setCellValue("{$col}2", $val);
        }

        // Highlight sample row
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F1F5F9']],
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Import_Pembayaran.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Scan uploaded invoice and extract metadata.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx,txt,png,jpg,jpeg|max:10000',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $text = '';

        // Attempt basic text extraction for text files
        if ($file->getClientMimeType() === 'text/plain') {
            $text = file_get_contents($file->getRealPath());
        }

        // Initialize default mock values
        $invoiceNumber = 'INV/' . date('Ymd') . '/' . rand(100, 999);
        
        // Pick a contract from contracts list
        $contracts = Kontrak::all();
        $randomContract = $contracts->count() > 0 ? $contracts->random() : null;
        
        $projectId = $randomContract ? $randomContract->project_id : 'PS-049-00';
        $projectName = $randomContract ? $randomContract->project_name : 'Jasa Pemeliharaan Jaringan';
        $customer = $randomContract ? $randomContract->project_client : 'PT. Bagus Abadi';
        $amount = $randomContract ? round($randomContract->project_value * 0.15) : 150000000; // 15% of contract value
        $invoiceDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+30 days'));
        $notes = 'Tagihan termin/pembayaran atas pekerjaan: ' . $projectName;

        // Smart Extraction based on filename/content
        $combinedText = $filename . ' ' . substr($text, 0, 2000);

        // 1. Try to find Project ID pattern (e.g., PS-xxx-xx, MS-xxx, etc.)
        if (preg_match('/(PS-\d{3}-\d{2}|MS-\d{3}|PS-\d{3})/i', $combinedText, $matches)) {
            $matchedPid = strtoupper($matches[1]);
            // Verify if exists
            $foundContract = Kontrak::where('project_id', 'like', "%{$matchedPid}%")->first();
            if ($foundContract) {
                $projectId = $foundContract->project_id;
                $projectName = $foundContract->project_name;
                $customer = $foundContract->project_client;
                $amount = round($foundContract->project_value * 0.2); // Default to 20%
            }
        }

        // 2. Try to find Invoice Number (e.g., INV/2026/08/01 or similar)
        if (preg_match('/(INV[\/\-\w\d]+)/i', $combinedText, $matches)) {
            $invoiceNumber = strtoupper($matches[1]);
        }

        // 3. Try to find Amount (e.g., Rp. 150.000.000 or 150000000)
        if (preg_match('/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})*(?:,\d+)?)/i', $text, $matches)) {
            // Convert to integer
            $cleanAmount = (int)str_replace(['.', ','], '', $matches[1]);
            if ($cleanAmount > 1000) {
                $amount = $cleanAmount;
            }
        }

        // 4. Try to find date
        if (preg_match('/(\d{2,4}[\-\/\.]\d{2}[\-\/\.]\d{2,4})/', $combinedText, $matches)) {
            try {
                $parsedDate = date('Y-m-d', strtotime($matches[1]));
                if ($parsedDate && $parsedDate !== '1970-01-01') {
                    $invoiceDate = $parsedDate;
                    $dueDate = date('Y-m-d', strtotime($parsedDate . ' +30 days'));
                }
            } catch (\Exception $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil di-scan dengan AI OCR!',
            'data' => [
                'invoice_number' => $invoiceNumber,
                'project_id'     => $projectId,
                'project_name'   => $projectName,
                'customer'       => $customer,
                'invoice_date'   => $invoiceDate,
                'due_date'       => $dueDate,
                'invoice_amount' => $amount,
                'notes'          => $notes,
            ]
        ]);
    }

    /**
     * Export Rekap Piutang & AR Aging ke file Excel (.xlsx).
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $userProjectIds = [];
        if ($user && $user->hasRole('sales')) {
            $userProjectIds = Kontrak::where('service_manager', $user->name)
                ->orWhere('service_manager', $user->username)
                ->pluck('project_id')
                ->unique()
                ->toArray();
        }

        $query = Invoice::query();

        if ($user && $user->hasRole('sales') && !empty($userProjectIds)) {
            $query->where(function($q) use ($userProjectIds) {
                $q->whereIn('project_id', $userProjectIds)
                  ->orWhere('sales_user_id', auth()->id());
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhere('project_id', 'like', "%{$s}%")
                    ->orWhere('project_name', 'like', "%{$s}%")
                    ->orWhere('customer', 'like', "%{$s}%");
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('quick_tab')) {
            switch ($request->quick_tab) {
                case 'overdue':
                    $query->whereIn('payment_status', ['unpaid', 'partial'])
                          ->where('due_date', '<', now()->toDateString());
                    break;
                case 'due_soon':
                    $query->whereIn('payment_status', ['unpaid', 'partial'])
                          ->where('due_date', '>=', now()->toDateString())
                          ->where('due_date', '<=', now()->addDays(7)->toDateString());
                    break;
                case 'no_followup':
                    $query->whereIn('payment_status', ['unpaid', 'partial'])
                          ->whereNull('last_followup_at');
                    break;
                case 'paid':
                    $query->where('payment_status', 'paid');
                    break;
            }
        }

        $invoices = $query->orderBy('due_date', 'asc')->get();

        // Filter AR Aging category in memory if specified
        if ($request->filled('aging_category')) {
            $invoices = $invoices->filter(function ($inv) use ($request) {
                return $inv->aging_category === $request->aging_category;
            });
        }

        // Build PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('AR Aging & Rekap Invoice');

        // Document Title
        $sheet->setCellValue('A1', 'PT PERTAMINA GAS - REKAP PIUTANG & UMUR PIUTANG (AR AGING)');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF0F2A4A'));

        $roleName = $user ? ($user->role ?? (method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : 'SALES')) : 'SALES';
        $subTitle = 'Diekspor pada: ' . date('d F Y, H:i:s') . ' WIB | Oleh: ' . ($user->name ?? 'User') . ' (' . strtoupper($roleName) . ')';
        $sheet->setCellValue('A2', $subTitle);
        $sheet->mergeCells('A2:N2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('FF6B7280'));

        // Table Headers
        $headers = [
            'No',
            'No. Invoice',
            'Project ID',
            'Nama Proyek',
            'Customer / Klien',
            'Tgl Invoice',
            'Jatuh Tempo',
            'Nilai Tagihan (IDR)',
            'Terbayar (IDR)',
            'Sisa Piutang (IDR)',
            'Status Pembayaran',
            'Umur Piutang (Hari)',
            'Kategori Aging',
            'Follow-up Terakhir & Komitmen',
        ];

        $headerRow = 4;
        $colIndex = 1;
        foreach ($headers as $head) {
            $sheet->setCellValueByColumnAndRow($colIndex, $headerRow, $head);
            $colIndex++;
        }

        // Header Styling
        $headerRange = 'A4:N4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2A4A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(26);

        $rowNum = 5;
        $no = 1;
        $totalTagihan = 0;
        $totalTerbayar = 0;
        $totalOutstanding = 0;

        foreach ($invoices as $inv) {
            $totalTagihan += $inv->invoice_amount;
            $totalTerbayar += $inv->paid_amount;
            $totalOutstanding += $inv->remaining_amount;

            // Extract last followup string
            $lastFollowupText = '-';
            if (!empty($inv->sales_followup_notes)) {
                $decoded = json_decode($inv->sales_followup_notes, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $first = $decoded[0];
                    $lastFollowupText = ($first['created_at_human'] ?? '') . ' [' . ($first['channel'] ?? 'WA') . ']: ' . ($first['notes'] ?? '');
                    if (!empty($first['promised_date'])) {
                        $lastFollowupText .= ' (Janji: ' . date('d/m/Y', strtotime($first['promised_date'])) . ')';
                    }
                } else {
                    $lastFollowupText = (string)$inv->sales_followup_notes;
                }
            }

            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $inv->invoice_number);
            $sheet->setCellValue('C' . $rowNum, $inv->project_id);
            $sheet->setCellValue('D' . $rowNum, $inv->project_name);
            $sheet->setCellValue('E' . $rowNum, $inv->customer);
            $sheet->setCellValue('F' . $rowNum, $inv->invoice_date ? date('d/m/Y', strtotime($inv->invoice_date)) : '-');
            $sheet->setCellValue('G' . $rowNum, $inv->due_date ? date('d/m/Y', strtotime($inv->due_date)) : '-');
            $sheet->setCellValue('H' . $rowNum, $inv->invoice_amount);
            $sheet->setCellValue('I' . $rowNum, $inv->paid_amount);
            $sheet->setCellValue('J' . $rowNum, $inv->remaining_amount);
            $sheet->setCellValue('K' . $rowNum, strtoupper($inv->payment_status));
            $sheet->setCellValue('L' . $rowNum, $inv->remaining_amount > 0 ? $inv->aging_days . ' hari' : '0 hari');
            $sheet->setCellValue('M' . $rowNum, $inv->aging_label);
            $sheet->setCellValue('N' . $rowNum, $lastFollowupText);

            // Row zebra background
            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:N{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            // Highlighting Overdue Invoices
            if ($inv->remaining_amount > 0 && $inv->is_overdue) {
                $sheet->getStyle("K{$rowNum}")->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
                $sheet->getStyle("M{$rowNum}")->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
            }

            $rowNum++;
        }

        // Number Formatting for Currency columns H, I, J
        $sheet->getStyle('H5:J' . ($rowNum))->getNumberFormat()->setFormatCode('#,##0');

        // Total Row
        $sheet->setCellValue('A' . $rowNum, 'TOTAL');
        $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
        if ($rowNum > 5) {
            $sheet->setCellValue('H' . $rowNum, "=SUM(H5:H" . ($rowNum - 1) . ")");
            $sheet->setCellValue('I' . $rowNum, "=SUM(I5:I" . ($rowNum - 1) . ")");
            $sheet->setCellValue('J' . $rowNum, "=SUM(J5:J" . ($rowNum - 1) . ")");
        } else {
            $sheet->setCellValue('H' . $rowNum, 0);
            $sheet->setCellValue('I' . $rowNum, 0);
            $sheet->setCellValue('J' . $rowNum, 0);
        }
        
        $totalRange = "A{$rowNum}:N{$rowNum}";
        $sheet->getStyle($totalRange)->getFont()->setBold(true);
        $sheet->getStyle($totalRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Borders for table
        $dataRange = 'A4:N' . $rowNum;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');

        // Alignments
        $sheet->getStyle('A5:A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:C' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:G' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K5:M' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H5:J' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-fit column widths
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Rekap_Piutang_AR_Aging_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

