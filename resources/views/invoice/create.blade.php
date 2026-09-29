@extends('layouts.main')

@section('title', 'Input Invoice Vendor Baru')
@section('page-title', 'Input Invoice Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}" class="text-decoration-none">Invoice Vendor</a></li>
    <li class="breadcrumb-item active">Input Baru</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                <i class="bi bi-receipt text-primary me-2"></i>Input Invoice Vendor Baru
            </h4>
            <p class="text-muted small mb-0">
                Pencatatan faktur tagihan dari rekanan/penyedia jasa barang &amp; kalkulasi perpajakan proyek.
            </p>
        </div>
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    @if(isset($selectedBasto) && $selectedBasto)
        <div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 rounded-3 d-flex align-items-center mb-4 py-2.5 px-3">
            <i class="bi bi-info-circle-fill text-primary fs-5 me-2.5"></i>
            <div class="small">
                Terhubung dengan <strong>BASTO #{{ $selectedBasto->basto_number ?? '-' }}</strong> untuk Project <strong>{{ $selectedBasto->project_id }}</strong>. Data proyek telah disinkronkan.
            </div>
        </div>
    @endif

    <form action="{{ route('invoice.store') }}" method="POST" id="formInvoiceVendor">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Form Detail Tagihan -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-card-text text-primary me-2"></i>Informasi Utama Tagihan
                        </span>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">
                            WAJIB DIISI LENGKAP
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Nomor Invoice Vendor -->
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label fw-semibold text-secondary small mb-1">
                                    Nomor Invoice Vendor <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}" 
                                           class="form-control border-start-0 @error('invoice_number') is-invalid @enderror" 
                                           placeholder="Contoh: INV/VND/2026/001" required>
                                </div>
                                @error('invoice_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Project ID -->
                            <div class="col-md-6">
                                <label for="project_id" class="form-label fw-semibold text-secondary small mb-1">
                                    Proyek Terkait (Project ID) <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id" class="form-select select2-enable @error('project_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ !old('project_id', request('project_id', $selectedBasto->project_id ?? '')) ? 'selected' : '' }}>-- Pilih Kode Proyek --</option>
                                    @foreach($projects as $pid => $pname)
                                        @php
                                            $cData = isset($kontraksMap[$pid]) ? $kontraksMap[$pid] : null;
                                            $clientName = $cData ? $cData->project_client : '';
                                            $val = $cData ? $cData->project_value : 0;
                                            $isSelected = old('project_id', request('project_id', $selectedBasto->project_id ?? '')) === $pid;
                                        @endphp
                                        <option value="{{ $pid }}" 
                                            data-customer="{{ $clientName }}"
                                            data-value="{{ $val }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $pid }} &bull; {{ Str::limit($pname, 45) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Nama Penyedia / Vendor -->
                            <div class="col-md-7">
                                <label for="customer" class="form-label fw-semibold text-secondary small mb-1">
                                    Penyedia / Nama Vendor <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="customer" id="customer" list="vendorDatalist" value="{{ old('customer') }}" 
                                           class="form-control border-start-0 @error('customer') is-invalid @enderror" 
                                           placeholder="Ketik atau pilih nama vendor rekanan..." required>
                                </div>
                                <datalist id="vendorDatalist">
                                    @if(isset($vendorList))
                                        @foreach($vendorList as $vnd)
                                            <option value="{{ $vnd }}">
                                        @endforeach
                                    @endif
                                </datalist>
                                @error('customer')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Admin Pencatat -->
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-secondary small mb-1">Petugas Pencatat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-check"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light text-muted" value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>

                            <!-- Tanggal Invoice & Jatuh Tempo -->
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label fw-semibold text-secondary small mb-1">
                                    Tanggal Terbit Invoice <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" 
                                       class="form-control @error('invoice_date') is-invalid @enderror" required>
                                @error('invoice_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-semibold text-secondary small mb-1">
                                    Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                                       class="form-control @error('due_date') is-invalid @enderror" required>
                                @error('due_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Nilai Pokok (DPP), PPN, PPh 23 -->
                            <div class="col-md-6">
                                <label for="subtotal" class="form-label fw-semibold text-secondary small mb-1">
                                    Nilai Pokok / DPP (Subtotal) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                    <input type="number" name="subtotal" id="subtotal" value="{{ old('subtotal') }}" 
                                           class="form-control font-monospace fw-bold @error('subtotal') is-invalid @enderror" 
                                           placeholder="10000000" required>
                                </div>
                                @error('subtotal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="tax_ppn_percent" class="form-label fw-semibold text-secondary small mb-1">Tarif PPN</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tax_ppn_percent" id="tax_ppn_percent" 
                                           value="{{ old('tax_ppn_percent', 11.00) }}" class="form-control text-center font-monospace">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="tax_pph_percent" class="form-label fw-semibold text-secondary small mb-1">Tarif PPh 23</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tax_pph_percent" id="tax_pph_percent" 
                                           value="{{ old('tax_pph_percent', 2.00) }}" class="form-control text-center font-monospace">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                            </div>

                            <!-- Catatan Pekerjaan -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold text-secondary small mb-1">Rincian Pengadaan / Catatan Tagihan</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Uraian barang/jasa yang ditagihkan oleh vendor...">{{ old('notes', isset($selectedBasto) && $selectedBasto ? ('Penagihan termin BASTO #' . ($selectedBasto->basto_number ?? '-') . ' - Proyek ' . ($selectedBasto->project_id)) : '') }}</textarea>
                                @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Upload Box, Ringkasan & Action -->
            <div class="col-lg-4">
                <!-- Dropzone Box (Simpel & Rapi) -->
                <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 text-center" style="background: #F8FAFC; border: 1.5px dashed #CBD5E1 !important;" id="dropzoneContainer">
                    <input type="file" id="invoiceFile" class="d-none" accept=".pdf,.xls,.xlsx,.doc,.docx,.png,.jpg,.jpeg">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-2 rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 44px; height: 44px;">
                        <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 fs-7">Upload / Scan Dokumen</h6>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Klik atau tarik file PDF / Foto invoice vendor ke sini</p>
                    <button type="button" class="btn btn-xs btn-outline-primary rounded-2 px-3 py-1 fw-semibold mx-auto" onclick="document.getElementById('invoiceFile').click()">
                        Pilih Berkas
                    </button>
                    <div id="uploadedFileName" class="text-success small fw-semibold mt-2 d-none">
                        <i class="bi bi-check-circle-fill me-1"></i><span id="fileNameLabel"></span>
                    </div>
                </div>

                <!-- Scanner Progress (Hidden by default) -->
                <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-dark text-white d-none" id="scannerOverlay">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="small fw-semibold">Memindai Berkas Invoice...</span>
                    </div>
                    <div class="progress bg-secondary bg-opacity-50 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 0%;" id="scanProgressBar"></div>
                    </div>
                    <span class="text-white-50 mt-1 d-block" style="font-size: 0.7rem;" id="scanStepText">Menganalisis teks faktur...</span>
                </div>

                <!-- Ringkasan Finansial Tagihan (Live Calculation) -->
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom">
                        <span class="fw-bold text-dark small">
                            <i class="bi bi-calculator text-primary me-1"></i>Ringkasan Finansial Tagihan
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">Subtotal / DPP</span>
                            <span class="font-monospace fw-semibold text-dark" id="summarySubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">PPN (<span id="summaryPpnPct">11</span>%)</span>
                            <span class="font-monospace text-info fw-semibold" id="summaryPpnVal">+ Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 small">
                            <span class="text-muted">Potongan PPh 23 (<span id="summaryPphPct">2</span>%)</span>
                            <span class="font-monospace text-danger fw-semibold" id="summaryPphVal">- Rp 0</span>
                        </div>

                        <div class="border-top pt-2.5">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                                Total Tagihan Bersih (Payable)
                            </div>
                            <div class="h4 fw-bold text-primary font-monospace mb-0 mt-1" id="summaryTotal">
                                Rp 0
                            </div>
                            <input type="hidden" name="invoice_amount" id="invoice_amount" value="{{ old('invoice_amount', 0) }}">
                        </div>
                    </div>
                </div>

                <!-- Action Button Card -->
                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="bi bi-check2-circle fs-5"></i> Simpan Invoice Vendor
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-light border w-100 py-1.5 fw-semibold text-secondary small">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('invoiceFile');
        const dropzone = document.getElementById('dropzoneContainer');
        const scannerOverlay = document.getElementById('scannerOverlay');
        const scanProgressBar = document.getElementById('scanProgressBar');
        const scanStepText = document.getElementById('scanStepText');
        const uploadedFileName = document.getElementById('uploadedFileName');
        const fileNameLabel = document.getElementById('fileNameLabel');

        // Inputs for live tax calculation
        const subtotalInput = document.getElementById('subtotal');
        const ppnInput = document.getElementById('tax_ppn_percent');
        const pphInput = document.getElementById('tax_pph_percent');
        const totalHidden = document.getElementById('invoice_amount');

        // Summary labels
        const summarySubtotal = document.getElementById('summarySubtotal');
        const summaryPpnPct = document.getElementById('summaryPpnPct');
        const summaryPpnVal = document.getElementById('summaryPpnVal');
        const summaryPphPct = document.getElementById('summaryPphPct');
        const summaryPphVal = document.getElementById('summaryPphVal');
        const summaryTotal = document.getElementById('summaryTotal');

        function updateCalculation() {
            const subtotal = parseFloat(subtotalInput.value) || 0;
            const ppn = parseFloat(ppnInput.value) || 0;
            const pph = parseFloat(pphInput.value) || 0;

            const ppnVal = Math.round(subtotal * (ppn / 100));
            const pphVal = Math.round(subtotal * (pph / 100));
            const total = Math.max(0, subtotal + ppnVal - pphVal);

            totalHidden.value = total > 0 ? total : subtotal;

            summarySubtotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            summaryPpnPct.textContent = ppn;
            summaryPpnVal.textContent = '+ Rp ' + ppnVal.toLocaleString('id-ID');
            summaryPphPct.textContent = pph;
            summaryPphVal.textContent = '- Rp ' + pphVal.toLocaleString('id-ID');
            summaryTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        if (subtotalInput) subtotalInput.addEventListener('input', updateCalculation);
        if (ppnInput) ppnInput.addEventListener('input', updateCalculation);
        if (pphInput) pphInput.addEventListener('input', updateCalculation);

        // Run calculation once on page load
        updateCalculation();

        // Drag and Drop & File Upload handling
        if (dropzone && fileInput) {
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#3B82F6';
                dropzone.style.background = '#EFF6FF';
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.style.borderColor = '#CBD5E1';
                dropzone.style.background = '#F8FAFC';
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#CBD5E1';
                dropzone.style.background = '#F8FAFC';
                if (e.dataTransfer.files.length > 0) {
                    processFile(e.dataTransfer.files[0]);
                }
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    processFile(fileInput.files[0]);
                }
            });
        }

        function processFile(file) {
            fileNameLabel.textContent = file.name;
            uploadedFileName.classList.remove('d-none');

            // Show scanner progress
            scannerOverlay.classList.remove('d-none');
            let progress = 0;
            const steps = [
                'Membaca berkas faktur tagihan...',
                'Mendeteksi nomor invoice dan nama vendor...',
                'Mengekstrak nilai DPP dan tarif pajak...',
                'Menyinkronkan ke formulir...'
            ];

            const interval = setInterval(() => {
                progress += 25;
                scanProgressBar.style.width = `${progress}%`;
                const stepIdx = Math.min(Math.floor(progress / 25), steps.length - 1);
                scanStepText.textContent = steps[stepIdx];

                if (progress >= 100) {
                    clearInterval(interval);
                    
                    // AJAX Call to scan endpoint
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route("invoice.scan") }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        scannerOverlay.classList.add('d-none');
                        if (res.success && res.data) {
                            const d = res.data;
                            if (d.invoice_number) document.getElementById('invoice_number').value = d.invoice_number;
                            if (d.customer) document.getElementById('customer').value = d.customer;
                            if (d.project_id) {
                                document.getElementById('project_id').value = d.project_id;
                                if (typeof $ !== 'undefined') $('#project_id').val(d.project_id).trigger('change');
                            }
                            if (d.invoice_date) document.getElementById('invoice_date').value = d.invoice_date;
                            if (d.due_date) document.getElementById('due_date').value = d.due_date;
                            if (d.amount) {
                                subtotalInput.value = d.amount;
                                updateCalculation();
                            }
                            if (d.notes) document.getElementById('notes').value = d.notes;

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Faktur Berhasil Terbaca',
                                    text: 'Data dari berkas telah dipindahkan ke formulir.',
                                    timer: 2500,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false
                                });
                            }
                        }
                    })
                    .catch(() => {
                        scannerOverlay.classList.add('d-none');
                    });
                }
            }, 200);
        }
    });
</script>
@endpush
