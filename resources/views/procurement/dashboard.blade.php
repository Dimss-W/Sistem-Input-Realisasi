@extends('layouts.main')

@section('title', 'Procurement Command Hub (Dashboard Pengadaan)')

@section('content')
<div class="container-fluid px-3 py-3 page-entrance">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-cart-check-fill text-primary me-2"></i>Procurement Command Hub
                </h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                    Pengadaan Barang & Jasa
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Pengelolaan siklus pengadaan terpadu: Penerbitan PO/SPK, pembinaan rekanan vendor, verifikasi 3-Way Match & pemenuhan BASTO.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Terbitkan PO Baru
            </a>
            <a href="{{ route('procurement.verification') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-shield-check me-1"></i> Verifikasi 3-Way Match
            </a>
        </div>
    </div>

    <!-- Alert status -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 4 Quick KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: PO Commitment -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Total Komitmen PO/SPK</span>
                    <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPoCommitment, 0, ',', '.') }}</h4>
                <div class="small text-muted d-flex justify-content-between">
                    <span>{{ $totalPoIssued }} PO Diterbitkan</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $totalPoActive }} Aktif</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Actual Vendor Spend -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Realisasi Belanja Pengadaan</span>
                    <i class="bi bi-cash-coin text-success fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-success">Rp {{ number_format($totalActualSpend, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <span>Dari {{ $totalVendorsCount }} Rekanan Vendor Terdaftar</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: BASTO Validated -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Kesiapan BASTO (Lolos QC)</span>
                    <i class="bi bi-patch-check text-purple fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $approvedBastoCount }} Dokumen</h4>
                <div class="small text-muted d-flex justify-content-between">
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Siap Tagih</span>
                    <span class="text-muted">{{ $pendingBastoCount }} Pending QC/SM</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Invoice Verification Queue -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Antrean Verifikasi Tagihan</span>
                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-warning">{{ $pendingInvoicesCount }} Tagihan</h4>
                <div class="small text-muted">
                    Nilai: <strong class="text-dark">Rp {{ number_format($pendingInvoicesAmount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Recent POs & Top Vendor Partners -->
    <div class="row g-3 mb-4">
        <!-- Recent POs Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-receipt text-primary me-1"></i> Daftar PO / SPK Pengadaan Terkini
                    </span>
                    <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-link text-decoration-none p-0 small">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size: 0.7rem;">
                            <tr>
                                <th class="ps-3">No. PO</th>
                                <th>Proyek</th>
                                <th>Vendor</th>
                                <th>Judul Pengadaan</th>
                                <th class="text-end">Nilai PO</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $po)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $po->po_number }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $po->project_id }}</span></td>
                                <td class="text-dark">{{ Str::limit($po->vendor_name, 18) }}</td>
                                <td>{{ Str::limit($po->po_title, 25) }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($po->po_amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $po->status_badge }}" style="font-size: 0.68rem;">
                                        {{ $po->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada Surat Pesanan (PO/SPK) yang diterbitkan.
                                    <div class="mt-2">
                                        <a href="{{ route('procurement.orders') }}" class="btn btn-xs btn-outline-primary">
                                            <i class="bi bi-plus"></i> Buat PO Perdana
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Vendor Partners -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-trophy-fill text-warning me-1"></i> Top Vendor Pengadaan
                    </span>
                    <a href="{{ route('procurement.vendors') }}" class="btn btn-sm btn-link text-decoration-none p-0 small">
                        Direktori <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($topVendors as $idx => $tv)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border border-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-circle {{ $idx == 0 ? 'bg-warning text-dark' : ($idx == 1 ? 'bg-secondary text-white' : 'bg-dark bg-opacity-25 text-dark') }}" style="width: 24px; height: 24px; line-height: 18px;">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <div class="fw-bold text-dark small">{{ Str::limit($tv->vendor, 18) }}</div>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $tv->total_transaksi }} Transaksi</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary small">Rp {{ number_format($tv->total_spend / 1000000, 1, ',', '.') }} jt</div>
                                <span class="text-muted" style="font-size: 0.68rem;">Realisasi</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada riwayat transaksi vendor.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Quick Access Navigation -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-file-earmark-ruled fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Surat Pesanan (PO / SPK)</h6>
                        <p class="text-muted small mb-2">Terbitkan & pantau komitmen pengadaan barang/jasa.</p>
                        <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-outline-primary py-0 px-2 small">Buka Modul PO &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Verifikasi 3-Way Match</h6>
                        <p class="text-muted small mb-2">Penyelarasan PO, BASTO lolos QC, & Tagihan Vendor.</p>
                        <a href="{{ route('procurement.verification') }}" class="btn btn-sm btn-outline-success py-0 px-2 small">Buka Verifikasi &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Monitoring Invoice Vendor</h6>
                        <p class="text-muted small mb-2">Pantau Prognosa vs Actual belanja penyedia.</p>
                        <a href="{{ route('monitoring.invoice-vendor') }}" class="btn btn-sm btn-outline-info py-0 px-2 small">Buka Tagihan &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
