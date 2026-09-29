<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\MasterVendor;
use App\Models\Kontrak;
use App\Models\Realisasi;
use App\Models\Basto;
use App\Models\Prognosa;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:procurement,admin');
    }

    /**
     * Dashboard Pengadaan (Procurement Command Hub).
     */
    public function dashboard()
    {
        // 1. Metrik Komitmen PO Pengadaan
        $totalPoCommitment = (float) PurchaseOrder::sum('po_amount');
        $totalPoIssued     = PurchaseOrder::count();
        $totalPoActive     = PurchaseOrder::whereIn('status', ['issued', 'in_progress'])->count();
        $totalPoCompleted  = PurchaseOrder::where('status', 'completed')->count();

        // 2. Realisasi Belanja Pengadaan dari Seluruh Vendor
        $totalActualSpend  = (float) Realisasi::sum('realisasi_biaya_final');
        $totalVendorsCount = Realisasi::distinct()->whereNotNull('vendor')->where('vendor', '!=', '')->count('vendor');

        // 3. Status BASTO Rekanan (Kesiapan Syarat Pembayaran)
        $approvedBastoCount = Basto::where('status', 'approved')->count();
        $pendingBastoCount  = Basto::whereIn('status', ['submitted', 'draft'])->count();

        // 4. Tagihan Vendor Menunggu Verifikasi
        $pendingInvoicesCount = Realisasi::whereIn('status', ['WAIT INV', 'PROSES', 'UNPAID'])->count();
        $pendingInvoicesAmount = (float) Realisasi::whereIn('status', ['WAIT INV', 'PROSES', 'UNPAID'])->sum('realisasi_biaya_final');

        // 5. Top 5 Vendor Berdasarkan Akumulasi Nilai Kontrak / Realisasi
        $topVendors = Realisasi::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->select('vendor', DB::raw('SUM(realisasi_biaya_final) as total_spend'), DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('vendor')
            ->orderBy('total_spend', 'desc')
            ->limit(5)
            ->get();

        // 6. Daftar PO Terkini
        $recentOrders = PurchaseOrder::orderBy('id', 'desc')->limit(6)->get();

        return view('procurement.dashboard', compact(
            'totalPoCommitment', 'totalPoIssued', 'totalPoActive', 'totalPoCompleted',
            'totalActualSpend', 'totalVendorsCount', 'approvedBastoCount', 'pendingBastoCount',
            'pendingInvoicesCount', 'pendingInvoicesAmount', 'topVendors', 'recentOrders'
        ));
    }

    /**
     * Direktori & Manajemen Rekanan / Vendor.
     */
    public function vendors(Request $request)
    {
        $search = $request->input('search');

        $query = Realisasi::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->select(
                'vendor',
                DB::raw('COUNT(DISTINCT project_id) as total_projects'),
                DB::raw('SUM(realisasi_biaya_final) as total_spend'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('MAX(tahun) as last_active_year')
            )
            ->groupBy('vendor');

        if ($search) {
            $query->where('vendor', 'like', "%{$search}%");
        }

        $vendors = $query->orderBy('total_spend', 'desc')->paginate(15)->withQueryString();

        return view('procurement.vendors', compact('vendors', 'search'));
    }

    /**
     * Daftar & Manajemen Surat Pesanan (PO / SPK Pengadaan).
     */
    public function orders(Request $request)
    {
        $filters = $request->only(['search', 'status', 'project_id', 'vendor']);

        $query = PurchaseOrder::query();

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function($q) use ($s) {
                $q->where('po_number', 'like', "%{$s}%")
                  ->orWhere('po_title', 'like', "%{$s}%")
                  ->orWhere('vendor_name', 'like', "%{$s}%")
                  ->orWhere('project_id', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['vendor'])) {
            $query->where('vendor_name', $filters['vendor']);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Data pendukung untuk modal pembuatan PO baru
        $projects = Kontrak::orderBy('project_id')->get(['project_id', 'project_name']);
        $vendors = Realisasi::distinct()->whereNotNull('vendor')->where('vendor', '!=', '')->orderBy('vendor')->pluck('vendor');

        return view('procurement.orders', compact('orders', 'filters', 'projects', 'vendors'));
    }

    /**
     * Simpan Penerbitan PO / SPK Pengadaan Baru & Otomatis Sinkronisasi ke Prognosa Bulanan.
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'po_number'         => 'required|string|unique:purchase_orders,po_number|max:100',
            'project_id'        => 'required|string',
            'vendor_name'       => 'required|string|max:255',
            'po_title'          => 'required|string|max:255',
            'description'       => 'nullable|string',
            'po_amount'         => 'required|numeric|min:0',
            'term_of_payment'   => 'required|integer|min:1|max:365',
            'order_date'        => 'required|date',
            'delivery_deadline' => 'nullable|date|after_or_equal:order_date',
            'contract_file'     => 'nullable|file|mimes:pdf|max:10240',
            'notes'             => 'nullable|string',
        ], [
            'po_number.unique'       => 'Nomor PO / SPK Pengadaan sudah terdaftar.',
            'po_amount.min'          => 'Nilai PO tidak boleh negatif.',
            'term_of_payment.min'    => 'Termin pembayaran minimal 1 hari.',
            'contract_file.max'      => 'File kontrak PDF maksimal 10 MB.',
        ]);

        $project = Kontrak::where('project_id', $validated['project_id'])->first();
        $validated['project_name'] = $project ? $project->project_name : $validated['project_id'];
        $validated['created_by_user_id'] = auth()->id();
        $validated['status'] = 'issued';

        // Hitung estimasi tanggal jatuh tempo pembayaran & bulan penagihan kas prognosa
        $orderDate = \Carbon\Carbon::parse($validated['order_date']);
        $topDays = (int) $validated['term_of_payment'];
        $dueDate = $orderDate->copy()->addDays($topDays);

        $indoMonths = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];

        $prognosaBulan = $indoMonths[$dueDate->month] ?? 'JANUARI';
        $prognosaTahun = (int) $dueDate->year;

        $validated['payment_due_date'] = $dueDate->format('Y-m-d');
        $validated['prognosa_periode'] = $prognosaBulan;
        $validated['prognosa_tahun']   = $prognosaTahun;

        if ($request->hasFile('contract_file')) {
            $file = $request->file('contract_file');
            $filename = 'PO_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('purchase_orders', $filename, 'public');
            $validated['contract_file'] = $path;
        }

        $po = PurchaseOrder::create($validated);

        // Otomatis sinkronisasi proyeksi penagihan ke tabel Prognosa dengan data_source = 'procurement_po'
        Prognosa::create([
            'project_id'       => $po->project_id,
            'project_name'     => $po->project_name,
            'activity'         => $po->po_title,
            'satuan_kerja'     => 'PENGADAAN',
            'pic'              => auth()->user()->name,
            'prognosa_biaya'   => $po->po_amount,
            'periode'          => $prognosaBulan,
            'partner'          => $po->vendor_name,
            'keterangan'       => "Prognosa Berbasis PO #{$po->po_number} (Termin TOP {$topDays} Hari)",
            'tahun_original'   => $prognosaTahun,
            'tahun_normalized' => $prognosaTahun,
            'data_source'      => 'procurement_po',
            'po_id'            => $po->id,
            'is_overridden'    => false,
        ]);

        // Auto-sinkronisasi nomor PO & vendor ke Master Kontrak jika masih kosong
        Kontrak::where('project_id', $po->project_id)
            ->where(function ($q) {
                $q->whereNull('contract_number')->orWhere('contract_number', '');
            })
            ->update(['contract_number' => $po->po_number]);

        Kontrak::where('project_id', $po->project_id)
            ->where(function ($q) {
                $q->whereNull('resource_management')->orWhere('resource_management', '');
            })
            ->update(['resource_management' => $po->vendor_name]);

        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'CREATE',
                'module'      => 'procurement',
                'record_id'   => (string) $po->id,
                'description' => "Pengadaan (Reza) menerbitkan PO #{$po->po_number} senilai Rp " . number_format($po->po_amount, 0, ',', '.') . " dengan TOP {$topDays} hari (Jatuh tempo {$prognosaBulan} {$prognosaTahun})",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        return redirect()->route('procurement.orders')
            ->with('success', "PO [{$po->po_number}] berhasil diterbitkan dengan termin {$topDays} hari. Estimasi tagihan otomatis masuk ke Prognosa {$prognosaBulan} {$prognosaTahun}.");
    }

    /**
     * Verifikasi 3-Way Match (PO Pengadaan + BASTO Lolos QC + Tagihan Vendor).
     */
    public function verification(Request $request)
    {
        // Ambil data BASTO yang telah disetujui (Approved) sebagai acuan verifikasi sah pengadaan
        $approvedBastos = Basto::with(['sm', 'qcUser'])
            ->where('status', 'approved')
            ->orderBy('reviewed_at', 'desc')
            ->get();

        // Ambil PO yang aktif
        $activeOrders = PurchaseOrder::whereIn('status', ['issued', 'in_progress', 'basto_verified'])
            ->orderBy('id', 'desc')
            ->get();

        // Tagihan vendor yang sedang berstatus menunggu verifikasi
        $pendingInvoices = Realisasi::whereIn('status', ['WAIT INV', 'PROSES', 'UNPAID'])
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('procurement.verification', compact('approvedBastos', 'activeOrders', 'pendingInvoices'));
    }

    /**
     * Aksi Konfirmasi Verifikasi PO (3-Way Match Complete).
     */
    public function completeVerification($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'completed']);

        return back()->with('success', "PO [{$po->po_number}] telah terverifikasi 3-Way Match secara sah.");
    }
}
