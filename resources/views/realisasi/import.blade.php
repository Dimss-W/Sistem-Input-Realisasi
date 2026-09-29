@extends('layouts.main')

@section('title', 'Import Excel')
@section('page-title', 'Import Data Excel')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}" class="text-decoration-none">Data Realisasi</a></li>
    <li class="breadcrumb-item active">Import Excel</li>
@endsection

@section('content')

<div class="row g-3">

    {{-- Kolom Kiri: Form Upload --}}
    <div class="col-lg-7">

        {{-- Import Summary (jika ada) --}}
        @if(session('import_summary'))
            @php $sum = session('import_summary'); @endphp
            <div class="card mb-3" style="border-left: 4px solid
                {{ empty($sum['errors']) ? '#10B981' : '#F59E0B' }}">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="bi bi-{{ empty($sum['errors']) ? 'check-circle text-success' : 'exclamation-triangle text-warning' }} me-2"></i>
                        Hasil Import
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-sm-3">
                            <div class="text-center p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <div style="font-size: 1.8rem; font-weight: 800; color: #1E3A5F;">{{ $sum['total'] ?? 0 }}</div>
                                <div style="font-size: 0.7rem; color: #64748B;">Total Baris</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-center p-3 rounded" style="background: #D1FAE5; border: 1px solid #A7F3D0;">
                                <div style="font-size: 1.8rem; font-weight: 800; color: #065F46;">{{ $sum['inserted'] ?? 0 }}</div>
                                <div style="font-size: 0.7rem; color: #065F46;">Data Baru</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-center p-3 rounded" style="background: #FEF3C7; border: 1px solid #FCD34D;">
                                <div style="font-size: 1.8rem; font-weight: 800; color: #92400E;">{{ $sum['updated'] ?? 0 }}</div>
                                <div style="font-size: 0.7rem; color: #92400E;">Diperbarui</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-center p-3 rounded" style="background: #F3F4F6; border: 1px solid #E5E7EB;">
                                <div style="font-size: 1.8rem; font-weight: 800; color: #374151;">{{ $sum['skipped'] ?? 0 }}</div>
                                <div style="font-size: 0.7rem; color: #374151;">Dilewati</div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($sum['errors']))
                        <div class="alert alert-warning p-3 mb-0">
                            <div class="fw-semibold mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                {{ count($sum['errors']) }} Error Ditemukan:
                            </div>
                            <ul class="mb-0 ps-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($sum['errors'] as $err)
                                    <li style="font-size: 0.78rem;">{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @if(empty($sum['errors']))
                <div class="card-footer bg-transparent">
                    <a href="{{ route('realisasi.index') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-table me-1"></i> Lihat Data Realisasi
                    </a>
                </div>
                @endif
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="bi bi-x-circle-fill me-2"></i>
            @foreach($errors->all() as $err) {{ $err }} @endforeach
        </div>
        @endif

        {{-- Upload Form --}}
        <div class="card">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-upload text-success me-2"></i>Upload File Excel</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('realisasi.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    {{-- Drop Zone --}}
                    <div class="upload-zone mb-3" id="uploadZone" onclick="document.getElementById('fileInput').click()">
                        <div class="upload-icon">
                            <i class="bi bi-file-earmark-excel"></i>
                        </div>
                        <div class="fw-semibold mb-1" id="uploadText">Klik atau drag & drop file Excel</div>
                        <div class="text-muted" style="font-size: 0.78rem;">.xlsx, .xls — Maksimal 10MB</div>
                        <input type="file" id="fileInput" name="file"
                               accept=".xlsx,.xls,.csv"
                               class="d-none"
                               required>
                    </div>

                    {{-- Selected File Info --}}
                    <div id="fileInfo" class="alert alert-info d-none align-items-center gap-2 mb-3">
                        <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                        <div>
                            <div class="fw-semibold" id="fileName">—</div>
                            <div style="font-size: 0.75rem;" id="fileSize">—</div>
                        </div>
                    </div>

                    {{-- Info Panel --}}
                    <div class="alert alert-info p-3 mb-3">
                        <div class="fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i>Logika Import</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.78rem;">
                            <li>Jika data <strong>belum ada</strong> di database → <span class="badge bg-success">INSERT</span></li>
                            <li>Jika data <strong>sudah ada</strong> (record_key sama) → <span class="badge bg-warning text-dark">UPDATE</span></li>
                            <li><strong>Tidak</strong> menghapus data lama yang tidak ada di Excel</li>
                            <li>Proses menggunakan <strong>database transaction</strong> — aman dari korupsi data</li>
                        </ul>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success" id="btnImport" disabled>
                            <i class="bi bi-upload me-2"></i>Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Panduan & Template --}}
    <div class="col-lg-5">

        {{-- Download Template --}}
        <div class="card mb-3" style="border-left: 4px solid #10B981;">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-download text-success me-2"></i>Download Template</h6>
            </div>
            <div class="card-body">
                <p style="font-size: 0.82rem; color: var(--text-secondary);">
                    Gunakan template ini sebagai panduan format file Excel yang benar.
                    Template sudah menyertakan header yang diperlukan dan contoh data.
                </p>
                <a href="{{ route('realisasi.template') }}" class="btn btn-outline-success w-100">
                    <i class="bi bi-file-earmark-excel me-2"></i>
                    Download Template Excel
                </a>
            </div>
        </div>

        {{-- Panduan Kolom --}}
        <div class="card">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-list-check text-primary me-2"></i>Panduan Kolom</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size: 0.75rem;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th class="py-2 px-3">Kolom</th>
                                <th class="py-2 px-3">Wajib</th>
                                <th class="py-2 px-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $cols = [
                                ['Source_Row', false, 'Nomor baris Excel sumber'],
                                ['Project_ID', true, 'ID project (misal: PS-024-00)'],
                                ['Project_Name', true, 'Nama lengkap project'],
                                ['Item_Biaya', false, 'UPAH, MATERIAL, JASA, dll'],
                                ['Satuan_Kerja', false, 'DMO, dll'],
                                ['PIC', false, 'Nama penanggung jawab'],
                                ['Periode', true, 'JANUARI s/d DESEMBER'],
                                ['Realisasi_Biaya_Original', false, 'Angka, tanpa format'],
                                ['Currency', false, 'IDR (default), USD, dll'],
                                ['Realisasi_Biaya_IDR', false, 'Konversi ke IDR'],
                                ['Status', false, 'PAID, UNPAID, PENDING'],
                                ['Vendor', false, 'Nama vendor'],
                                ['Sifat', false, 'MONTHLY, YEARLY, dll'],
                                ['Link_Evidence', false, 'URL atau nama file'],
                                ['Tahun', true, 'Tahun (misal: 2026)'],
                                ['Data_Flag', false, 'Flag data opsional'],
                                ['Realisasi_Biaya_Final', false, 'Nilai final untuk Power BI'],
                            ];
                            @endphp
                            @foreach($cols as [$col, $wajib, $ket])
                            <tr>
                                <td class="py-1 px-3">
                                    <code style="font-size: 0.65rem;">{{ $col }}</code>
                                </td>
                                <td class="py-1 px-3 text-center">
                                    @if($wajib)
                                        <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem;">Wajib</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="py-1 px-3 text-muted">{{ $ket }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const fileInput   = document.getElementById('fileInput');
const uploadZone  = document.getElementById('uploadZone');
const fileInfo    = document.getElementById('fileInfo');
const fileName    = document.getElementById('fileName');
const fileSize    = document.getElementById('fileSize');
const btnImport   = document.getElementById('btnImport');
const uploadText  = document.getElementById('uploadText');

function handleFile(file) {
    if (!file) return;
    fileName.textContent = file.name;
    fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
    fileInfo.classList.remove('d-none');
    fileInfo.classList.add('d-flex');
    btnImport.disabled = false;
    uploadText.textContent = 'File dipilih: ' + file.name;
}

fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

// Drag & Drop
uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
uploadZone.addEventListener('drop', e => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        handleFile(file);
    }
});

// Loading on submit
document.getElementById('importForm')?.addEventListener('submit', function() {
    btnImport.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sedang mengimport...';
    btnImport.disabled = true;
});
</script>
@endpush
