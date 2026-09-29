@extends('layouts.main')

@section('title', 'Import Data Pembayaran')
@section('page-title', 'Import Pembayaran Excel')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}" class="text-decoration-none">Invoice</a></li>
    <li class="breadcrumb-item active">Import Pembayaran</li>
@endsection

@section('content')
<div class="row g-3">
    <!-- Main Upload Panel -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Upload File Pembayaran</h5>
                <p class="text-muted small mb-0 mt-1">Gunakan file template resmi untuk melakukan upload massal data pembayaran.</p>
            </div>

            <div class="card-body p-4">
                @if($errors->any() && !session('success'))
                    <div class="alert alert-danger alert-dismissible fade show p-4 mb-4" role="alert">
                        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ditemukan Galat Data:</h6>
                        <ul class="mb-0 ps-3 small" style="max-height: 150px; overflow-y: auto;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('payment.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="upload-zone mb-4" id="dropZone" onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-cloud-arrow-up-fill upload-icon"></i>
                        <h5 class="fw-bold text-dark mb-1">Tarik & Lepas file Excel di sini</h5>
                        <p class="text-muted small mb-3">Atau klik untuk menelusuri file dari komputer Anda</p>
                        <span class="badge bg-light text-secondary border px-3 py-2" id="fileNameBadge" style="font-size:0.8rem; display:none;"></span>
                        
                        <input type="file" name="file" id="fileInput" class="d-none" accept=".xlsx, .xls" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-top pt-4">
                        <a href="{{ route('payment.template') }}" class="btn btn-outline-success fw-bold rounded-3 d-flex align-items-center gap-1">
                            <i class="bi bi-file-earmark-excel-fill"></i> Download Template Pembayaran
                        </a>
                        <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4" id="submitBtn" disabled>
                            <i class="bi bi-cloud-check-fill me-1"></i> Mulai Proses Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History Panel -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Import Pembayaran</h5>
                <p class="text-muted small mb-0 mt-1">10 transaksi upload file pembayaran terakhir oleh Finance.</p>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
                        <thead class="table-light text-uppercase text-secondary fs-8 fw-bold border-bottom">
                            <tr>
                                <th class="py-3 ps-4">Tanggal & File</th>
                                <th class="py-3 text-center">Status / Baris</th>
                                <th class="py-3 text-center">Uploader</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 150px;" title="{{ $log->file_name }}">{{ $log->file_name }}</div>
                                        <span class="text-muted fs-8">{{ $log->imported_at ? $log->imported_at->format('d/m/Y H:i') : '—' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($log->error_rows > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size:0.65rem;">
                                                FAILED ({{ $log->error_rows }})
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size:0.65rem;">
                                                SUCCESS ({{ $log->success_rows }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-secondary small">{{ $log->user ? $log->user->name : 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-clock fs-2 d-block mb-1"></i> Belum ada riwayat upload.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 50px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.02);
    }
    .upload-zone .upload-icon {
        font-size: 3.5rem;
        color: #22c55e;
        margin-bottom: 12px;
        display: block;
    }
    .fs-8 { font-size: 0.75rem; }
</style>
@endpush

@push('scripts')
<script>
    const fileInput = document.getElementById('fileInput');
    const dropZone = document.getElementById('dropZone');
    const fileNameBadge = document.getElementById('fileNameBadge');
    const submitBtn = document.getElementById('submitBtn');

    // Drag events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
        }, false);
    });

    dropZone.addEventListener('drop', e => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            fileInput.files = files;
            updateFileName(files[0].name);
        }
    }, false);

    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            updateFileName(this.files[0].name);
        }
    });

    function updateFileName(name) {
        fileNameBadge.innerText = name;
        fileNameBadge.style.display = 'inline-block';
        submitBtn.disabled = false;
    }
</script>
@endpush
