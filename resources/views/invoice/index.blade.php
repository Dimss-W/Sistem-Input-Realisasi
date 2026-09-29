@extends('layouts.main')

@section('title', 'Monitoring & Kelola Invoice Vendor')
@section('page-title', 'Invoice Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item active">Invoice Vendor</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                <i class="bi bi-receipt-cutoff text-primary me-2"></i>Monitoring &amp; Kelola Invoice Vendor
            </h4>
            <p class="text-muted small mb-0">
                Pengawasan faktur tagihan vendor/rekanan penyedia barang &amp; jasa, status pembayaran, serta perpajakan proyek.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light border btn-density-toggle fw-semibold shadow-xs" 
                    style="border-radius:7px; background:#FFFFFF; color:#475569;" 
                    onclick="toggleTableDensity('table')" 
                    title="Beralih antara tampilan baris rapat (compact) atau luas (comfortable)">
                <i class="bi bi-arrows-collapse me-1"></i>Tampilan Rapat
            </button>
            @if(auth()->user()->hasRole(['admin', 'procurement', 'dmo']))
                <a href="{{ route('invoice.export', request()->query()) }}" class="btn btn-sm btn-outline-success rounded-3 px-3 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5" title="Export Rekap ke Excel">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                </a>
            @endif
            @if(auth()->user()->hasRole(['admin', 'procurement']))
                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#importPaymentModal">
                    <i class="bi bi-upload"></i> Import Pembayaran
                </button>
                <a href="{{ route('invoice.create') }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-plus-circle-fill"></i> + Input Invoice Vendor
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Metric Strip (Clean & Minimalist) -->
    @php
        $baseQuery = App\Models\Invoice::query();
        $totalInvAmount = (clone $baseQuery)->sum('invoice_amount');
        $totalPayAmount = (clone $baseQuery)->sum('payment_amount');
        $totalOutstanding = max(0, $totalInvAmount - $totalPayAmount);
        $paidCount = (clone $baseQuery)->where('payment_status', 'paid')->count();
        $unpaidCount = (clone $baseQuery)->where('payment_status', 'unpaid')->count();
        $partialCount = (clone $baseQuery)->where('payment_status', 'partial')->count();
        $overdueCount = (clone $baseQuery)->where('payment_status', '!=', 'paid')->where('due_date', '<', now()->startOfDay())->count();
    @endphp

    <div class="row g-3 mb-4">
        <!-- 1. Total Tagihan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Tagihan Masuk</span>
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 34px; height: 34px;">
                        <i class="bi bi-receipt fs-6"></i>
                    </div>
                </div>
                <div class="h5 fw-bold text-dark font-monospace mb-1">
                    Rp {{ number_format($totalInvAmount, 0, ',', '.') }}
                </div>
                <span class="text-muted small" style="font-size: 0.75rem;">
                    Total seluruh tagihan rekanan
                </span>
            </div>
        </div>

        <!-- 2. Terbayar Lunas -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Sudah Dibayarkan</span>
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px;">
                        <i class="bi bi-check2-all fs-6"></i>
                    </div>
                </div>
                <div class="h5 fw-bold text-success font-monospace mb-1">
                    Rp {{ number_format($totalPayAmount, 0, ',', '.') }}
                </div>
                <span class="text-muted small" style="font-size: 0.75rem;">
                    <strong>{{ $paidCount }}</strong> lunas &bull; <strong>{{ $partialCount }}</strong> sebagian
                </span>
            </div>
        </div>

        <!-- 3. Sisa Belum Bayar -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Sisa Belum Dibayar</span>
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning" style="width: 34px; height: 34px;">
                        <i class="bi bi-hourglass-split fs-6"></i>
                    </div>
                </div>
                <div class="h5 fw-bold text-dark font-monospace mb-1">
                    Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                </div>
                <span class="text-muted small" style="font-size: 0.75rem;">
                    <strong>{{ $unpaidCount }}</strong> invoice belum diselesaikan
                </span>
            </div>
        </div>

        <!-- 4. Menunggak / Overdue -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Jatuh Tempo (Overdue)</span>
                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center text-danger" style="width: 34px; height: 34px;">
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    </div>
                </div>
                <div class="h5 fw-bold text-danger font-monospace mb-1">
                    {{ $overdueCount }} Tagihan
                </div>
                <span class="text-muted small" style="font-size: 0.75rem;">
                    Melewati batas tempo pembayaran
                </span>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <!-- Quick Tab Header -->
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                @php
                    $activeTab = $filters['quick_tab'] ?? 'all';
                @endphp
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    <a href="{{ route('invoice.index', array_merge(request()->except(['quick_tab', 'page']), ['quick_tab' => 'all'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-semibold {{ $activeTab === 'all' || empty($activeTab) ? 'btn-primary shadow-2xs' : 'btn-light text-secondary' }}" style="font-size: 0.78rem;">
                       Semua Tagihan
                    </a>
                    <a href="{{ route('invoice.index', array_merge(request()->except(['quick_tab', 'page']), ['quick_tab' => 'overdue'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-semibold {{ $activeTab === 'overdue' ? 'btn-danger shadow-2xs' : 'btn-light text-danger' }}" style="font-size: 0.78rem;">
                       <i class="bi bi-clock-history me-1"></i>Menunggak (Overdue)
                    </a>
                    <a href="{{ route('invoice.index', array_merge(request()->except(['quick_tab', 'page']), ['quick_tab' => 'due_soon'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-semibold {{ $activeTab === 'due_soon' ? 'btn-warning text-dark shadow-2xs' : 'btn-light text-dark' }}" style="font-size: 0.78rem;">
                       <i class="bi bi-alarm me-1"></i>Jatuh Tempo &le;7 Hari
                    </a>
                    <a href="{{ route('invoice.index', array_merge(request()->except(['quick_tab', 'page']), ['quick_tab' => 'paid'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-semibold {{ $activeTab === 'paid' ? 'btn-success shadow-2xs' : 'btn-light text-success' }}" style="font-size: 0.78rem;">
                       <i class="bi bi-check2-circle me-1"></i>Lunas (Paid)
                    </a>
                </div>

                <span class="text-muted small">
                    Menampilkan <strong>{{ $invoices->total() }}</strong> faktur tagihan
                </span>
            </div>

            <!-- Filter Inputs Row -->
            <form action="{{ route('invoice.index') }}" method="GET" class="row g-2 align-items-center mt-3 pt-3 border-top">
                @if(request('quick_tab'))
                    <input type="hidden" name="quick_tab" value="{{ request('quick_tab') }}">
                @endif
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" class="form-control border-start-0" placeholder="Cari nomor invoice, nama vendor, atau proyek...">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="payment_status" id="payment_status" class="form-select form-select-sm">
                        <option value="">-- Semua Status Pembayaran --</option>
                        <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>UNPAID (Belum Bayar)</option>
                        <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>PARTIAL (Sebagian)</option>
                        <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>PAID (Lunas)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="project_id" id="project_id" class="form-select form-select-sm select2-enable">
                        <option value="">-- Semua Kode Proyek --</option>
                        @foreach($projectIds as $pid)
                            <option value="{{ $pid }}" {{ ($filters['project_id'] ?? '') === $pid ? 'selected' : '' }}>{{ $pid }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1.5">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    @if(array_filter($filters))
                        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-light border text-secondary" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="table-responsive" style="min-height: 280px;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-secondary text-uppercase fw-bold border-bottom" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                    <tr>
                        <th class="ps-4 py-3 text-nowrap" style="width: 45px;">No</th>
                        <th class="py-3 text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Nomor resmi faktur/invoice yang diterbitkan penyedia">
                                Nomor Invoice Vendor <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="py-3 text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Kode proyek & nama pekerjaan penerima jasa">
                                Proyek Terkait <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="py-3 text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Perusahaan vendor/rekanan penagih">
                                Penyedia / Vendor <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="py-3 text-nowrap">Tgl Terbit</th>
                        <th class="py-3 text-end text-nowrap" data-bs-toggle="tooltip" title="Nominal kotor tagihan dalam invoice">Total Tagihan</th>
                        <th class="py-3 text-end text-nowrap" data-bs-toggle="tooltip" title="Total dana yang telah dicairkan/dibayar">Terbayar</th>
                        <th class="py-3 text-end text-nowrap" data-bs-toggle="tooltip" title="Sisa kewajiban pembayaran yang belum dilunasi">Sisa Tagihan</th>
                        <th class="py-3 text-center text-nowrap">
                            <span class="th-content justify-content-center" data-bs-toggle="tooltip" title="Status pelunasan: UNPAID (Belum Bayar), PARTIAL (Sebagian), PAID (Lunas)">
                                Status <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="py-3 pe-4 text-end text-nowrap" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $index => $inv)
                        <tr>
                            <td class="ps-4 text-muted">{{ $invoices->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $inv->invoice_number }}</div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    <span class="text-muted" style="font-size: 0.72rem;">
                                        Due: {{ $inv->due_date ? $inv->due_date->format('d/m/Y') : '-' }}
                                    </span>
                                    @if($inv->isOverdue())
                                        <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.62rem;">OVERDUE</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="project-code">{{ $inv->project_id }}</code>
                                <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $inv->project_name }}">
                                    {{ $inv->project_name }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $inv->customer ?? '—' }}</div>
                            </td>
                            <td class="text-secondary text-nowrap">
                                {{ $inv->invoice_date ? $inv->invoice_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-end fw-bold font-monospace text-dark">
                                {{ $inv->invoice_amount_formatted }}
                            </td>
                            <td class="text-end font-monospace text-success fw-semibold">
                                {{ $inv->payment_amount_formatted }}
                                @if(($inv->pph23_deducted ?? 0) > 0)
                                    <div class="text-muted" style="font-size: 0.68rem;" title="Potongan PPh 23">
                                        PPh: Rp {{ number_format($inv->pph23_deducted, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold {{ $inv->remaining_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $inv->outstanding_formatted }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeMap = [
                                        'paid' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'partial' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25',
                                        'unpaid' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'
                                    ];
                                    $badgeClass = $badgeMap[$inv->payment_status] ?? 'bg-secondary bg-opacity-10 text-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.68rem;">
                                    {{ strtoupper($inv->payment_status) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1">
                                    @if(auth()->user()->hasRole(['admin', 'procurement']) && $inv->payment_status !== 'paid')
                                        <button type="button" class="btn btn-xs btn-outline-success fw-semibold rounded-2 px-2 py-1" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $inv->id }}">
                                            <i class="bi bi-wallet2 me-0.5"></i> Bayar
                                        </button>
                                    @endif

                                    @if(auth()->user()->hasRole(['admin', 'procurement']))
                                        <a href="{{ route('invoice.edit', $inv->id) }}" class="btn btn-xs btn-light border text-secondary rounded-2 p-1 px-1.5" title="Edit">
                                            <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasRole('admin'))
                                        <form id="del-inv-{{ $inv->id }}" action="{{ route('invoice.destroy', $inv->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-xs btn-light border text-danger rounded-2 p-1 px-1.5" title="Hapus" onclick="confirmDelete('del-inv-{{ $inv->id }}', '{{ $inv->invoice_number }}')">
                                                <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Catat Pembayaran Vendor -->
                        @if(auth()->user()->hasRole(['admin', 'procurement']) && $inv->payment_status !== 'paid')
                            <div class="modal fade" id="paymentModal{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-3 border-0 shadow">
                                        <form action="{{ route('invoice.payment.store', $inv->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <div>
                                                    <h6 class="modal-title fw-bold text-dark mb-0">
                                                        <i class="bi bi-wallet2 text-success me-1.5"></i>Pencatatan Pembayaran Tagihan
                                                    </h6>
                                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                                        Invoice: <strong>{{ $inv->invoice_number }}</strong> &bull; Vendor: {{ $inv->customer }}
                                                    </span>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body p-4">
                                                <!-- Strip Ringkasan Sisa -->
                                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                                                        <span class="text-muted">Total Nilai Tagihan</span>
                                                        <span class="font-monospace fw-semibold">{{ $inv->invoice_amount_formatted }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center small">
                                                        <span class="text-muted">Sisa Belum Dibayar</span>
                                                        <span class="font-monospace fw-bold text-danger">{{ $inv->outstanding_formatted }}</span>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                                        <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Jumlah Kas Keluar (Rp) <span class="text-danger">*</span></label>
                                                        <input type="number" name="payment_amount" class="form-control form-control-sm font-monospace" max="{{ $inv->remaining_amount }}" min="1" placeholder="Nominal kas/bank transfer" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Potongan PPh 23 (Rp)</label>
                                                        <input type="number" name="pph23_amount" class="form-control form-control-sm font-monospace" min="0" placeholder="Jika ada bukti potong">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Nomor Bukti Potong (Bupot)</label>
                                                        <input type="text" name="bupot_number" class="form-control form-control-sm" placeholder="Contoh: BP-23/2026/001">
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Nomor Referensi Transfer / Rekening Bank</label>
                                                        <input type="text" name="payment_reference" class="form-control form-control-sm" placeholder="Contoh: TRF-MANDIRI-98129">
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold text-secondary small mb-1">Catatan Pembayaran</label>
                                                        <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Keterangan transfer atau termin pembayaran..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-top py-2.5 px-4 gap-2">
                                                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                                                    <i class="bi bi-check-lg me-1"></i> Simpan Pembayaran
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <i class="bi bi-inbox fs-4 text-secondary"></i>
                                </div>
                                <div class="fw-semibold text-dark">Tidak ada data invoice vendor yang cocok</div>
                                <div class="small text-muted mt-0.5">Silakan sesuaikan filter pencarian atau klik "+ Input Invoice Vendor" untuk menambah data baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    Halaman {{ $invoices->currentPage() }} dari {{ $invoices->lastPage() }}
                </span>
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Import Pembayaran Excel -->
@if(auth()->user()->hasRole(['admin', 'procurement']))
<div class="modal fade" id="importPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <form action="{{ route('payment.upload.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h6 class="modal-title fw-bold text-dark mb-0">
                        <i class="bi bi-file-earmark-arrow-up text-primary me-1.5"></i>Import Pembayaran via Excel
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Unggah file spreadsheet hasil rekonsiliasi bank untuk melunasi tagihan invoice secara massal.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                    </div>
                    <div class="alert alert-light border rounded-3 p-2.5 small d-flex align-items-center justify-content-between">
                        <span class="text-muted">Gunakan template resmi sistem:</span>
                        <a href="{{ route('payment.template') }}" class="btn btn-xs btn-outline-primary fw-semibold">
                            <i class="bi bi-download me-1"></i> Unduh Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 gap-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                        <i class="bi bi-upload me-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .project-code {
        font-family: 'Courier New', monospace;
        font-size: 0.76rem;
        font-weight: 700;
        color: #0369A1;
        background: #F0F9FF;
        padding: 1px 6px;
        border-radius: 4px;
        border: 1px solid #BAE6FD;
    }
    .shadow-2xs {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-xs {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(formId, invNum) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Invoice?',
                text: `Hapus invoice vendor #${invNum}? Semua data pembayaran terkait akan ikut terhapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-sm btn-danger rounded-2 px-3 fw-bold me-2',
                    cancelButton: 'btn btn-sm btn-light border rounded-2 px-3 fw-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        } else {
            if (confirm(`Hapus invoice vendor #${invNum}?`)) {
                document.getElementById(formId).submit();
            }
        }
    }
</script>
@endpush
