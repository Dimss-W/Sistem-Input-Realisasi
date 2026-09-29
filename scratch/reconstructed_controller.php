<?php
3: namespace App\Http\Controllers;
44:02Z","content":"✅ Data cocok sempurna! Jumlah Kontrak **84**, Total Nilai Kontrak **Rp309.33 Miliar**, Total 
5: use App\Models\Realisasi;
// MISSING LINE 5
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
18: class RealisasiController extends Controller
// MISSING LINE 18
{
protected RealisasiImportService $importService;
22:     protected array $periodeList = [
// MISSING LINE 22
'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER',
];
27:     protected array $currencyList = ['IDR', 'USD', 'EUR', 'SGD', 'JPY'];
// MISSING LINE 27
29:     protected array $statusList = ['PAID', 'UNPAID', 'PENDING', 'CANCEL', 'PROSES'];
// MISSING LINE 29
31:     protected array $sifatList = ['MONTHLY', 'YEARLY', 'ONE-TIME', 'WEEKLY'];
// MISSING LINE 31
33:     public function __construct(RealisasiImportService $importService)
// MISSING LINE 33
{
$this->importService = $importService;
}
38:     // =========================================================================
// MISSING LINE 38
// INDEX — Halaman daftar realisasi
// =========================================================================
public function index(Request $request)
{
$filters = $request->only([
'search', 'project_id', 'project_name', 'pic',
'periode', 'status', 'vendor', 'tahun',
'satuan_kerja', 'item_biaya',
]);
49:         $query = Realisasi::filter($filters)
// MISSING LINE 49
->orderBy('tahun', 'desc')
->orderByRaw("FIELD(periode,
'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
) DESC")
->orderBy('project_id');
57:         $realisasi  = $query->paginate(25)->withQueryString();
// MISSING LINE 57
$totalCount = Realisasi::filter($filters)->count();
$totalIDR   = Realisasi::filter($filters)->sum('realisasi_biaya_final');
61:         // Dropdown options
// MISSING LINE 61
$projectIds  = Realisasi::distinct()->orderBy('project_id')->pluck('project_id');
$picList     = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic');
$vendorList  = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor');
$tahunList   = Realisasi::distinct()->orderBy('tahun', 'desc')->whereNotNull('tahun')->pluck('tahun');
67:         return view('realisasi.index', compact(
// MISSING LINE 67
'realisasi', 'filters', 'totalCount', 'totalIDR',
'projectIds', 'picList', 'vendorList', 'tahunList'
));
}
73:     // =========================================================================
// MISSING LINE 73
// CREATE — Form tambah data baru
// =========================================================================
public function create()
{
$periodeList  = $this->periodeList;
$currencyList = $this->currencyList;
$statusList   = $this->statusList;
$sifatList    = $this->sifatList;
83:         // Suggestion lists untuk autocomplete
// MISSING LINE 83
$projectIds   = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
$projectNames = Realisasi::distinct()->orderBy('project_name')->pluck('project_name', 'project_name');
$picList      = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
$vendorList   = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
$satuanList   = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
$itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');
91:         return view('realisasi.create', compact(
// MISSING LINE 91
'periodeList', 'currencyList', 'statusList', 'sifatList',
'projectIds', 'projectNames', 'picList', 'vendorList',
'satuanList', 'itemBiayaList'
));
}
98:     // =========================================================================
// MISSING LINE 98
// STORE — Simpan data baru
// =========================================================================
public function store(Request $request)
{
$validated = $this->validateRealisasi($request);
105:         // Normalisasi
// MISSING LINE 105
$validated = $this->normalizeInput($validated);
108:         // Generate record_key
// MISSING LINE 108
$recordKey = Realisasi::generateRecordKey($validated);
111:         // Cek duplicate record_key
// MISSING LINE 111
$existing = Realisasi::where('record_key', $recordKey)->first();
if ($existing) {
return back()
->withInput()
->withErrors(['record_key' => 'Data dengan kombinasi yang sama (Project ID, Item Biaya, PIC, Periode, Vendor, Sifat, Tahun) sudah ada. ID: #' . $existing->id . '. Gunakan fitur Edit jika ingin memperbarui.']);
}
119:         $validated['record_key'] = $recordKey;
// MISSING LINE 119
121:         $realisasi = Realisasi::create($validated);
// MISSING LINE 121
123:         RealisasiLog::record(
// MISSING LINE 123
$realisasi->id, 'CREATE',
null, $realisasi->toArray(),
null, $request->ip()
);
129:         return redirect()->route('realisasi.index')
// MISSING LINE 129
->with('success', "Data realisasi #{$realisasi->id} berhasil ditambahkan.");
}
133:     // =========================================================================
// MISSING LINE 133
// SHOW — Detail data
// =========================================================================
public function show(string $id)
{
$realisasi = Realisasi::findOrFail($id);
$logs      = RealisasiLog::where('realisasi_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
141:         return view('realisasi.show', compact('realisasi', 'logs'));
// MISSING LINE 141
}
144:     // =========================================================================
// MISSING LINE 144
// EDIT — Form edit data lama
// =========================================================================
public function edit(string $id)
{
$realisasi    = Realisasi::findOrFail($id);
$periodeList  = $this->periodeList;
$currencyList = $this->currencyList;
$statusList   = $this->statusList;
$sifatList    = $this->sifatList;
155:         $projectIds    = Realisasi::distinct()->orderBy('project_id')->pluck('project_id', 'project_id');
// MISSING LINE 155
$picList       = Realisasi::distinct()->orderBy('pic')->whereNotNull('pic')->pluck('pic', 'pic');
$vendorList    = Realisasi::distinct()->orderBy('vendor')->whereNotNull('vendor')->pluck('vendor', 'vendor');
$satuanList    = Realisasi::distinct()->orderBy('satuan_kerja')->whereNotNull('satuan_kerja')->pluck('satuan_kerja', 'satuan_kerja');
$itemBiayaList = Realisasi::distinct()->orderBy('item_biaya')->whereNotNull('item_biaya')->pluck('item_biaya', 'item_biaya');
161:         return view('realisasi.edit', compact(
// MISSING LINE 161
'realisasi', 'periodeList', 'currencyList', 'statusList', 'sifatList',
'projectIds', 'picList', 'vendorList', 'satuanList', 'itemBiayaList'
));
}
167:     // =========================================================================
// MISSING LINE 167
// UPDATE — Update data LAMA (BUKAN insert baru!)
// =========================================================================
public function update(Request $request, string $id)
{
$realisasi = Realisasi::findOrFail($id);
$oldData   = $realisasi->toArray();
175:         $validated = $this->validateRealisasi($request, $id);
// MISSING LINE 175
177:         // Normalisasi
// MISSING LINE 177
$validated = $this->normalizeInput($validated);
180:         // Hitung record_key baru
// MISSING LINE 180
$newRecordKey = Realisasi::generateRecordKey($validated);
183:         // Cek jika record_key berubah dan sudah ada di record lain
// MISSING LINE 183
if ($newRecordKey !== $realisasi->record_key) {
$conflicting = Realisasi::where('record_key', $newRecordKey)
->where('id', '!=', $id)
->first();
189:             if ($conflicting) {
// MISSING LINE 189
return back()
->withInput()
->withErrors(['record_key' => 'Kombinasi data yang diubah sudah dimiliki record lain (ID: #' . $conflicting->id . '). Periksa kembali data Anda.']);
}
}
196:         $validated['record_key'] = $newRecordKey;
// MISSING LINE 196
198:         // UPDATE — ID tetap sama, BUKAN insert baru
// MISSING LINE 198
$realisasi->update($validated);
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.
// MISSING LINE 201
// MISSING LINE 202
// MISSING LINE 203
// MISSING LINE 204
// MISSING LINE 205
// MISSING LINE 206
// MISSING LINE 207
// MISSING LINE 208
// MISSING LINE 209
// MISSING LINE 210
// MISSING LINE 211
// MISSING LINE 212
// MISSING LINE 213
// MISSING LINE 214
// MISSING LINE 215
// MISSING LINE 216
// MISSING LINE 217
// MISSING LINE 218
// MISSING LINE 219
221:         RealisasiLog::record(
// MISSING LINE 221
$realisasi->id, 'DELETE',
$oldData, null,
null, $request->ip()
);
227:         return redirect()->route('realisasi.index')
// MISSING LINE 227
->with('success', "Data realisasi #{$id} berhasil dihapus.");
}
231:     // =========================================================================
// MISSING LINE 231
// IMPORT FORM — Halaman upload Excel
// =========================================================================
public function importForm()
{
return view('realisasi.import');
}
239:     // =========================================================================
// MISSING LINE 239
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
257:         $file     = $request->file('file');
// MISSING LINE 257
$filePath = $file->store('imports', 'local');
$fullPath = storage_path('app/' . $filePath);
261:         $summary = $this->importService->import(
// MISSING LINE 261
$fullPath,
null,
$request->ip()
);
267:         // Hapus file temporary
// MISSING LINE 267
if (file_exists($fullPath)) {
unlink($fullPath);
}
272:         return redirect()->route('realisasi.import.form')
// MISSING LINE 272
->with('import_summary', $summary);
}
276:     // =========================================================================
// MISSING LINE 276
// EXPORT — Export ke Excel sesuai filter aktif
// =========================================================================
public function export(Request $request)
{
$filters = $request->only([
'search', 'project_id', 'project_name', 'pic',
'periode', 'status', 'vendor', 'tahun',
'satuan_kerja', 'item_biaya',
]);
287:         $data = Realisasi::filter($filters)
// MISSING LINE 287
->orderBy('tahun', 'desc')
->orderByRaw("FIELD(periode,
'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
) DESC")
->orderBy('project_id')
->get();
296:         $spreadsheet = new Spreadsheet();
// MISSING LINE 296
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Realisasi');
300:         // Header styling
// MISSING LINE 300
$headers = [
'No', 'Source Row', 'Project ID', 'Project Name', 'Item Biaya',
'Satuan Kerja', 'PIC', 'Periode', 'Realisasi Biaya Original',
'Currency', 'Realisasi Biaya IDR', 'Status', 'Vendor', 'Sifat',
'Link Evidence', 'Tahun', 'Data Flag', 'Realisasi Biaya Final',
];
308:         foreach ($headers as $colIndex => $header) {
// MISSING LINE 308
$col = chr(65 + $colIndex);
$cell = $col . '1';
$sheet->setCellValue($cell, $header);
}
314:         // Header style
// MISSING LINE 314
$headerStyle = [
'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$lastCol = chr(65 + count($headers) - 1);
$sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
324:         // Data rows
// MISSING LINE 324
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
348:             foreach ($values as $colIndex => $value) {
// MISSING LINE 348
$col = chr(65 + $colIndex);
$sheet->setCellValue("{$col}{$excelRow}", $value);
}
353:             // Zebra striping
// MISSING LINE 353
if ($rowIndex % 2 === 1) {
$sheet->getStyle("A{$excelRow}:{$lastCol}{$excelRow}")
->getFill()
->setFillType(Fill::FILL_SOLID)
->getStartColor()->setRGB('F5F8FC');
}
}
362:         // Auto column width
// MISSING LINE 362
foreach (range('A', $lastCol) as $col) {
$sheet->getColumnDimension($col)->setAutoSize(true);
}
367:         // Freeze header
// MISSING LINE 367
$sheet->freezePane('A2');
370:         $filename = 'Realisasi_Export_' . date('Ymd_His') . '.xlsx';
// MISSING LINE 370
372:         $writer = new Xlsx($spreadsheet);
// MISSING LINE 372
374:         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// MISSING LINE 374
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
378:         $writer->save('php://output');
// MISSING LINE 378
exit;
}
382:     // =========================================================================
// MISSING LINE 382
// DOWNLOAD TEMPLATE — Template Excel kosong untuk import
// =========================================================================
public function downloadTemplate()
{
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Realisasi');
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.
// MISSING LINE 391
'Source_Row', 'Project_ID', 'Project_Name', 'Item_Biaya',
'Satuan_Kerja', 'PIC', 'Periode', 'Realisasi_Biaya_Original',
'Currency', 'Realisasi_Biaya_IDR', 'Status', 'Vendor', 'Sifat',
'Link_Evidence', 'Tahun', 'Data_Flag', 'Realisasi_Biaya_Final',
];
398:         foreach ($headers as $colIndex => $header) {
// MISSING LINE 398
$col = chr(65 + $colIndex);
$sheet->setCellValue("{$col}1", $header);
}
403:         $lastCol = chr(65 + count($headers) - 1);
// MISSING LINE 403
405:         // Header style
// MISSING LINE 405
$sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);
412:         // Contoh data baris pertama
// MISSING LINE 412
$example = [
'1', 'PS-024-00',
'Pekerjaan Jasa Operasi dan Pemeliharaan Terintegrasi Gas Management System',
'UPAH', 'DMO', 'ULFA', 'JANUARI',
'863767114', 'IDR', '863767114', 'PAID', 'PERSADA', 'MONTHLY',
'', '2026', '', '863767114',
];
421:         foreach ($example as $colIndex => $val) {
// MISSING LINE 421
$col = chr(65 + $colIndex);
$sheet->setCellValue("{$col}2", $val);
}
426:         // Style baris contoh
// MISSING LINE 426
$sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']],
]);
431:         foreach (range('A', $lastCol) as $col) {
// MISSING LINE 431
$sheet->getColumnDimension($col)->setAutoSize(true);
}
435:         $writer   = new Xlsx($spreadsheet);
// MISSING LINE 435
$filename = 'Template_Import_Realisasi.xlsx';
438:         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// MISSING LINE 438
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
442:         $writer->save('php://output');
// MISSING LINE 442
exit;
}
446:     // =========================================================================
// MISSING LINE 446
// DASHBOARD — Ringkasan statistik
// =========================================================================
public function dashboard(Request $request)
{
$currentYear  = date('Y');
$currentMonth = strtoupper(now()->locale('id')->translatedFormat('F'));
454:         $monthMap = [
// MISSING LINE 454
'JANUARY' => 'JANUARI', 'FEBRUARY' => 'FEBRUARI', 'MARCH' => 'MARET',
'APRIL'   => 'APRIL',   'MAY'      => 'MEI',       'JUNE'  => 'JUNI',
'JULY'    => 'JULI',    'AUGUST'   => 'AGUSTUS',   'SEPTEMBER' => 'SEPTEMBER',
'OCTOBER' => 'OKTOBER', 'NOVEMBER' => 'NOVEMBER',  'DECEMBER' => 'DESEMBER',
];
$currentMonth = $monthMap[$currentMonth] ?? $currentMonth;
462:         // === FILTER PARAMS ===
463:         $stats = [
$filterProject  = $request->input('project_name');
$filterSM       = $request->input('service_manager');
$filterClient   = $request->input('client');
$filterPeriode  = $request->input('periode');
468:         // === KONTRAK QUERY ===
'realisasi_bulan_ini'  => Realisasi::where('tahun', $currentYear)
$kontrakQuery = \App\Models\Kontrak::query();
if ($filterProject) {
$kontrakQuery->where('project_name', 'like', "%{$filterProject}%");
}
if ($filterClient) {
$kontrakQuery->where('project_client', $filterClient);
}
477:         // === REALISASI QUERY ===
$kontrakQuery->where('project_client', $filterClient);
$realisasiQuery = Realisasi::query();
if ($filterProject) {
$realisasiQuery->where('project_name', 'like', "%{$filterProject}%");
}
if ($filterClient) {
$realisasiQuery->where('vendor', $filterClient);
}
if ($filterPeriode) {
$realisasiQuery->where('periode', $filterPeriode);
}
489:         // === PROGNOSA QUERY ===
$realisasiQuery->where('vendor', $filterClient);
$prognosaQuery = \App\Models\Prognosa::query();
if ($filterProject) {
$prognosaQuery->where('project_name', 'like', "%{$filterProject}%");
}
if ($filterPeriode) {
$prognosaQuery->where('periode', $filterPeriode);
}
498:         // === RESOLVE SERVICE MANAGER / PIC FILTER ===
$prognosaQuery->where('project_name', 'like', "%{$filterProject}%");
if ($filterSM) {
$isSM = \App\Models\Kontrak::where('service_manager', $filterSM)->exists();
$isPIC = Realisasi::where('pic', $filterSM)->exists();
503:             if ($isSM) {
504:         // === STATS CARDS ===
$kontrakQuery->where('service_manager', $filterSM);
$smProjects = \App\Models\Kontrak::where('service_manager', $filterSM)->pluck('project_id');
$realisasiQuery->whereIn('project_id', $smProjects);
$prognosaQuery->whereIn('project_id', $smProjects);
} elseif ($isPIC) {
$realisasiQuery->where('pic', $filterSM);
$prognosaQuery->where('pic', $filterSM);
$picProjects = Realisasi::where('pic', $filterSM)->pluck('project_id');
$kontrakQuery->whereIn('project_id', $picProjects);
} else {
$kontrakQuery->where('service_manager', $filterSM);
$realisasiQuery->where('pic', $filterSM);
}
}
519:         // === STATS CARDS ===
520:     protected function validateRealisasi(Request $request, ?string $excludeId = null): array
$totalKontrak        = (clone $kontrakQuery)->count();
$totalNilaiKontrak   = (clone $kontrakQuery)->sum('project_value');
$totalRealisasi      = (clone $realisasiQuery)->sum('realisasi_biaya_final');
$totalPrognosa       = (clone $prognosaQuery)->sum('prognosa_biaya');
$persentaseRealisasi = $totalNilaiKontrak > 0 ? ($totalRealisasi / $totalNilaiKontrak * 100) : 0;
526:         $stats = [
'satuan_kerja'             => 'nullable|string|max:100',
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
540:         // === CHART 1: Orang (Service Manager) vs Kontrak yang Dipegang ===
], [
$smStats = (clone $kontrakQuery)
->select('service_manager', DB::raw('COUNT(*) as contract_count'), DB::raw('SUM(project_value) as total_value'))
->whereNotNull('service_manager')
->groupBy('service_manager')
->orderByDesc('contract_count')
->get();
548:         // === CHART 2: Total Nilai Kontrak Vendor (Semua & Masing-Masing) ===
]);
// Group by client
$nilaiKontrakPerClient = (clone $kontrakQuery)
->select('project_client', DB::raw('SUM(project_value) as total'))
->groupBy('project_client')
->orderByDesc('total')
->get();
556:         // Masing-masing kontrak per client
$data[$field] = strtoupper(trim($data[$field]));
$vendorContractsBreakdown = (clone $kontrakQuery)
->select('project_client', 'project_id', 'project_name', 'project_value')
->whereNotNull('project_client')
->orderBy('project_client')
->orderByDesc('project_value')
->get()
->groupBy('project_client');
565:         // === CHART 3: Realisasi Nilai Kontrak Vendor (Semua & Masing-Masing) ===
$pid = $k->project_id;
// Group by client
$realisasiPerClient = (clone $realisasiQuery)
->leftJoin('kontrak', 'realisasi.project_id', '=', 'kontrak.project_id')
->select('kontrak.project_client', DB::raw('SUM(realisasi.realisasi_biaya_final) as total'))
->whereNotNull('kontrak.project_client')
->groupBy('kontrak.project_client')
->orderByDesc('total')
->get();
575:         // Masing-masing kontrak per client (realisasi)
576:         // === CHART: Nilai Kontrak per Client/Vendor ===
$vendorRealisasiBreakdown = (clone $realisasiQuery)
->leftJoin('kontrak', 'realisasi.project_id', '=', 'kontrak.project_id')
->select('kontrak.project_client', 'realisasi.project_id', 'realisasi.project_name', DB::raw('SUM(realisasi.realisasi_biaya_final) as total'))
->whereNotNull('kontrak.project_client')
->groupBy('kontrak.project_client', 'realisasi.project_id', 'realisasi.project_name')
->orderBy('kontrak.project_client')
->orderByDesc('total')
->get()
->groupBy('project_client');
586:         // === CHART 4: Realisasi & Prognosa per Bulan ===
587:         // === FILTER DROPDOWNS ===
$periodeOrder = Realisasi::$periodeOrder;
$realisasiPerBulan = (clone $realisasiQuery)
->select('periode', DB::raw('SUM(realisasi_biaya_final) as total'))
->groupBy('periode')
->get()
->sortBy(fn($item) => $periodeOrder[$item->periode] ?? 99)
->values();
595:         $prognosaPerBulan = (clone $prognosaQuery)
596:         return view('dashboard.index', compact(
->select('periode', DB::raw('SUM(prognosa_biaya) as total'))
->groupBy('periode')
->get()
->sortBy(fn($item) => $periodeOrder[$item->periode] ?? 99)
->values();
602:         // === FILTER DROPDOWNS ===
'currentYear'
$projectNameList  = \App\Models\Kontrak::distinct()->orderBy('project_name')->whereNotNull('project_name')->pluck('project_name');
$serviceManagerList = \App\Models\Kontrak::whereNotNull('service_manager')->distinct()->pluck('service_manager')
->concat(Realisasi::whereNotNull('pic')->distinct()->pluck('pic'))
->unique()
->sort()
->values();
$clientList       = \App\Models\Kontrak::distinct()->orderBy('project_client')->whereNotNull('project_client')->pluck('project_client');
$periodeList      = collect($this->periodeList);
612:         // Transaksi terbaru
'stats', 'realisasiPerBulan', 'prognosaPerBulan',
$recentTransaksi = Realisasi::orderBy('updated_at', 'desc')->limit(8)->get();
615:         return view('dashboard.index', compact(
'projectNameList', 'serviceManagerList', 'clientList', 'periodeList',
'stats', 'realisasiPerBulan', 'prognosaPerBulan',
'smStats', 'nilaiKontrakPerClient', 'vendorContractsBreakdown',
'realisasiPerClient', 'vendorRealisasiBreakdown', 'recentTransaksi',
'projectNameList', 'serviceManagerList', 'clientList', 'periodeList',
'filterProject', 'filterSM', 'filterClient', 'filterPeriode',
// =========================================================================
623:     protected function validateRealisasi(Request $request, ?string $excludeId = null): array
// MISSING LINE 623
{
return $request->validate([
