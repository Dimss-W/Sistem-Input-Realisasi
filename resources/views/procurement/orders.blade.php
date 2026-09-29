@extends('layouts.main')

@section('title', 'Surat Pesanan & PO / SPK Pengadaan')

@section('content')
<div class="container-fluid px-3 py-3 page-entrance">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-file-earmark-ruled-fill text-primary me-2"></i>PO / SPK Pengadaan
                </h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                    Purchase Orders Management
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Penerbitan surat pesanan, komitmen biaya pengadaan barang/jasa, dan pelacakan status pemenuhan kontrak vendor.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('procurement.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Pengadaan
            </a>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePO">
                <i class="bi bi-plus-circle me-1"></i> Terbitkan PO Baru
            </button>
        </div>
    </div>

    <!-- Alert status -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan penginputan:
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('procurement.orders') }}" class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari No. PO, judul, vendor..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2 col-sm-6">
                    <select name="status" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Status --</option>
                        <option value="issued" {{ ($filters['status'] ?? '') == 'issued' ? 'selected' : '' }}>Issued (Diterbitkan)</option>
                        <option value="in_progress" {{ ($filters['status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="basto_verified" {{ ($filters['status'] ?? '') == 'basto_verified' ? 'selected' : '' }}>BASTO Verified</option>
                        <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Project Filter -->
                <div class="col-md-3 col-sm-6">
                    <select name="project_id" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Proyek --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->project_id }}" {{ ($filters['project_id'] ?? '') == $p->project_id ? 'selected' : '' }}>
                                {{ $p->project_id }} - {{ Str::limit($p->project_name, 22) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vendor Filter -->
                <div class="col-md-3 col-sm-6">
                    <select name="vendor" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Vendor --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v }}" {{ ($filters['vendor'] ?? '') == $v ? 'selected' : '' }}>{{ Str::limit($v, 25) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Button -->
                <div class="col-md-1 col-sm-12 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                        <i class="bi bi-filter"></i>
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['project_id']) || !empty($filters['vendor']))
                        <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Orders -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">
                <i class="bi bi-table text-primary me-1"></i> Data Surat Pesanan Pengadaan
            </span>
            <span class="text-muted small">
                Menampilkan {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} PO
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>No. PO / SPK</th>
                        <th>Proyek</th>
                        <th>Vendor Penerima</th>
                        <th>Pengadaan / Uraian</th>
                        <th class="text-end">Nilai Komitmen PO</th>
                        <th class="text-center">Tgl Terbit</th>
                        <th class="text-center">Termin (TOP) &amp; Jatuh Tempo</th>
                        <th class="text-center">Status PO</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $po)
                    <tr>
                        <td class="ps-3 text-muted">{{ $orders->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-primary">{{ $po->po_number }}</div>
                            @if($po->contract_file)
                                <a href="{{ asset('storage/' . $po->contract_file) }}" target="_blank" class="text-decoration-none small text-muted" style="font-size: 0.7rem;">
                                    <i class="bi bi-paperclip text-danger"></i> Unduh PDF
                                </a>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $po->project_id }}</span>
                            <div class="text-muted small" style="font-size: 0.7rem;">{{ Str::limit($po->project_name, 20) }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $po->vendor_name }}</div>
                        </td>
                        <td>
                            <div class="text-dark">{{ Str::limit($po->po_title, 35) }}</div>
                            @if($po->delivery_deadline)
                                <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-calendar-event me-1"></i>Target: {{ \Carbon\Carbon::parse($po->delivery_deadline)->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($po->po_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center text-muted small">
                            {{ \Carbon\Carbon::parse($po->order_date)->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-semibold" style="font-size: 0.72rem;">
                                TOP {{ $po->term_of_payment ?? 30 }} Hari
                            </span>
                            @if($po->payment_due_date)
                                <div class="text-primary small fw-semibold mt-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-clock-history me-0.5"></i>{{ \Carbon\Carbon::parse($po->payment_due_date)->format('d/m/Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.65rem;">Prognosa: {{ $po->prognosa_periode }} {{ $po->prognosa_tahun }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $po->status_badge }}" style="font-size: 0.72rem;">
                                {{ $po->status_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('procurement.verification') }}" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size: 0.72rem;" title="3-Way Match Check">
                                    <i class="bi bi-shield-check"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-2 text-muted mb-2 d-block"></i>
                            Tidak ada data Surat Pesanan (PO) yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}</span>
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Terbitkan PO Baru -->
<div class="modal fade" id="modalCreatePO" tabindex="-1" aria-labelledby="modalCreatePOLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('procurement.orders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold text-dark" id="modalCreatePOLabel">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Penerbitan Surat Pesanan (PO / SPK) Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Nomor PO -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nomor PO / SPK Pengadaan <span class="text-danger">*</span></label>
                            <input type="text" name="po_number" class="form-control form-control-sm" placeholder="Masukkan Nomor PO Resmi (Contoh: PO/2026/09/PROC-001)" value="{{ old('po_number') }}" required>
                        </div>

                        <!-- Proyek -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Proyek Terkait <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Proyek --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->project_id }}" {{ old('project_id') == $p->project_id ? 'selected' : '' }}>
                                        {{ $p->project_id }} - {{ Str::limit($p->project_name, 35) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vendor -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Rekanan / Vendor <span class="text-danger">*</span></label>
                            <input list="vendorListOptions" name="vendor_name" class="form-control form-control-sm" placeholder="Ketik atau pilih vendor..." value="{{ old('vendor_name') }}" required>
                            <datalist id="vendorListOptions">
                                @foreach($vendors as $v)
                                    <option value="{{ $v }}">
                                @endforeach
                            </datalist>
                        </div>

                        <!-- Nilai Komitmen PO -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nilai Komitmen PO (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="po_amount" class="form-control form-control-sm" placeholder="Contoh: 75000000" value="{{ old('po_amount') }}" required min="0">
                        </div>

                        <!-- Judul Pengadaan -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Judul / Perihal Pengadaan <span class="text-danger">*</span></label>
                            <input type="text" name="po_title" class="form-control form-control-sm" placeholder="Contoh: Pengadaan Hardware Switch dan Fiber Optic Ruang Server" value="{{ old('po_title') }}" required>
                        </div>

                        <!-- Tanggal Order & Deadline -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tanggal Terbit Order <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" id="poOrderDateInput" class="form-control form-control-sm" value="{{ old('order_date', date('Y-m-d')) }}" required>
                        </div>

                        <!-- Termin Pembayaran (TOP) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Termin Pembayaran / TOP (Hari) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="poTopInput" name="term_of_payment" class="form-control" placeholder="Contoh: 120" value="{{ old('term_of_payment', 30) }}" required min="1" max="365">
                                <span class="input-group-text bg-light">Hari</span>
                            </div>
                            <div class="d-flex gap-1 mt-1">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5" style="font-size: 0.68rem;" onclick="setTop(30)">30 Hari</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5" style="font-size: 0.68rem;" onclick="setTop(60)">60 Hari</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1.5" style="font-size: 0.68rem;" onclick="setTop(90)">90 Hari</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1.5 fw-bold" style="font-size: 0.68rem;" onclick="setTop(120)">120 Hari</button>
                            </div>
                        </div>

                        <!-- Live Preview Kalkulator TOP & Prognosa -->
                        <div class="col-12">
                            <div class="p-2.5 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary small d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <i class="bi bi-calculator me-1"></i><strong>Kalkulator Prognosa Jatuh Tempo:</strong>
                                    <span id="topPreviewText" class="text-dark ms-1">Menghitung perkiraan...</span>
                                </div>
                                <span class="badge bg-primary text-white py-1 px-2" id="topBadgeMonth">Sinkron ke Prognosa</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Target Waktu Pengiriman / Selesai (Opsional)</label>
                            <input type="date" name="delivery_deadline" class="form-control form-control-sm" value="{{ old('delivery_deadline') }}">
                        </div>

                        <!-- File Dokumen Kontrak PDF -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Upload Dokumen Kontrak / SPK Fisik (PDF)</label>
                            <input type="file" name="contract_file" class="form-control form-control-sm" accept=".pdf">
                            <span class="text-muted" style="font-size: 0.7rem;">Maksimal ukuran file: 10 MB (Format PDF).</span>
                        </div>

                        <!-- Uraian Spesifikasi / Catatan -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Spesifikasi Detail / Catatan Pengadaan</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Rincian item barang/jasa, terms of delivery, dsb...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan &amp; Terbitkan PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setTop(days) {
        const topInput = document.getElementById('poTopInput');
        if (topInput) {
            topInput.value = days;
            updateTopPreview();
        }
    }

    function updateTopPreview() {
        const orderDateInput = document.getElementById('poOrderDateInput');
        const topInput = document.getElementById('poTopInput');
        const previewText = document.getElementById('topPreviewText');
        const badgeMonth = document.getElementById('topBadgeMonth');

        if (!orderDateInput || !topInput || !orderDateInput.value) return;

        const parts = orderDateInput.value.split('-');
        if (parts.length !== 3) return;

        const orderDate = new Date(parts[0], parts[1] - 1, parts[2]);
        const topDays = parseInt(topInput.value) || 30;

        const dueDate = new Date(orderDate);
        dueDate.setDate(dueDate.getDate() + topDays);

        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        const day = String(dueDate.getDate()).padStart(2, '0');
        const month = monthNames[dueDate.getMonth()];
        const year = dueDate.getFullYear();

        if (previewText) {
            previewText.innerHTML = `Estimasi Jatuh Tempo: <strong>${day} ${month} ${year}</strong> (TOP ${topDays} hari kalender)`;
        }
        if (badgeMonth) {
            badgeMonth.innerText = `Masuk Prognosa: ${month.toUpperCase()} ${year}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const orderDateInput = document.getElementById('poOrderDateInput');
        const topInput = document.getElementById('poTopInput');

        if (orderDateInput) orderDateInput.addEventListener('change', updateTopPreview);
        if (topInput) topInput.addEventListener('input', updateTopPreview);

        updateTopPreview();
    });
</script>
@endpush
