<?php

namespace App\Http\Controllers;

use App\Models\Realisasi;
use App\Models\RealisasiLog;
use App\Services\RealisasiImportService;
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
    }

    // =========================================================================
    // INDEX — Halaman daftar realisasi
    // =========================================================================
    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'project_id', 'project_name', 'pic',
            'periode', 'status', 'vendor', 'tahun',
            'satuan_kerja', 'item_biaya',
        ]);

        $query = Realisasi::filter($filters)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode,
                'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
                'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
            ) DESC")
            ->orderBy('project_id');

        $realisasi  = $query->paginate(25)->withQueryString();
        $totalCount = Realisasi::filter($filters)->count();
        $totalIDR   = Realisasi::filter($filters)->sum('realisasi_biaya_final');

        // Dropdown options
        $projectIds  = Realisasi::distinct()->orderBy('project_id')->pluck('project_id');
        $picList     = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic');
        $vendorList  = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor');
        $tahunList   = Realisasi::distinct()->orderBy('tahun', 'desc')->whereNotNull('tahun')->pluck('tahun');

        return view('realisasi.index', compact(
            'realisasi', 'filters', 'totalCount', 'totalIDR',
            'projectIds', 'picList', 'vendorList', 'tahunList'
        ));
    }

    // =========================================================================
    // CREATE — Form tambah data baru
    // =========================================================================
    public function create()
    {
        $periodeList  = $this->periodeList;
        $currencyList = $this->currencyList;
        $statusList   = $this->statusList;
        $sifatList    = $this->sifatList;

        // Suggestion lists untuk autocomplete
        $projectIds   = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
        $projectNames = Realisasi::distinct()->orderBy('project_name')->pluck('project_name', 'project_name');
        $picList      = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
        $vendorList   = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
        $satuanList   = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
        $itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');

        return view('realisasi.create', compact(
            'periodeList', 'currencyList', 'statusList', 'sifatList',
            'projectIds', 'projectNames', 'picList', 'vendorList',
            'satuanList', 'itemBiayaList'
        ));
    }

    // =========================================================================
    // STORE — Simpan data baru
    // =========================================================================
    public function store(Request $request)
    {
        $validated = $this->validateRealisasi($request);

        // Normalisasi
        $validated = $this->normalizeInput($validated);

        // Generate record_key
        $recordKey = Realisasi::generateRecordKey($validated);

        // Cek duplicate record_key
        $existing = Realisasi::where('record_key', $recordKey)->first();
        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['record_key' => 'Data dengan kombinasi yang sama (Project ID, Item Biaya, PIC, Periode, Vendor, Sifat, Tahun) sudah ada. ID: #' . $existing->id . '. Gunakan fitur Edit jika ingin memperbarui.']);
        }

        $validated['record_key'] = $recordKey;

        $realisasi = Realisasi::create($validated);

        RealisasiLog::record(
            $realisasi->id, 'CREATE',
            null, $realisasi->toArray(),
            null, $request->ip()
        );

        return redirect()->route('realisasi.index')
            ->with('success', "Data realisasi #{$realisasi->id} berhasil ditambahkan.");
    }

    // =========================================================================
    // SHOW — Detail data
    // =========================================================================
    public function show(string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $logs      = RealisasiLog::where('realisasi_id', $id)->orderBy('created_at', 'desc')->take(10)->get();

        return view('realisasi.show', compact('realisasi', 'logs'));
    }

    // =========================================================================
    // EDIT — Form edit data lama
    // =========================================================================
    public function edit(string $id)
    {
        $realisasi    = Realisasi::findOrFail($id);
        $periodeList  = $this->periodeList;
        $currencyList = $this->currencyList;
        $statusList   = $this->statusList;
        $sifatList    = $this->sifatList;

        $projectIds    = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
        $picList       = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
        $vendorList    = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
        $satuanList    = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
        $itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');

        return view('realisasi.edit', compact(
            'realisasi', 'periodeList', 'currencyList', 'statusList', 'sifatList',
            'projectIds', 'picList', 'vendorList', 'satuanList', 'itemBiayaList'
        ));
    }

    // =========================================================================
    // UPDATE — Update data LAMA (BUKAN insert baru!)
    // =========================================================================
    public function update(Request $request, string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $oldData   = $realisasi->toArray();

        $validated = $this->validateRealisasi($request, $id);

        // Normalisasi
        $validated = $this->normalizeInput($validated);

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

        // UPDATE — ID tetap sama, BUKAN insert baru
        $realisasi->update($validated);

        RealisasiLog::record(
            $realisasi->id, 'UPDATE',
            $oldData, $realisasi->fresh()->toArray(),
            null, $request->ip()
        );

        return redirect()->route('realisasi.show', $realisasi->id)
            ->with('success', "Data realisasi #{$realisasi->id} berhasil diperbarui.");
    }

    // =========================================================================
    // DESTROY — Soft delete
    // =========================================================================
    public function destroy(Request $request, string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $oldData   = $realisasi->toArray();

        $realisasi->delete(); // Soft delete

        RealisasiLog::record(
            $realisasi->id, 'DELETE',
            $oldData, null,
            null, $request->ip()
        );

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
    public function dashboard()
    {
        $currentYear  = date('Y');
        $currentMonth = strtoupper(now()->locale('id')->translatedFormat('F'));

        // Mapping nama bulan English ke Indonesia (karena locale bisa berbeda)
        $monthMap = [
            'JANUARY' => 'JANUARI', 'FEBRUARY' => 'FEBRUARI', 'MARCH' => 'MARET',
            'APRIL'   => 'APRIL',   'MAY'      => 'MEI',       'JUNE'  => 'JUNI',
            'JULY'    => 'JULI',    'AUGUST'   => 'AGUSTUS',   'SEPTEMBER' => 'SEPTEMBER',
            'OCTOBER' => 'OKTOBER', 'NOVEMBER' => 'NOVEMBER',  'DECEMBER' => 'DESEMBER',
        ];
        $currentMonth = $monthMap[$currentMonth] ?? $currentMonth;

        $stats = [
            'total_transaksi'      => Realisasi::count(),
            'total_realisasi'      => Realisasi::sum('realisasi_biaya_final'),
            'total_project'        => Realisasi::distinct('project_id')->count('project_id'),
            'total_vendor'         => Realisasi::whereNotNull('vendor')->distinct('vendor')->count('vendor'),
            'realisasi_bulan_ini'  => Realisasi::where('tahun', $currentYear)
                                               ->where('periode', $currentMonth)
                                               ->sum('realisasi_biaya_final'),
        ];

        // Chart: Realisasi per bulan (tahun ini)
        $periodeOrder = Realisasi::$periodeOrder;
        $realisasiPerBulan = Realisasi::where('tahun', $currentYear)
            ->select('periode', DB::raw('SUM(realisasi_biaya_final) as total'))
            ->groupBy('periode')
            ->get()
            ->sortBy(fn($item) => $periodeOrder[$item->periode] ?? 99)
            ->values();

        // Chart: Realisasi per project (top 10)
        $realisasiPerProject = Realisasi::where('tahun', $currentYear)
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total'))
            ->groupBy('project_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Chart: Realisasi per status
        $realisasiPerStatus = Realisasi::where('tahun', $currentYear)
            ->select('status', DB::raw('COUNT(*) as count, SUM(realisasi_biaya_final) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        // Chart: Realisasi per vendor (top 8)
        $realisasiPerVendor = Realisasi::where('tahun', $currentYear)
            ->whereNotNull('vendor')
            ->select('vendor', DB::raw('SUM(realisasi_biaya_final) as total'))
            ->groupBy('vendor')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // Transaksi terbaru
        $recentTransaksi = Realisasi::orderBy('updated_at', 'desc')->limit(8)->get();

        return view('dashboard.index', compact(
            'stats', 'realisasiPerBulan', 'realisasiPerProject',
            'realisasiPerStatus', 'realisasiPerVendor', 'recentTransaksi',
            'currentYear'
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
}
