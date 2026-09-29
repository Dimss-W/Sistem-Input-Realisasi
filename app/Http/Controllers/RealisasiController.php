<?php

namespace App\Http\Controllers;

use App\Models\Realisasi;
use App\Models\RealisasiLog;
use App\Models\Kontrak;
use App\Services\RealisasiImportService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RealisasiController extends Controller
{
    use LogsActivity;

    protected RealisasiImportService $importService;

    protected array $periodeList = [
        'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
        'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER',
    ];

    protected array $currencyList = ['IDR', 'USD', 'EUR', 'SGD', 'JPY'];

    protected array $statusList = ['PAID', 'UNPAID', 'PENDING', 'CANCEL', 'PROSES'];

    protected array $sifatList = ['MONTHLY', 'YEARLY', 'ONE-TIME', 'WEEKLY'];

    public function __construct(RealisasiImportService $importService)
    {
        $this->importService = $importService;
        $this->middleware('role:dmo,admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    // =========================================================================
    // INDEX — Halaman daftar realisasi
    // =========================================================================
    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'project_id', 'project_name', 'pic',
            'periode', 'status', 'vendor', 'tahun',
            'satuan_kerja', 'item_biaya', 'service_manager', 'basto_status',
        ]);

        $isSM = auth()->check() && auth()->user()->hasRole('osm_service_manager');

        // Data ID Proyek yang sudah memiliki berkas BASTO
        $projectsWithBasto = \App\Models\Basto::distinct()->pluck('project_id')->toArray();

        $query = Realisasi::with('kontrak');

        if ($isSM) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $smProjects = \App\Models\Kontrak::whereIn('service_manager', $smNames)->pluck('project_id');
            $query->whereIn('project_id', $smProjects);
        }

        // Filter Pelacak BASTO
        if (!empty($filters['basto_status'])) {
            if ($filters['basto_status'] === 'has_basto') {
                $query->whereIn('project_id', $projectsWithBasto);
            } elseif ($filters['basto_status'] === 'no_basto') {
                $query->whereNotIn('project_id', $projectsWithBasto);
            }
        }

        $query->filter($filters)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode,
                'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
                'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
            ) DESC")
            ->orderBy('project_id');

        $realisasi  = $query->paginate(25)->withQueryString();

        // Calculate totals with the same filters
        $totalsQuery = Realisasi::query();
        if ($isSM) {
            $totalsQuery->whereIn('project_id', $smProjects);
        }
        if (!empty($filters['basto_status'])) {
            if ($filters['basto_status'] === 'has_basto') {
                $totalsQuery->whereIn('project_id', $projectsWithBasto);
            } elseif ($filters['basto_status'] === 'no_basto') {
                $totalsQuery->whereNotIn('project_id', $projectsWithBasto);
            }
        }
        $totalCount = $totalsQuery->filter($filters)->count();
        $totalIDR   = $totalsQuery->filter($filters)->sum('realisasi_biaya_final');

        // Dropdown options
        if ($isSM) {
            $projectIds = Realisasi::whereIn('project_id', $smProjects)->distinct()->orderBy('project_id')->whereNotNull('project_id')->where('project_id', '!=', '')->pluck('project_id');
            $picList    = Realisasi::whereIn('project_id', $smProjects)->distinct()->orderBy('pic')->whereNotNull('pic')->where('pic', '!=', '')->pluck('pic');
            $vendorList = Realisasi::whereIn('project_id', $smProjects)->distinct()->orderBy('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->pluck('vendor');
            $tahunList  = Realisasi::whereIn('project_id', $smProjects)->distinct()->orderBy('tahun', 'desc')->whereNotNull('tahun')->pluck('tahun');
            $smList     = \App\Models\Kontrak::whereIn('service_manager', $smNames)->distinct()->orderBy('service_manager')->whereNotNull('service_manager')->where('service_manager', '!=', '')->pluck('service_manager');
        } else {
            $projectIds  = Realisasi::distinct()->orderBy('project_id')->whereNotNull('project_id')->where('project_id', '!=', '')->pluck('project_id');
            $picList     = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->where('pic', '!=', '')->pluck('pic');
            $vendorList  = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->pluck('vendor');
            $tahunList   = Realisasi::distinct()->orderBy('tahun', 'desc')->whereNotNull('tahun')->pluck('tahun');
            $smList      = \App\Models\Kontrak::whereNotNull('service_manager')->where('service_manager', '!=', '')->distinct()->orderBy('service_manager')->pluck('service_manager');
        }

        $dbStatuses = Realisasi::distinct()->orderBy('status')->whereNotNull('status')->where('status', '!=', '')->pluck('status')->toArray();
        $defaultStatuses = ['PAID', 'UNPAID', 'PENDING', 'CANCEL', 'PROSES', 'POPAY', 'WAIT INV', 'IN PROGRES'];
        $statusList = collect(array_unique(array_merge($defaultStatuses, $dbStatuses)))->values();

        $projectTotals = Realisasi::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total_realisasi'))
            ->pluck('total_realisasi', 'project_id');

        return view('realisasi.index', compact(
            'realisasi', 'filters', 'totalCount', 'totalIDR',
            'projectIds', 'picList', 'vendorList', 'tahunList', 'smList', 'statusList',
            'projectTotals', 'projectsWithBasto'
        ));
    }
    // =========================================================================
    // CREATE — Form tambah data baru
    // =========================================================================
    public function create()
    {
        $periodeList  = $this->periodeList;
        $currencyList = $this->currencyList;
        $dbStatuses   = Realisasi::distinct()->whereNotNull('status')->where('status', '!=', '')->pluck('status')->toArray();
        $statusList   = array_values(array_unique(array_merge($this->statusList, $dbStatuses)));
        sort($statusList);
        $sifatList    = $this->sifatList;

        $fxService    = new \App\Services\ExchangeRateService();
        $fxData       = $fxService->getLiveRates();

        // Suggestion lists untuk autocomplete
        $projectIds    = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
        $projectNames  = Realisasi::distinct()->orderBy('project_name')->pluck('project_name', 'project_name');
        $picList       = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
        $vendorList    = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
        $satuanList    = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
        $itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');
        $smList        = \App\Models\Kontrak::whereNotNull('service_manager')->where('service_manager', '!=', '')->distinct()->orderBy('service_manager')->pluck('service_manager', 'service_manager');
        
        // Agregasi realisasi dan master data kontrak untuk Smart Auto-fill & Budget Guard
        $projectRealisasiSums = Realisasi::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total_spent'))
            ->pluck('total_spent', 'project_id');

        $kontrakRaw = \App\Models\Kontrak::select('project_id', 'project_name', 'service_manager', 'project_client', 'project_value', 'costbased')->get();

        $kontrakMap = $kontrakRaw->mapWithKeys(function ($k) use ($projectRealisasiSums) {
            $spent = (float) ($projectRealisasiSums[$k->project_id] ?? 0);
            $val = (float) ($k->project_value ?? 0);
            $remaining = max(0, $val - $spent);
            $burnPct = $val > 0 ? round(($spent / $val) * 100, 1) : 0;
            return [
                $k->project_id => [
                    'project_id'       => $k->project_id,
                    'project_name'     => $k->project_name,
                    'service_manager'  => $k->service_manager,
                    'project_client'   => $k->project_client ?? '—',
                    'project_value'    => $val,
                    'total_realisasi'  => $spent,
                    'remaining_budget' => $remaining,
                    'burn_rate'        => $burnPct,
                ]
            ];
        });

        return view('realisasi.create', compact(
            'periodeList', 'currencyList', 'statusList', 'sifatList',
            'projectIds', 'projectNames', 'picList', 'vendorList',
            'satuanList', 'itemBiayaList', 'smList', 'kontrakMap', 'fxData'
        ));
    }

    public function getExchangeRates(Request $request, \App\Services\ExchangeRateService $fxService)
    {
        $forceRefresh = $request->boolean('refresh');
        return response()->json($fxService->getLiveRates($forceRefresh));
    }

    // =========================================================================
    // STORE — Simpan data baru
    // =========================================================================
    public function store(Request $request)
    {
        $validated = $this->validateRealisasi($request);

        // Normalisasi
        $validated = $this->normalizeInput($validated);

        // Cek Period Lock
        if (!auth()->user()->hasRole('admin') && \App\Models\PeriodLock::isPeriodLocked($validated['tahun'], $validated['periode'])) {
            return back()
                ->withInput()
                ->withErrors(['periode' => "Periode {$validated['periode']} {$validated['tahun']} telah DIKUNCI oleh Administrator. Data baru tidak dapat ditambahkan pada periode ini."]);
        }

        // Generate record_key
        $recordKey = Realisasi::generateRecordKey($validated);

        // Cek duplikasi spesifik kombinasi (project_id, item_biaya, vendor, periode, tahun)
        $duplicateCheck = Realisasi::where('project_id', $validated['project_id'])
            ->where('item_biaya', $validated['item_biaya'])
            ->where('vendor', $validated['vendor'])
            ->where('periode', $validated['periode'])
            ->where('tahun', $validated['tahun'])
            ->first();
        if ($duplicateCheck) {
            return back()
                ->withInput()
                ->withErrors(['project_id' => "Duplikasi terdeteksi: Realisasi untuk Proyek {$validated['project_id']}, item '{$validated['item_biaya']}', vendor '{$validated['vendor']}' pada periode {$validated['periode']} {$validated['tahun']} sudah ada di sistem (ID: #{$duplicateCheck->id}). Gunakan fitur edit untuk memperbarui data."]);
        }

        // Cek duplicate record_key
        $existing = Realisasi::where('record_key', $recordKey)->first();
        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['record_key' => 'Data dengan kombinasi yang sama (Project ID, Item Biaya, PIC, Periode, Vendor, Sifat, Tahun) sudah ada. ID: #' . $existing->id . '. Gunakan fitur Edit jika ingin memperbarui.']);
        }

        $validated['record_key'] = $recordKey;

        // Cek potensi over-budget terhadap kontrak
        $kontrak = \App\Models\Kontrak::where('project_id', $validated['project_id'])->first();
        $warningMessage = null;
        if ($kontrak && $kontrak->project_value > 0) {
            $existingTotal = Realisasi::where('project_id', $validated['project_id'])->sum('realisasi_biaya_final');
            $newTotal = $existingTotal + ($validated['realisasi_biaya_final'] ?? 0);
            if ($newTotal > $kontrak->project_value) {
                $selisih = $newTotal - $kontrak->project_value;
                $warningMessage = "Perhatian: Akumulasi realisasi proyek {$validated['project_id']} kini mencapai Rp " . number_format($newTotal, 0, ',', '.') . ", melebihi nilai kontrak sebesar Rp " . number_format($selisih, 0, ',', '.') . " (Overbudget).";
            }
        }

        // Auto-sinkronisasi status pembayaran jika invoice sudah lunas
        $matchedInvoice = \App\Models\Invoice::where('project_id', $validated['project_id'])->latest()->first();
        if ($matchedInvoice && $matchedInvoice->payment_status === 'paid') {
            $validated['status'] = 'PAID';
        }

        // Pastikan item_biaya memiliki fallback informatif agar tidak kosong/strip
        if (empty($validated['item_biaya'])) {
            $validated['item_biaya'] = 'Pengadaan ' . ($validated['project_name'] ?? $validated['project_id']);
        }

        $realisasi = Realisasi::create($validated);

        if ($request->has('service_manager') && !empty($request->service_manager) && !empty($validated['project_id'])) {
            \App\Models\Kontrak::updateOrCreate(
                ['project_id' => strtoupper(trim($validated['project_id']))],
                ['service_manager' => strtoupper(trim($request->service_manager))]
            );
        }

        RealisasiLog::record(
            $realisasi->id, 'CREATE',
            null, $realisasi->toArray(),
            null, $request->ip()
        );

        $this->logActivity('CREATE', 'realisasi', (string) $realisasi->id, null, $realisasi->toArray(), 'Tambah data realisasi baru');

        $redirect = redirect()->route('realisasi.index')
            ->with('success', "Data realisasi #{$realisasi->id} berhasil ditambahkan.");

        if ($warningMessage) {
            $redirect->with('warning', $warningMessage);
        }

        return $redirect;
    }

    // =========================================================================
    // SHOW — Detail data
    // =========================================================================
    public function show(string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $logs         = RealisasiLog::where('realisasi_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
        $activityLogs = \App\Models\ActivityLog::where('module', 'realisasi')
            ->where('record_id', (string)$id)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        // Project contract & workflow components
        $kontrak = \App\Models\Kontrak::where('project_id', $realisasi->project_id)->first();
        $basto = \App\Models\Basto::where('project_id', $realisasi->project_id)->latest()->first();
        $invoice = \App\Models\Invoice::where('project_id', $realisasi->project_id)->latest()->first();

        // Total akumulasi realisasi proyek
        $projectTotalRealisasi = Realisasi::where('project_id', $realisasi->project_id)->sum('realisasi_biaya_final');

        return view('realisasi.show', compact(
            'realisasi', 'logs', 'activityLogs', 'kontrak', 'basto', 'invoice', 'projectTotalRealisasi'
        ));
    }

    // =========================================================================
    // EDIT — Form edit data lama
    // =========================================================================
    public function edit(string $id)
    {
        $realisasi    = Realisasi::findOrFail($id);

        // Cek Period Lock
        if (!auth()->user()->hasRole('admin') && \App\Models\PeriodLock::isPeriodLocked($realisasi->tahun, $realisasi->periode)) {
            return redirect()->route('realisasi.show', $id)
                ->with('error', "Periode {$realisasi->periode} {$realisasi->tahun} telah DIKUNCI oleh Administrator. Data transaksi ini tidak dapat diedit.");
        }

        $periodeList  = $this->periodeList;
        $currencyList = $this->currencyList;
        $dbStatuses   = Realisasi::distinct()->whereNotNull('status')->where('status', '!=', '')->pluck('status')->toArray();
        $statusList   = array_values(array_unique(array_merge($this->statusList, $dbStatuses)));
        sort($statusList);
        $sifatList    = $this->sifatList;

        $fxService    = new \App\Services\ExchangeRateService();
        $fxData       = $fxService->getLiveRates();

        $projectIds    = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
        $projectNames  = Realisasi::distinct()->orderBy('project_name')->pluck('project_name', 'project_name');
        $picList       = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
        $vendorList    = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
        $satuanList    = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
        $itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');
        $smList        = \App\Models\Kontrak::whereNotNull('service_manager')->where('service_manager', '!=', '')->distinct()->orderBy('service_manager')->pluck('service_manager', 'service_manager');
        
        // Agregasi realisasi dan master data kontrak untuk Smart Auto-fill & Budget Guard
        $projectRealisasiSums = Realisasi::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total_spent'))
            ->pluck('total_spent', 'project_id');

        $kontrakRaw = \App\Models\Kontrak::select('project_id', 'project_name', 'service_manager', 'project_client', 'project_value', 'costbased')->get();

        $kontrakMap = $kontrakRaw->mapWithKeys(function ($k) use ($projectRealisasiSums) {
            $spent = (float) ($projectRealisasiSums[$k->project_id] ?? 0);
            $val = (float) ($k->project_value ?? 0);
            $remaining = max(0, $val - $spent);
            $burnPct = $val > 0 ? round(($spent / $val) * 100, 1) : 0;
            return [
                $k->project_id => [
                    'project_id'       => $k->project_id,
                    'project_name'     => $k->project_name,
                    'service_manager'  => $k->service_manager,
                    'project_client'   => $k->project_client ?? '—',
                    'project_value'    => $val,
                    'total_realisasi'  => $spent,
                    'remaining_budget' => $remaining,
                    'burn_rate'        => $burnPct,
                ]
            ];
        });

        return view('realisasi.edit', compact(
            'realisasi', 'periodeList', 'currencyList', 'statusList', 'sifatList',
            'projectIds', 'projectNames', 'picList', 'vendorList', 'satuanList', 'itemBiayaList',
            'smList', 'kontrakMap', 'fxData'
        ));
    }

    // =========================================================================
    // UPDATE — Update data LAMA (BUKAN insert baru!)
    // =========================================================================
    public function update(Request $request, string $id)
    {
        $realisasi = Realisasi::findOrFail($id);

        // Cek Period Lock data lama
        if (!auth()->user()->hasRole('admin') && \App\Models\PeriodLock::isPeriodLocked($realisasi->tahun, $realisasi->periode)) {
            return redirect()->route('realisasi.show', $id)
                ->with('error', "Periode {$realisasi->periode} {$realisasi->tahun} telah DIKUNCI oleh Administrator. Data ini tidak dapat diubah.");
        }

        $oldData   = $realisasi->toArray();

        $validated = $this->validateRealisasi($request, $id);

        // Normalisasi
        $validated = $this->normalizeInput($validated);

        // Cek Period Lock data baru (jika periode/tahun dipindah)
        if (!auth()->user()->hasRole('admin') && \App\Models\PeriodLock::isPeriodLocked($validated['tahun'], $validated['periode'])) {
            return back()
                ->withInput()
                ->withErrors(['periode' => "Periode target {$validated['periode']} {$validated['tahun']} telah DIKUNCI oleh Administrator."]);
        }

        // Hitung record_key baru
        $newRecordKey = Realisasi::generateRecordKey($validated);

        // Cek jika record_key berubah dan sudah ada di record lain
        if ($newRecordKey !== $realisasi->record_key) {
            $conflicting = Realisasi::where('record_key', $newRecordKey)
                ->where('id', '!=', $id)
                ->first();

            if ($conflicting) {
                return back()
                    ->withInput()
                    ->withErrors(['record_key' => 'Kombinasi data yang diubah sudah dimiliki record lain (ID: #' . $conflicting->id . '). Periksa kembali data Anda.']);
            }
        }

        $validated['record_key'] = $newRecordKey;

        // Cek potensi over-budget terhadap kontrak
        $kontrak = \App\Models\Kontrak::where('project_id', $validated['project_id'])->first();
        $warningMessage = null;
        if ($kontrak && $kontrak->project_value > 0) {
            $existingTotal = Realisasi::where('project_id', $validated['project_id'])->where('id', '!=', $id)->sum('realisasi_biaya_final');
            $newTotal = $existingTotal + ($validated['realisasi_biaya_final'] ?? 0);
            if ($newTotal > $kontrak->project_value) {
                $selisih = $newTotal - $kontrak->project_value;
                $warningMessage = "Perhatian: Akumulasi realisasi proyek {$validated['project_id']} kini mencapai Rp " . number_format($newTotal, 0, ',', '.') . ", melebihi nilai kontrak sebesar Rp " . number_format($selisih, 0, ',', '.') . " (Overbudget).";
            }
        }

        // UPDATE — ID tetap sama, BUKAN insert baru
        $realisasi->update($validated);

        if ($request->has('service_manager') && !empty($request->service_manager) && !empty($validated['project_id'])) {
            \App\Models\Kontrak::updateOrCreate(
                ['project_id' => strtoupper(trim($validated['project_id']))],
                ['service_manager' => strtoupper(trim($request->service_manager))]
            );
        }

        RealisasiLog::record(
            $realisasi->id, 'UPDATE',
            $oldData, $realisasi->toArray(),
            null, $request->ip()
        );

        $this->logActivity('UPDATE', 'realisasi', (string) $realisasi->id, $oldData, $realisasi->toArray(), 'Edit data realisasi');

        $redirect = redirect()->route('realisasi.index')
            ->with('success', "Data realisasi #{$realisasi->id} berhasil diperbarui.");

        if ($warningMessage) {
            $redirect->with('warning', $warningMessage);
        }

        return $redirect;
    }

    // =========================================================================
    // DESTROY — Hapus data
    // =========================================================================
    public function destroy(Request $request, string $id)
    {
        $realisasi = Realisasi::findOrFail($id);

        // Cek Period Lock
        if (!auth()->user()->hasRole('admin') && \App\Models\PeriodLock::isPeriodLocked($realisasi->tahun, $realisasi->periode)) {
            return redirect()->route('realisasi.show', $id)
                ->with('error', "Periode {$realisasi->periode} {$realisasi->tahun} telah DIKUNCI oleh Administrator. Data ini tidak dapat dihapus.");
        }

        $oldData   = $realisasi->toArray();
        $projectId = $realisasi->project_id;

        RealisasiLog::record(
            $realisasi->id, 'DELETE',
            $oldData, null,
            null, $request->ip()
        );

        $this->logActivity('DELETE', 'realisasi', (string) $id, $oldData, null, 'Hapus data realisasi');

        $realisasi->delete();

        // Auto-cleanup master kontrak jika sudah tidak ada sisa transaksi untuk proyek ini
        if ($projectId) {
            $hasRemainingRealisasi = Realisasi::where('project_id', $projectId)->exists();
            $hasBasto             = \App\Models\Basto::where('project_id', $projectId)->exists();
            $hasInvoice           = \App\Models\Invoice::where('project_id', $projectId)->exists();

            if (!$hasRemainingRealisasi && !$hasBasto && !$hasInvoice) {
                \App\Models\Kontrak::where('project_id', $projectId)->delete();
            }
        }

        return redirect()->route('realisasi.index')
            ->with('success', "Data realisasi #{$id} berhasil dihapus.");
    }

    // =========================================================================
    // IMPORT FORM — Halaman upload Excel
    // =========================================================================
    public function importForm()
    {
        return view('realisasi.import');
    }

    // =========================================================================
    // IMPORT — Proses upload dan UPSERT Excel
    // =========================================================================
    public function import(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240', // 10MB max
            ],
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $file     = $request->file('file');
        $filePath = $file->store('imports', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $summary = $this->importService->import(
            $fullPath,
            null,
            $request->ip()
        );

        // Hapus file temporary
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return redirect()->route('realisasi.import.form')
            ->with('import_summary', $summary);
    }

    // =========================================================================
    // EXPORT — Export ke Excel sesuai filter aktif
    // =========================================================================
    public function export(Request $request)
    {
        $filters = $request->only([
            'search', 'project_id', 'project_name', 'pic',
            'periode', 'status', 'vendor', 'tahun',
            'satuan_kerja', 'item_biaya',
        ]);

        $data = Realisasi::filter($filters)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode,
                'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
                'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
            ) DESC")
            ->orderBy('project_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Realisasi');

        // Header styling
        $headers = [
            'No', 'Source Row', 'Project ID', 'Project Name', 'Item Biaya',
            'Satuan Kerja', 'PIC', 'Periode', 'Realisasi Biaya Original',
            'Currency', 'Realisasi Biaya IDR', 'Status', 'Vendor', 'Sifat',
            'Link Evidence', 'Tahun', 'Data Flag', 'Realisasi Biaya Final',
        ];

        foreach ($headers as $colIndex => $header) {
            $col = chr(65 + $colIndex);
            $cell = $col . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Header style
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $lastCol = chr(65 + count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        // Data rows
        foreach ($data as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $values = [
                $rowIndex + 1,
                $row->source_row,
                $row->project_id,
                $row->project_name,
                $row->item_biaya,
                $row->satuan_kerja,
                $row->pic,
                $row->periode,
                $row->realisasi_biaya_original,
                $row->currency,
                $row->realisasi_biaya_idr,
                $row->status,
                $row->vendor,
                $row->sifat,
                $row->link_evidence,
                $row->tahun,
                $row->data_flag,
                $row->realisasi_biaya_final,
            ];

            foreach ($values as $colIndex => $value) {
                $col = chr(65 + $colIndex);
                $sheet->setCellValue("{$col}{$excelRow}", $value);
            }

            // Zebra striping
            if ($rowIndex % 2 === 1) {
                $sheet->getStyle("A{$excelRow}:{$lastCol}{$excelRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F5F8FC');
            }
        }

        // Auto column width
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        $filename = 'Realisasi_Export_' . date('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // DOWNLOAD TEMPLATE — Template Excel kosong untuk import
    // =========================================================================
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Realisasi');

        $headers = [
            'Source_Row', 'Project_ID', 'Project_Name', 'Item_Biaya',
            'Satuan_Kerja', 'PIC', 'Periode', 'Realisasi_Biaya_Original',
            'Currency', 'Realisasi_Biaya_IDR', 'Status', 'Vendor', 'Sifat',
            'Link_Evidence', 'Tahun', 'Data_Flag', 'Realisasi_Biaya_Final',
        ];

        foreach ($headers as $colIndex => $header) {
            $col = chr(65 + $colIndex);
            $sheet->setCellValue("{$col}1", $header);
        }

        $lastCol = chr(65 + count($headers) - 1);

        // Header style
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Contoh data baris pertama
        $example = [
            '1', 'PS-024-00',
            'Pekerjaan Jasa Operasi dan Pemeliharaan Terintegrasi Gas Management System',
            'UPAH', 'DMO', 'ULFA', 'JANUARI',
            '863767114', 'IDR', '863767114', 'PAID', 'PERSADA', 'MONTHLY',
            '', '2026', '', '863767114',
        ];

        foreach ($example as $colIndex => $val) {
            $col = chr(65 + $colIndex);
            $sheet->setCellValue("{$col}2", $val);
        }

        // Style baris contoh
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']],
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Realisasi.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // DASHBOARD — Ringkasan statistik
    // =========================================================================
    public function dashboard(Request $request)
    {
        if (auth()->check() && auth()->user()->hasRole('procurement')) {
            return redirect()->route('procurement.dashboard');
        }

        // === DATA PENGUSAHAAN & PENGAWASAN MUTU (QC) ===
        $totalBastos = \App\Models\Basto::count();
        $qcPendingCount = \App\Models\Basto::where('status', 'submitted')
            ->where(function($q) {
                $q->whereNull('qc_status')->orWhere('qc_status', 'pending');
            })->count();
        $qcRevisionCount = \App\Models\Basto::where('qc_status', 'revision_needed')->count();
        $qcVerifiedCount = \App\Models\Basto::where('qc_status', 'verified')->count();

        $inspectedTotal = $qcVerifiedCount + $qcRevisionCount;
        $passRate = $inspectedTotal > 0 ? round(($qcVerifiedCount / $inspectedTotal) * 100, 1) : 0;

        // Antrean BASTO untuk QC
        $qcQueue = \App\Models\Basto::with(['dmo', 'sm', 'qcUser'])
            ->orderByRaw("CASE 
                WHEN status = 'submitted' AND (qc_status IS NULL OR qc_status = 'pending') THEN 1 
                WHEN qc_status = 'revision_needed' THEN 2 
                ELSE 3 END")
            ->orderBy('updated_at', 'desc')
            ->take(12)
            ->get();

        // Statistik Kepatuhan 4 Kriteria
        $criteriaCompliance = [
            'admin_doc'         => 0,
            'spk_compliance'    => 0,
            'baut_teknis'       => 0,
            'physical_evidence' => 0,
        ];

        $allInspected = \App\Models\Basto::whereNotNull('qc_checklist')->get();
        $totalWithChecklist = $allInspected->count();

        if ($totalWithChecklist > 0) {
            foreach ($allInspected as $ib) {
                $ch = is_array($ib->qc_checklist) ? $ib->qc_checklist : [];
                if (!empty($ch['admin_doc'])) $criteriaCompliance['admin_doc']++;
                if (!empty($ch['spk_compliance'])) $criteriaCompliance['spk_compliance']++;
                if (!empty($ch['baut_teknis'])) $criteriaCompliance['baut_teknis']++;
                if (!empty($ch['physical_evidence'])) $criteriaCompliance['physical_evidence']++;
            }
        }

        // Temuan / Aktivitas Terkini
        $recentInspections = \App\Models\Basto::with(['qcUser', 'dmo'])
            ->whereNotNull('qc_status')
            ->whereNotNull('qc_verified_at')
            ->orderBy('qc_verified_at', 'desc')
            ->take(5)
            ->get();

        $currentYear  = date('Y');
        $currentMonth = strtoupper(now()->locale('id')->translatedFormat('F'));

        $monthMap = [
            'JANUARY' => 'JANUARI', 'FEBRUARY' => 'FEBRUARI', 'MARCH' => 'MARET',
            'APRIL'   => 'APRIL',   'MAY'      => 'MEI',       'JUNE'  => 'JUNI',
            'JULY'    => 'JULI',    'AUGUST'   => 'AGUSTUS',   'SEPTEMBER' => 'SEPTEMBER',
            'OCTOBER' => 'OKTOBER', 'NOVEMBER' => 'NOVEMBER',  'DECEMBER' => 'DESEMBER',
        ];
        $currentMonth = $monthMap[$currentMonth] ?? $currentMonth;

        // === FILTER PARAMS ===
        $filterProject  = $request->input('project_name');
        $filterSM       = $request->input('service_manager');
        $filterClient   = $request->input('client');
        $filterPeriode  = $request->input('periode');
        $filterTahun    = $request->input('tahun');

        $tahunList = Realisasi::distinct()->whereNotNull('tahun')->where('tahun', '!=', '')->orderBy('tahun', 'desc')->pluck('tahun');
        if ($tahunList->isEmpty()) {
            $tahunList = collect(['2026']);
        }

        $isSMUser = auth()->check() && auth()->user()->hasRole('osm_service_manager');

        // === KONTRAK QUERY ===
        $kontrakQuery = \App\Models\Kontrak::query();
        if ($filterProject) {
            $kontrakQuery->where('project_name', 'like', "%{$filterProject}%");
        }
        if ($filterClient) {
            $kontrakQuery->where('project_client', $filterClient);
        }

        // Mapping Kuartal & Semester
        $quarterMap = [
            'Q1' => ['JANUARI', 'FEBRUARI', 'MARET'],
            'Q2' => ['APRIL', 'MEI', 'JUNI'],
            'Q3' => ['JULI', 'AGUSTUS', 'SEPTEMBER'],
            'Q4' => ['OKTOBER', 'NOVEMBER', 'DESEMBER'],
            'SEMESTER 1' => ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI'],
            'SEMESTER 2' => ['JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'],
        ];

        // === REALISASI QUERY ===
        $realisasiQuery = Realisasi::query();
        if ($filterProject) {
            $realisasiQuery->where('realisasi.project_name', 'like', "%{$filterProject}%");
        }
        if ($filterClient) {
            $realisasiQuery->where('realisasi.vendor', $filterClient);
        }
        if ($filterPeriode) {
            $upperP = strtoupper(trim($filterPeriode));
            if (isset($quarterMap[$upperP])) {
                $realisasiQuery->whereIn('realisasi.periode', $quarterMap[$upperP]);
            } else {
                $realisasiQuery->where('realisasi.periode', $filterPeriode);
            }
        }
        if ($filterTahun) {
            $realisasiQuery->where('realisasi.tahun', $filterTahun);
        }

        // === PROGNOSA QUERY ===
        $prognosaQuery = \App\Models\Prognosa::query();
        if ($filterProject) {
            $prognosaQuery->where('project_name', 'like', "%{$filterProject}%");
        }
        if ($filterPeriode) {
            $upperP = strtoupper(trim($filterPeriode));
            if (isset($quarterMap[$upperP])) {
                $prognosaQuery->whereIn('periode', $quarterMap[$upperP]);
            } else {
                $prognosaQuery->where('periode', $filterPeriode);
            }
        }
        if ($filterTahun) {
            $prognosaQuery->where('tahun_normalized', $filterTahun);
        }

        // === RESOLVE SERVICE MANAGER / PIC FILTER ===
        if ($isSMUser) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $kontrakQuery->whereIn('service_manager', $smNames);
            $smProjects = \App\Models\Kontrak::whereIn('service_manager', $smNames)->pluck('project_id');
            $realisasiQuery->whereIn('realisasi.project_id', $smProjects);
            $prognosaQuery->whereIn('project_id', $smProjects);
            $filterSM = auth()->user()->name;
        } else {
            if ($filterSM) {
                $isSM = \App\Models\Kontrak::where('service_manager', $filterSM)->exists();
                $isPIC = Realisasi::where('pic', $filterSM)->exists();

                if ($isSM) {
                    $kontrakQuery->where('service_manager', $filterSM);
                    $smProjects = \App\Models\Kontrak::where('service_manager', $filterSM)->pluck('project_id');
                    $realisasiQuery->whereIn('realisasi.project_id', $smProjects);
                    $prognosaQuery->whereIn('project_id', $smProjects);
                } elseif ($isPIC) {
                    $realisasiQuery->where('realisasi.pic', $filterSM);
                    $prognosaQuery->where('pic', $filterSM);
                    $picProjects = Realisasi::where('pic', $filterSM)->pluck('project_id');
                    $kontrakQuery->whereIn('project_id', $picProjects);
                } else {
                    $kontrakQuery->where('service_manager', $filterSM);
                    $realisasiQuery->where('realisasi.pic', $filterSM);
                }
            }
        }

        // === STATS CARDS ===
        $totalKontrak        = (clone $kontrakQuery)->count();
        $totalNilaiKontrak   = (clone $kontrakQuery)->sum('project_value');
        $totalRealisasi      = (clone $realisasiQuery)->sum('realisasi_biaya_final');
        $totalPrognosa       = (clone $prognosaQuery)->sum('prognosa_biaya');
        $persentaseRealisasi = $totalNilaiKontrak > 0 ? ($totalRealisasi / $totalNilaiKontrak * 100) : 0;

        $stats = [
            'total_transaksi'      => Realisasi::count(),
            'total_realisasi'      => $totalRealisasi,
            'total_project'        => Realisasi::distinct('project_id')->count('project_id'),
            'total_vendor'         => Realisasi::whereNotNull('vendor')->distinct('vendor')->count('vendor'),
            'realisasi_bulan_ini'  => Realisasi::where('tahun', $currentYear)
                                               ->where('periode', $currentMonth)
                                               ->sum('realisasi_biaya_final'),
            'total_kontrak'        => $totalKontrak,
            'total_nilai_kontrak'  => $totalNilaiKontrak,
            'total_prognosa'       => $totalPrognosa,
            'persentase_realisasi' => $persentaseRealisasi,
        ];

        // === CHART 1: Orang (Service Manager) vs Kontrak yang Dipegang ===
        $smStats = (clone $kontrakQuery)
            ->select('service_manager', DB::raw('COUNT(*) as contract_count'), DB::raw('SUM(project_value) as total_value'))
            ->whereNotNull('service_manager')
            ->groupBy('service_manager')
            ->orderByDesc('contract_count')
            ->get();

        // === CHART 2: Total Nilai Kontrak Vendor (Semua & Masing-Masing) ===
        // Group by client
        $nilaiKontrakPerClient = (clone $kontrakQuery)
            ->select('project_client', DB::raw('SUM(project_value) as total'))
            ->groupBy('project_client')
            ->orderByDesc('total')
            ->get();

        // Masing-masing kontrak per client
        $vendorContractsBreakdown = (clone $kontrakQuery)
            ->select('project_client', 'project_id', 'project_name', 'project_value')
            ->whereNotNull('project_client')
            ->orderBy('project_client')
            ->orderByDesc('project_value')
            ->get()
            ->groupBy('project_client');

        // === CHART 3: Realisasi Nilai Kontrak Vendor (Semua & Masing-Masing) ===
        // Group by client
        $realisasiPerClient = (clone $realisasiQuery)
            ->leftJoin('kontrak', 'realisasi.project_id', '=', 'kontrak.project_id')
            ->select('kontrak.project_client', DB::raw('SUM(realisasi.realisasi_biaya_final) as total'))
            ->whereNotNull('kontrak.project_client')
            ->groupBy('kontrak.project_client')
            ->orderByDesc('total')
            ->get();

        // Masing-masing kontrak per client (realisasi)
        $vendorRealisasiBreakdown = (clone $realisasiQuery)
            ->leftJoin('kontrak', 'realisasi.project_id', '=', 'kontrak.project_id')
            ->select('kontrak.project_client', 'realisasi.project_id', 'realisasi.project_name', DB::raw('SUM(realisasi.realisasi_biaya_final) as total'))
            ->whereNotNull('kontrak.project_client')
            ->groupBy('kontrak.project_client', 'realisasi.project_id', 'realisasi.project_name')
            ->orderBy('kontrak.project_client')
            ->orderByDesc('total')
            ->get()
            ->groupBy('project_client');

        // === CHART 4: Realisasi & Prognosa per Bulan ===
        $periodeOrder = Realisasi::$periodeOrder;
        $realisasiPerBulan = (clone $realisasiQuery)
            ->select('periode', DB::raw('SUM(realisasi_biaya_final) as total'))
            ->groupBy('periode')
            ->get()
            ->sortBy(fn($item) => $periodeOrder[$item->periode] ?? 99)
            ->values();

        $prognosaPerBulan = (clone $prognosaQuery)
            ->select('periode', DB::raw('SUM(prognosa_biaya) as total'))
            ->groupBy('periode')
            ->get()
            ->sortBy(fn($item) => $periodeOrder[$item->periode] ?? 99)
            ->values();

        // === FILTER DROPDOWNS ===
        if ($isSMUser) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $projectNameList = \App\Models\Kontrak::whereIn('service_manager', $smNames)
                ->distinct()->orderBy('project_name')->whereNotNull('project_name')->pluck('project_name');
        } else {
            $projectNameList  = \App\Models\Kontrak::distinct()->orderBy('project_name')->whereNotNull('project_name')->pluck('project_name');
        }

        $serviceManagerList = \App\Models\Kontrak::whereNotNull('service_manager')->distinct()->pluck('service_manager')
            ->concat(Realisasi::whereNotNull('pic')->distinct()->pluck('pic'))
            ->unique()
            ->sort()
            ->values();
        $clientList       = \App\Models\Kontrak::distinct()->orderBy('project_client')->whereNotNull('project_client')->pluck('project_client');
        $periodeList      = collect($this->periodeList);

        // === PROJECT HEALTH & ACTION CENTER (FOR SM & ADMIN) ===
        $projectHealthList = [];
        $healthCounts = [
            'safe'     => 0,
            'warning'  => 0,
            'critical' => 0,
        ];
        $actionCounts = [
            'pending_basto'     => 0,
            'critical_projects' => 0,
            'unbilled_realisasi'=> 0,
        ];

        // Ambil daftar project relevan untuk dihitung health status-nya
        $supervisedProjects = (clone $kontrakQuery)->get(['project_id', 'project_name', 'project_value', 'service_manager']);
        
        foreach ($supervisedProjects as $kp) {
            $pId = $kp->project_id;
            $pVal = (float) $kp->project_value;
            $rQ = Realisasi::where('project_id', $pId);
            $pQ = \App\Models\Prognosa::where('project_id', $pId);
            if ($filterTahun) {
                $rQ->where('tahun', $filterTahun);
                $pQ->where('tahun_normalized', $filterTahun);
            }
            $realisasiSum = (float) $rQ->sum('realisasi_biaya_final');
            $prognosaSum  = (float) $pQ->sum('prognosa_biaya');
            
            $serapanPct = $pVal > 0 ? ($realisasiSum / $pVal * 100) : 0;
            $sisaBudget = $pVal - $realisasiSum;

            if ($serapanPct >= 90) {
                $status = 'critical';
                $statusLabel = 'Kritis / Overbudget';
                $badgeClass  = 'danger';
                $healthCounts['critical']++;
            } elseif ($serapanPct >= 75) {
                $status = 'warning';
                $statusLabel = 'Waspada (75-90%)';
                $badgeClass  = 'warning';
                $healthCounts['warning']++;
            } else {
                $status = 'safe';
                $statusLabel = 'Sehat (<75%)';
                $badgeClass  = 'success';
                $healthCounts['safe']++;
            }

            $projectHealthList[] = [
                'project_id'     => $pId,
                'project_name'   => $kp->project_name,
                'service_manager'=> $kp->service_manager,
                'pagu'           => $pVal,
                'realisasi'      => $realisasiSum,
                'prognosa'       => $prognosaSum,
                'sisa'           => $sisaBudget,
                'serapan_pct'    => round($serapanPct, 1),
                'status'         => $status,
                'status_label'   => $statusLabel,
                'badge_class'    => $badgeClass,
            ];
        }

        // Urutkan project health: critical dulu, lalu warning, lalu safe
        usort($projectHealthList, function($a, $b) {
            $priority = ['critical' => 1, 'warning' => 2, 'safe' => 3];
            return ($priority[$a['status']] <=> $priority[$b['status']]) ?: ($b['serapan_pct'] <=> $a['serapan_pct']);
        });

        // Action Center Counts
        if ($isSMUser) {
            $actionCounts['pending_basto'] = \App\Models\Basto::where('status', 'submitted')->where('sm_user_id', auth()->id())->count();
        } else {
            $actionCounts['pending_basto'] = \App\Models\Basto::where('status', 'submitted')->count();
        }
        $actionCounts['critical_projects'] = $healthCounts['critical'];
        $actionCounts['unbilled_realisasi'] = (clone $realisasiQuery)->where(function($q) {
            $q->whereNull('status')->orWhere('status', 'PENDING')->orWhere('status', 'WAIT INV')->orWhere('status', 'IN PROGRES');
        })->count();

        // Transaksi terbaru
        $recentTransaksi = Realisasi::orderBy('updated_at', 'desc')->limit(8)->get();

        // === METRIK OPERASIONAL DMO (DMO COMMAND CENTER) ===
        $dmoBase = \App\Models\Basto::query();
        if (auth()->check() && auth()->user()->hasRole('dmo')) {
            $dmoBase->where('dmo_user_id', auth()->id());
        }
        $dmoRevisionCount  = (clone $dmoBase)->where('qc_status', 'revision_needed')->count();
        $dmoDraftCount     = (clone $dmoBase)->where('status', 'draft')->count();
        $dmoSubmittedCount = (clone $dmoBase)->where('status', 'submitted')->count();
        
        $allBastoProjectIds = \App\Models\Basto::distinct()->pluck('project_id')->toArray();
        $dmoProjectsWithoutBastoCount = Realisasi::distinct()->whereNotIn('project_id', $allBastoProjectIds)->count('project_id');
        $dmoMonthlyTransactionsCount  = Realisasi::where('tahun', $currentYear)->where('periode', $currentMonth)->count();

        $viewData = compact(
            'stats', 'realisasiPerBulan', 'prognosaPerBulan',
            'smStats', 'nilaiKontrakPerClient', 'vendorContractsBreakdown',
            'realisasiPerClient', 'vendorRealisasiBreakdown', 'recentTransaksi',
            'projectNameList', 'serviceManagerList', 'clientList', 'periodeList',
            'filterProject', 'filterSM', 'filterClient', 'filterPeriode', 'filterTahun', 'tahunList',
            'projectHealthList', 'healthCounts', 'actionCounts',
            'totalBastos', 'qcPendingCount', 'qcRevisionCount', 'qcVerifiedCount',
            'passRate', 'qcQueue', 'criteriaCompliance', 'totalWithChecklist', 'recentInspections',
            'dmoRevisionCount', 'dmoDraftCount', 'dmoSubmittedCount', 'dmoProjectsWithoutBastoCount', 'dmoMonthlyTransactionsCount'
        );

        if ((auth()->check() && auth()->user()->hasRole('osm_qc')) || $request->get('view') === 'qc') {
            return view('dashboard.qc', $viewData);
        }

        return view('dashboard.index', $viewData);
    }

    /**
     * Cetak Ringkasan Eksekutif Portofolio Proyek (Executive Portfolio Summary)
     */
    public function executiveSummary(Request $request)
    {
        $isSMUser = auth()->check() && auth()->user()->hasRole('osm_service_manager');
        $isAdmin  = auth()->check() && auth()->user()->hasRole('admin');

        $smName = $request->input('service_manager');
        $filterTahun = $request->input('tahun');

        $tahunList = Realisasi::distinct()->whereNotNull('tahun')->where('tahun', '!=', '')->orderBy('tahun', 'desc')->pluck('tahun');
        if ($tahunList->isEmpty()) {
            $tahunList = collect(['2026']);
        }

        if ($isSMUser) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $smDisplayName = auth()->user()->name;
            $kontrakList = \App\Models\Kontrak::whereIn('service_manager', $smNames)->get();
        } else {
            if ($smName && $smName !== 'all') {
                $smNames = [$smName];
                $smDisplayName = $smName;
                $kontrakList = \App\Models\Kontrak::where('service_manager', $smName)->get();
            } else {
                $smNames = [];
                $smDisplayName = 'Seluruh Service Manager (Konsolidasi)';
                $kontrakList = \App\Models\Kontrak::all();
            }
        }

        $availableSMs = \App\Models\Kontrak::whereNotNull('service_manager')
            ->where('service_manager', '!=', '')
            ->distinct()
            ->orderBy('service_manager')
            ->pluck('service_manager');

        // Kontrak Portofolio
        $projectIds = $kontrakList->pluck('project_id')->toArray();

        // Metrics
        $totalNilaiKontrak = $kontrakList->sum('project_value');

        $rQuery = Realisasi::whereIn('project_id', $projectIds);
        $pQuery = \App\Models\Prognosa::whereIn('project_id', $projectIds);
        if ($filterTahun) {
            $rQuery->where('tahun', $filterTahun);
            $pQuery->where('tahun_normalized', $filterTahun);
        }
        $totalRealisasi = $rQuery->sum('realisasi_biaya_final');
        $totalPrognosa = $pQuery->sum('prognosa_biaya');
        $persentaseRealisasi = $totalNilaiKontrak > 0 ? ($totalRealisasi / $totalNilaiKontrak * 100) : 0;
        $sisaBudget = $totalNilaiKontrak - $totalRealisasi;

        // Project Breakdown
        $projectItems = [];
        foreach ($kontrakList as $k) {
            $rItemQ = Realisasi::where('project_id', $k->project_id);
            $pItemQ = \App\Models\Prognosa::where('project_id', $k->project_id);
            if ($filterTahun) {
                $rItemQ->where('tahun', $filterTahun);
                $pItemQ->where('tahun_normalized', $filterTahun);
            }
            $realisasi = (float) $rItemQ->sum('realisasi_biaya_final');
            $prognosa  = (float) $pItemQ->sum('prognosa_biaya');
            $pagu      = (float) $k->project_value;
            $serapan   = $pagu > 0 ? ($realisasi / $pagu * 100) : 0;
            
            $bastoApproved = \App\Models\Basto::where('project_id', $k->project_id)->where('status', 'approved')->count();
            $bastoSubmitted = \App\Models\Basto::where('project_id', $k->project_id)->where('status', 'submitted')->count();

            $status = $serapan >= 90 ? 'critical' : ($serapan >= 75 ? 'warning' : 'safe');
            $statusLabel = $serapan >= 90 ? 'Kritis / Over' : ($serapan >= 75 ? 'Waspada' : 'Sehat');
            $badgeColor = $serapan >= 90 ? 'danger' : ($serapan >= 75 ? 'warning' : 'success');

            $projectItems[] = [
                'project_id'    => $k->project_id,
                'project_name'  => $k->project_name,
                'client'        => $k->project_client ?? 'PT PGN Tbk',
                'pagu'          => $pagu,
                'realisasi'     => $realisasi,
                'prognosa'      => $prognosa,
                'sisa'          => $pagu - $realisasi,
                'serapan'       => round($serapan, 1),
                'basto_info'    => "{$bastoApproved} Disetujui" . ($bastoSubmitted > 0 ? ", {$bastoSubmitted} Pending" : ""),
                'status'        => $status,
                'status_label'  => $statusLabel,
                'badge_color'   => $badgeColor,
            ];
        }

        // BASTO summary
        $bastoQuery = \App\Models\Basto::whereIn('project_id', $projectIds);
        $totalBasto = (clone $bastoQuery)->count();
        $bastoApprovedTotal = (clone $bastoQuery)->where('status', 'approved')->count();
        $bastoPendingTotal = (clone $bastoQuery)->where('status', 'submitted')->count();
        $bastoDraftTotal = (clone $bastoQuery)->where('status', 'draft')->count();

        return view('dashboard.executive_summary', compact(
            'smDisplayName', 'totalNilaiKontrak', 'totalRealisasi',
            'totalPrognosa', 'persentaseRealisasi', 'sisaBudget',
            'projectItems', 'totalBasto', 'bastoApprovedTotal', 'bastoPendingTotal', 'bastoDraftTotal', 'availableSMs',
            'filterTahun', 'tahunList'
        ));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    protected function validateRealisasi(Request $request, ?string $excludeId = null): array
    {
        return $request->validate([
            'project_id'               => 'required|string|max:100',
            'project_name'             => 'required|string|max:1000',
            'item_biaya'               => 'nullable|string|max:255',
            'satuan_kerja'             => 'nullable|string|max:100',
            'pic'                      => 'nullable|string|max:150',
            'periode'                  => ['required', Rule::in($this->periodeList)],
            'realisasi_biaya_original' => 'nullable|numeric|min:0',
            'currency'                 => ['nullable', Rule::in($this->currencyList)],
            'realisasi_biaya_idr'      => 'nullable|numeric|min:0',
            'status'                   => 'nullable|string|max:50',
            'vendor'                   => 'nullable|string|max:255',
            'sifat'                    => 'nullable|string|max:50',
            'link_evidence'            => 'nullable|string|max:2000',
            'tahun'                    => 'required|integer|min:2000|max:2099',
            'data_flag'                => 'nullable|string|max:100',
            'realisasi_biaya_final'    => 'nullable|numeric|min:0',
            'source_row'               => 'nullable|integer',
        ], [
            'project_id.required'  => 'Project ID wajib diisi.',
            'project_name.required' => 'Project Name wajib diisi.',
            'periode.required'     => 'Periode wajib dipilih.',
            'periode.in'           => 'Periode tidak valid. Pilih bulan yang tersedia.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'tahun.integer'        => 'Tahun harus berupa angka.',
            'tahun.min'            => 'Tahun minimal 2000.',
        ]);
    }

    protected function normalizeInput(array $data): array
    {
        $upperFields = ['project_id', 'item_biaya', 'satuan_kerja', 'pic', 'status', 'vendor', 'sifat', 'currency', 'data_flag'];
        foreach ($upperFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            }
        }

        // Trim project_name tapi pertahankan case
        if (!empty($data['project_name'])) {
            $data['project_name'] = trim($data['project_name']);
        }

        return $data;
    }

    /**
     * Global Quick Search Endpoint (Ctrl + K)
     */
    public function quickSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. Kontrak / Proyek
        $kontraks = \App\Models\Kontrak::where('project_id', 'like', "%{$q}%")
            ->orWhere('project_name', 'like', "%{$q}%")
            ->orWhere('contract_number', 'like', "%{$q}%")
            ->orWhere('project_client', 'like', "%{$q}%")
            ->limit(4)
            ->get();

        foreach ($kontraks as $k) {
            $results[] = [
                'type'     => 'Kontrak / Proyek',
                'badge'    => 'bg-primary',
                'icon'     => 'bi-folder-check',
                'title'    => "{$k->project_id} — {$k->project_name}",
                'subtitle' => "Client: {$k->project_client} | Nilai: Rp " . number_format($k->project_value, 0, ',', '.'),
                'url'      => route('dashboard', ['project_name' => $k->project_name]),
            ];
        }

        // 2. Realisasi
        $realisasis = Realisasi::where('project_id', 'like', "%{$q}%")
            ->orWhere('project_name', 'like', "%{$q}%")
            ->orWhere('vendor', 'like', "%{$q}%")
            ->orWhere('item_biaya', 'like', "%{$q}%")
            ->limit(4)
            ->get();

        foreach ($realisasis as $r) {
            $results[] = [
                'type'     => 'Realisasi',
                'badge'    => 'bg-success',
                'icon'     => 'bi-cash-stack',
                'title'    => "Realisasi #{$r->id}: {$r->project_id} - {$r->item_biaya}",
                'subtitle' => "Vendor: {$r->vendor} | {$r->periode} {$r->tahun} | Rp " . number_format($r->realisasi_biaya_final, 0, ',', '.'),
                'url'      => route('realisasi.show', $r->id),
            ];
        }

        // 3. Invoice
        $invoices = \App\Models\Invoice::where('invoice_number', 'like', "%{$q}%")
            ->orWhere('project_id', 'like', "%{$q}%")
            ->orWhere('customer', 'like', "%{$q}%")
            ->limit(4)
            ->get();

        foreach ($invoices as $inv) {
            $results[] = [
                'type'     => 'Invoice',
                'badge'    => 'bg-danger',
                'icon'     => 'bi-receipt',
                'title'    => "Invoice {$inv->invoice_number}",
                'subtitle' => "Customer: {$inv->customer} | Total: Rp " . number_format($inv->invoice_amount, 0, ',', '.') . " (" . strtoupper($inv->payment_status) . ")",
                'url'      => route('invoice.index', ['search' => $inv->invoice_number]),
            ];
        }

        // 4. BASTO
        $bastos = \App\Models\Basto::where('basto_number', 'like', "%{$q}%")
            ->orWhere('project_id', 'like', "%{$q}%")
            ->orWhere('project_name', 'like', "%{$q}%")
            ->limit(4)
            ->get();

        foreach ($bastos as $b) {
            $results[] = [
                'type'     => 'BASTO',
                'badge'    => 'bg-info text-dark',
                'icon'     => 'bi-file-earmark-check',
                'title'    => "BASTO: {$b->basto_number}",
                'subtitle' => "Proyek: {$b->project_name} | Status: " . strtoupper($b->status),
                'url'      => route('basto.show', $b->id),
            ];
        }

        return response()->json(array_slice($results, 0, 10));
    }
}
