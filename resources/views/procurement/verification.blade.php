@extends('layouts.main')

@section('title', 'Verifikasi 3-Way Match Pengadaan')

@section('content')
<div class="container-fluid px-3 py-3 page-entrance">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-shield-check text-success me-2"></i>Verifikasi 3-Way Match Pengadaan
                </h4>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.72rem;">
                    Validasi Sah Sebelum Pembayaran
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Penyelarasan tiga pilar dokumen transaksi: Surat Pesanan (PO/SPK), Berita Acara (BASTO lolos QC), dan Tagihan Rekanan (Invoice).
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('procurement.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Pengadaan
            </a>
            <a href="{{ route('monitoring.invoice-vendor') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-receipt-cutoff me-1"></i> Monitoring Tagihan Vendor
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

    <!-- Workflow Banner -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-3 text-center border-end border-secondary border-opacity-25">
                    <div class="rounded-circle bg-primary bg-opacity-25 text-primary d-inline-flex align-items-center justify-content-center p-3 mb-2">
                        <i class="bi bi-file-earmark-ruled fs-4 text-white"></i>
                    </div>
                    <div class="fw-bold small text-white">Surat Pesanan (PO)</div>
                    <span class="text-light opacity-75" style="font-size: 0.7rem;">Komitmen kuantitas & harga dari Procurement</span>
                </div>

                <div class="col-md-1 text-center d-none d-md-block">
                    <i class="bi bi-arrow-right fs-4 text-muted"></i>
                </div>

                <div class="col-md-3 text-center border-end border-secondary border-opacity-25">
                    <div class="rounded-circle bg-success bg-opacity-25 text-success d-inline-flex align-items-center justify-content-center p-3 mb-2">
                        <i class="bi bi-patch-check fs-4 text-white"></i>
                    </div>
                    <div class="fw-bold small text-white">BASTO Lolos QC & SM</div>
                    <span class="text-light opacity-75" style="font-size: 0.7rem;">Bukti fisik barang/jasa diterima sesuai standar mutu</span>
                </div>

                <div class="col-md-1 text-center d-none d-md-block">
                    <i class="bi bi-arrow-right fs-4 text-muted"></i>
                </div>

                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-info bg-opacity-25 text-info d-inline-flex align-items-center justify-content-center p-3 mb-2">
                        <i class="bi bi-receipt-cutoff fs-4 text-white"></i>
                    </div>
                    <div class="fw-bold small text-white">Invoice Vendor Cocok</div>
                    <span class="text-light opacity-75" style="font-size: 0.7rem;">Klaim pembayaran valid & siap diproses pelunasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row of 3 Match Columns -->
    <div class="row g-3">
        <!-- Section: Active POs -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-file-earmark-ruled text-primary me-1"></i> PO Aktif ({{ count($activeOrders) }})
                    </span>
                    <a href="{{ route('procurement.orders') }}" class="btn btn-xs btn-link text-decoration-none p-0" style="font-size: 0.72rem;">Kelola PO</a>
                </div>
                <div class="card-body p-2" style="max-height: 520px; overflow-y: auto;">
                    @forelse($activeOrders as $po)
                    <div class="card border border-light p-3 mb-2 rounded-2 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-primary small">{{ $po->po_number }}</span>
                            <span class="badge {{ $po->status_badge }}" style="font-size: 0.65rem;">{{ $po->status_label }}</span>
                        </div>
                        <div class="small fw-semibold text-dark">{{ $po->vendor_name }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ Str::limit($po->po_title, 32) }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="fw-bold text-dark small">Rp {{ number_format($po->po_amount, 0, ',', '.') }}</span>
                            
                            @if($po->status != 'completed')
                                <form action="{{ route('procurement.orders.verify', $po->id) }}" method="POST" onsubmit="return confirm('Konfirmasi verifikasi 3-Way Match untuk PO {{ $po->po_number }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success py-0 px-2" style="font-size: 0.7rem;">
                                        <i class="bi bi-check-lg me-1"></i> Sahkan Match
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.65rem;"><i class="bi bi-check-circle"></i> Selesai</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        Tidak ada PO aktif yang menunggu penyelarasan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Section: Approved BASTO Docs -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-patch-check text-success me-1"></i> BASTO Lolos QC & SM ({{ count($approvedBastos) }})
                    </span>
                    <a href="{{ route('basto.index') }}" class="btn btn-xs btn-link text-decoration-none p-0" style="font-size: 0.72rem;">Semua BASTO</a>
                </div>
                <div class="card-body p-2" style="max-height: 520px; overflow-y: auto;">
                    @forelse($approvedBastos as $basto)
                    <div class="card border border-light p-3 mb-2 rounded-2 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-success small">{{ $basto->basto_number ?? 'BASTO-'.$basto->id }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;">
                                <i class="bi bi-patch-check me-1"></i>Approved
                            </span>
                        </div>
                        <div class="small fw-semibold text-dark">{{ $basto->project_name ?? $basto->project_id }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            Disetujui: {{ $basto->reviewed_at ? \Carbon\Carbon::parse($basto->reviewed_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="fw-bold text-dark small">ID: {{ $basto->project_id }}</span>
                            @if($basto->attachment_file)
                                <a href="{{ asset('storage/' . $basto->attachment_file) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.7rem;">
                                    <i class="bi bi-file-pdf text-danger"></i> Berkas BASTO
                                </a>
                            @else
                                <span class="badge bg-light text-muted" style="font-size: 0.65rem;">Fisik Valid</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        Belum ada dokumen BASTO yang berstatus Approved.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Section: Pending Vendor Invoices -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-receipt text-warning me-1"></i> Antrean Tagihan Vendor ({{ count($pendingInvoices) }})
                    </span>
                    <a href="{{ route('monitoring.invoice-vendor') }}" class="btn btn-xs btn-link text-decoration-none p-0" style="font-size: 0.72rem;">Semua Tagihan</a>
                </div>
                <div class="card-body p-2" style="max-height: 520px; overflow-y: auto;">
                    @forelse($pendingInvoices as $inv)
                    <div class="card border border-light p-3 mb-2 rounded-2 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-dark small">{{ Str::limit($inv->vendor ?? 'Vendor', 20) }}</span>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size: 0.65rem;">
                                {{ $inv->status }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            Proyek: {{ $inv->project_id }} | Periode: {{ $inv->periode }} {{ $inv->tahun }}
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            Item: {{ Str::limit($inv->item_biaya ?? '-', 30) }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="fw-bold text-primary small">Rp {{ number_format($inv->realisasi_biaya_final, 0, ',', '.') }}</span>
                            <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">
                                {{ $inv->no_invoice ?? 'No Inv: -' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        Tidak ada tagihan vendor yang menunggu verifikasi.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
