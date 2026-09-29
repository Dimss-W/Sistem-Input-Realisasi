@extends('layouts.main')

@section('title', 'Direktori Rekanan & Vendor Pengadaan')

@section('content')
<div class="container-fluid px-3 py-3 page-entrance">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-buildings-fill text-primary me-2"></i>Direktori Rekanan Vendor
                </h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                    Procurement Database
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Database rekanan penyedia barang/jasa, portofolio proyek tergarap, total realisasi transaksi & rekam jejak pengadaan.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('procurement.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Pengadaan
            </a>
            <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-bag-plus me-1"></i> Terbitkan PO
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('procurement.vendors') }}" class="row g-2 align-items-center">
                <div class="col-md-5 col-sm-8">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama rekanan / vendor..." value="{{ $search ?? '' }}">
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('procurement.vendors') }}" class="btn btn-sm btn-outline-secondary" title="Reset Pencarian">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Vendors -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">
                <i class="bi bi-table text-primary me-1"></i> Daftar Rekanan Terdaftar
            </span>
            <span class="text-muted small">
                Menampilkan {{ $vendors->firstItem() ?? 0 }}-{{ $vendors->lastItem() ?? 0 }} dari {{ $vendors->total() }} vendor
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nama Rekanan / Perusahaan</th>
                        <th class="text-center">Jml Proyek Terlibat</th>
                        <th class="text-center">Total Transaksi</th>
                        <th class="text-end">Akumulasi Realisasi Belanja</th>
                        <th class="text-center">Tahun Aktif Terakhir</th>
                        <th class="text-center" style="width: 140px;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $index => $v)
                    <tr>
                        <td class="ps-3 text-muted">{{ $vendors->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($v->vendor, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $v->vendor }}</div>
                                    <span class="text-muted" style="font-size: 0.7rem;">Rekanan Pengadaan Internal</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $v->total_projects }} Proyek</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $v->total_transactions }} Transaksi</span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($v->total_spend, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $v->last_active_year ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('monitoring.invoice-vendor', ['vendor' => $v->vendor]) }}" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size: 0.72rem;" title="Lihat Riwayat Invoice">
                                    <i class="bi bi-receipt"></i> Tagihan
                                </a>
                                <a href="{{ route('procurement.orders', ['vendor' => $v->vendor]) }}" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.72rem;" title="Lihat PO Rekanan">
                                    <i class="bi bi-file-earmark-text"></i> PO
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-buildings fs-2 text-muted mb-2 d-block"></i>
                            Tidak ada rekanan vendor yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Halaman {{ $vendors->currentPage() }} dari {{ $vendors->lastPage() }}</span>
            {{ $vendors->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
