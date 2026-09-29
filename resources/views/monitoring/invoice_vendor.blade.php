@extends('layouts.main')

@section('title', 'Monitoring Invoice Vendor (Prognosa vs Actual)')

@push('styles')
<style>
    /* ===== PRINT STYLES UNTUK LAPORAN INVOICE VENDOR ===== */
    @media print {
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 8mm 10mm;
        }

        body {
            background: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif !important;
            font-size: 8pt !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Sembunyikan semua elemen non-laporan / interaktif */
        .d-print-none,
        .sidebar,
        .topbar,
        .btn,
        .filter-card,
        .card-header,
        .card-footer,
        .navbar,
        #sidebarToggle,
        .modal,
        .alert,
        .dropdown,
        .dropdown-menu,
        .badge.bg-danger.d-block {
            display: none !important;
        }

        .main-content,
        .page-entrance,
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Kop Dokumen Resmi PGN */
        .corp-print-header {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .corp-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .corp-logo-img {
            height: 38px;
            width: auto;
        }

        .corp-name {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            line-height: 1.1;
        }

        .corp-sub {
            font-size: 7.5pt;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .doc-title-block {
            text-align: right;
        }

        .doc-main-title {
            font-size: 11pt;
            font-weight: 800;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin: 0;
        }

        .doc-ref-number {
            font-size: 7.5pt;
            color: #64748b;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
        }

        /* Filter Parameter Info Strip */
        .meta-param-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 7.5pt;
        }

        .meta-param-item strong {
            color: #475569;
            text-transform: uppercase;
            font-size: 6.8pt;
            display: block;
        }

        .meta-param-item span {
            color: #0f172a;
            font-weight: 700;
        }

        /* Formal KPI Summary Box */
        .print-summary-box {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr);
            border: 1px solid #0f172a;
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .print-summary-cell {
            padding: 6px 10px;
            border-right: 1px solid #cbd5e1;
        }

        .print-summary-cell:last-child {
            border-right: none;
        }

        .print-summary-label {
            font-size: 6.8pt;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.04em;
        }

        .print-summary-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 1px;
            line-height: 1.1;
        }

        .print-summary-sub {
            font-size: 6.8pt;
            color: #475569;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Table Styling */
        .table-print-formal {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 7.5pt !important;
            margin-bottom: 14px;
        }

        .table-print-formal th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1px solid #475569 !important;
            padding: 4px 6px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 6.8pt !important;
            letter-spacing: 0.03em;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-print-formal td {
            border: 1px solid #94a3b8 !important;
            padding: 3.5px 5px !important;
            vertical-align: middle;
            color: #0f172a !important;
        }

        .table-print-formal tr:nth-child(even) td {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-print-formal tr {
            page-break-inside: avoid !important;
        }

        /* Status Tag for Print */
        .status-print-tag {
            font-weight: 700;
            font-size: 6.8pt;
            padding: 1px 5px;
            border-radius: 3px;
            display: inline-block;
            text-align: center;
        }

        .status-print-paid {
            color: #047857;
            background: #d1fae5 !important;
            border: 1px solid #a7f3d0;
        }

        .status-print-pending {
            color: #b45309;
            background: #fef3c7 !important;
            border: 1px solid #fde68a;
        }

        .status-print-unpaid {
            color: #be123c;
            background: #ffe4e6 !important;
            border: 1px solid #fecdd3;
        }

        /* Signature Section */
        .print-signature-section {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 18px;
            page-break-inside: avoid !important;
        }

        .sig-block {
            text-align: center;
        }

        .sig-title {
            font-size: 7.2pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 45px;
        }

        .sig-name {
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .sig-role {
            font-size: 7pt;
            color: #64748b;
            font-weight: 600;
        }

        /* Formal Report Footer */
        .print-footer-notice {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            margin-top: 14px;
            font-size: 6.5pt;
            color: #64748b;
        }
    }

    /* Mode Pratinjau di Layar Monitor */
    @media screen {
        @if(request('print') == '1')
        .sidebar, .topbar, #sidebarToggle, .filter-card, .btn-print-hide, .pagination-container, .nav-tabs, .alert {
            display: none !important;
        }
        body {
            background: #e2e8f0 !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 60px 15px 40px 15px !important;
            background: #e2e8f0 !important;
            min-height: 100vh;
        }
        .printable-report-card {
            max-width: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 12mm;
            border-radius: 6px;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.15);
        }
        .corp-print-header { display: flex !important; }
        .meta-param-grid { display: grid !important; }
        .print-summary-box { display: block !important; }
        .print-signature-section { display: grid !important; }
        .print-footer-notice { display: flex !important; }
        @else
        .corp-print-header,
        .meta-param-grid,
        .print-summary-box,
        .print-signature-section,
        .print-footer-notice {
            display: none !important;
        }
        @endif
    }
</style>
@endpush

@section('content')
@if(request('print') == '1')
    @include('partials.print_a4_toolbar', [
        'targetId'    => 'printableReportArea',
        'filename'    => 'Laporan_Monitoring_Invoice_Vendor_A4_' . date('Ymd_His'),
        'orientation' => 'landscape'
    ])
@endif

<div class="container-fluid px-3 py-3 page-entrance">

    <div id="printableReportArea" class="printable-report-card">
    <!-- ======================================================================= -->
    <!-- 1. KOP SURAT FORMAL RESMI (TAMPIL SAAT CETAK / PRATINJAU A4)             -->
    <!-- ======================================================================= -->
    <div class="corp-print-header">
        <div class="corp-logo-wrap">
            <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM Logo" class="corp-logo-img" style="height: 38px; width: auto; object-fit: contain;">
            <div>
                <div class="corp-name">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</div>
                <div class="corp-sub">PGNCOM &bull; Procurement &amp; Vendor Management Department</div>
            </div>
        </div>
        <div class="doc-title-block">
            <h1 class="doc-main-title">Laporan Monitoring Invoice Vendor</h1>
            <div class="doc-ref-number">Ref: PGN/PROC/INV-VENDOR/{{ date('Ym') }}/{{ str_pad($vendorInvoices->total(), 3, '0', STR_PAD_LEFT) }} &bull; Sesuai Filter</div>
        </div>
    </div>

    <!-- Metadata Parameter Filter (Hanya Saat Cetak) -->
    <div class="meta-param-grid">
        <div class="meta-param-item">
            <strong>Rekanan / Vendor</strong>
            <span>{{ !empty($filters['vendor']) ? Str::limit($filters['vendor'], 26) : 'Semua Rekanan / Vendor' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Project ID</strong>
            <span>{{ !empty($filters['project_id']) ? $filters['project_id'] : 'Semua Proyek Aktif' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Status Pembayaran</strong>
            <span>{{ !empty($filters['status']) ? $filters['status'] : 'Semua Status' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Periode Bulan &amp; Tahun</strong>
            <span>{{ !empty($filters['periode']) ? $filters['periode'] : 'Semua Bulan' }} {{ !empty($filters['tahun']) ? $filters['tahun'] : '(Semua Tahun)' }}</span>
        </div>
        @if(!empty($filters['risk_aging']))
        <div class="meta-param-item">
            <strong>Kategori Risiko</strong>
            <span style="color: #be123c; font-weight: 800;">⚠️ Risiko Hangus (&gt; 90 Hari)</span>
        </div>
        @endif
        @if(!empty($filters['search']))
        <div class="meta-param-item">
            <strong>Pencarian Kata Kunci</strong>
            <span>"{{ $filters['search'] }}"</span>
        </div>
        @endif
        <div class="meta-param-item">
            <strong>Tanggal &amp; Waktu Cetak</strong>
            <span>{{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB &bull; {{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- Formal 4-Metrics Summary Table (Hanya Saat Cetak) -->
    <div class="print-summary-box">
        <div class="print-summary-cell">
            <div class="print-summary-label">Budget Prognosa Terkait</div>
            <div class="print-summary-value">Rp {{ number_format($totalPrognosaBudget, 0, ',', '.') }}</div>
            <div class="print-summary-sub">Estimasi Rencana Operasional</div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Realisasi Tagihan Riil</div>
            <div class="print-summary-value" style="color: #0369a1;">Rp {{ number_format($totalActualSpend, 0, ',', '.') }}</div>
            <div class="print-summary-sub">Total <strong>{{ $vendorInvoices->total() }}</strong> Tagihan Tercatat</div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Deviasi / Selisih Anggaran</div>
            <div class="print-summary-value" style="color: {{ $variance >= 0 ? '#047857' : '#be123c' }};">
                Rp {{ number_format(abs($variance), 0, ',', '.') }}
            </div>
            <div class="print-summary-sub">{{ $variance >= 0 ? 'Hemat (+'.$variancePct.'%)' : 'Over Budget ('.$variancePct.'%)' }} dari Estimasi</div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Tunggakan &amp; Wait Inv</div>
            <div class="print-summary-value" style="color: #b45309;">Rp {{ number_format($totalUnpaidSpend, 0, ',', '.') }}</div>
            <div class="print-summary-sub">{{ $totalWaitInvCount }} invoice menunggu proses/bayar</div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 2. TAMPILAN SCREEN MONITOR (SEMBUNYI SAAT CETAK)                         -->
    <!-- ======================================================================= -->
    
    <!-- Screen Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-receipt-cutoff text-indigo me-2" style="color: #6366f1;"></i>Monitoring Invoice Vendor
                </h4>
                <span class="badge bg-indigo bg-opacity-10 border border-indigo border-opacity-25" style="color: #6366f1; background-color: rgba(99, 102, 241, 0.1); font-size: 0.72rem;">
                    Prognosa vs Actual Spend
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Pengawasan komprehensif tagihan penyedia barang/jasa, perbandingan anggaran prognosa terhadap realisasi tagihan aktual.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->hasRole(['admin', 'procurement']))
                <a href="{{ route('invoice.create') }}" class="btn btn-sm btn-primary fw-semibold shadow-xs">
                    <i class="bi bi-plus-circle me-1"></i> + Input Invoice Vendor
                </a>
            @endif
            <button type="button" onclick="cetakLaporanInvoiceVendor()" class="btn btn-sm btn-outline-secondary fw-semibold shadow-xs">
                <i class="bi bi-printer me-1"></i> Cetak Laporan Formal
            </button>
            @if(auth()->user()->hasRole('procurement'))
                <a href="{{ route('procurement.orders') }}" class="btn btn-sm btn-outline-primary fw-semibold shadow-xs">
                    <i class="bi bi-bag-plus me-1"></i> Kelola PO Pengadaan
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Flash Message (Screen Only) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2.5 px-3 small border-0 shadow-sm mb-3 d-print-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Screen 4 KPI Cards Strip -->
    <div class="row g-3 mb-3 d-print-none">
        <!-- KPI 1: Prognosa Budget -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #6366f1 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Budget Prognosa Terkait</span>
                    <i class="bi bi-cash-stack fs-5" style="color: #6366f1;"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPrognosaBudget, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <span>Estimasi Rencana Operasional</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Actual Tagihan Vendor -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Actual Tagihan Vendor</span>
                    <i class="bi bi-receipt text-primary fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-primary">Rp {{ number_format($totalActualSpend, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    Total: <strong class="text-dark">{{ $vendorInvoices->total() }}</strong> Tagihan Tercatat
                </div>
            </div>
        </div>

        <!-- KPI 3: Deviasi Budget (Prognosa - Actual) -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid {{ $variance >= 0 ? '#10b981' : '#ef4444' }} !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Deviasi / Selisih Budget</span>
                    <i class="bi {{ $variance >= 0 ? 'bi-graph-down-arrow text-success' : 'bi-graph-up-arrow text-danger' }} fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 {{ $variance >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format(abs($variance), 0, ',', '.') }}
                </h4>
                <div class="small text-muted">
                    @if($variance >= 0)
                        <span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Hemat (+{{ $variancePct }}%)</span> dari Estimasi
                    @else
                        <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>Over Budget ({{ $variancePct }}%)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI 4: Pending / Wait Inv -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Tunggakan &amp; Wait Inv</span>
                    <i class="bi bi-clock-history text-warning fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-warning">Rp {{ number_format($totalUnpaidSpend, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <span>{{ $totalWaitInvCount }} invoice menunggu proses/bayar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Dini Risiko Tagihan Hangus (> 90 Hari) — Screen Only -->
    @if($totalOverdueRiskCount > 0)
    <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 py-2.5 px-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2 shadow-xs d-print-none">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
            <div>
                <strong class="text-danger">Peringatan Dini: Terdeteksi {{ $totalOverdueRiskCount }} Tagihan Berisiko Hangus / Kedaluwarsa (&gt; 90 Hari)!</strong>
                <div class="text-muted small">
                    Total tagihan belum terbayar: <strong>Rp {{ number_format($totalOverdueRiskAmount, 0, ',', '.') }}</strong>. Segera proses pembayaran sebelum melewati batas ketentuan pajak/keuangan 3 bulan.
                </div>
            </div>
        </div>
        <a href="{{ ($filters['risk_aging'] ?? '') == '1' ? route('monitoring.invoice-vendor', collect($filters)->except('risk_aging')->all()) : route('monitoring.invoice-vendor', array_merge($filters, ['risk_aging' => '1'])) }}" 
           class="btn btn-sm {{ ($filters['risk_aging'] ?? '') == '1' ? 'btn-secondary' : 'btn-danger' }} rounded-pill px-3 fw-semibold shadow-sm">
            @if(($filters['risk_aging'] ?? '') == '1')
                <i class="bi bi-x-circle me-1"></i>Reset Filter Risiko
            @else
                <i class="bi bi-funnel-fill me-1"></i>Tampilkan Tagihan &gt; 90 Hari
            @endif
        </a>
    </div>
    @endif

    <!-- Top 8 Vendor Filter Strip (Screen Only) -->
    @if(count($vendorSummaries) > 0)
    <div class="card border-0 shadow-sm rounded-3 mb-3 d-print-none">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-dark text-uppercase">
                    <i class="bi bi-buildings me-1 text-primary"></i> Top Vendor Berdasarkan Nilai Tagihan
                </span>
                <span class="text-muted small" style="font-size: 0.72rem;">Klik nama vendor untuk filter</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($vendorSummaries as $vs)
                <a href="{{ route('monitoring.invoice-vendor', array_merge($filters, ['vendor' => $vs->vendor])) }}" 
                   class="badge text-decoration-none p-2 border rounded-2 d-flex align-items-center gap-2 {{ ($filters['vendor'] ?? '') == $vs->vendor ? 'bg-primary text-white border-primary' : 'bg-light text-dark border-light' }}"
                   style="font-size: 0.75rem;">
                    <span class="fw-semibold">{{ Str::limit($vs->vendor, 18) }}</span>
                    <span class="badge {{ ($filters['vendor'] ?? '') == $vs->vendor ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25 text-dark' }}">
                        Rp {{ number_format($vs->total_actual / 1000000, 1, ',', '.') }}M
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Bar Praktis (DIBUANG SAAT CETAK) -->
    <div class="card border-0 shadow-sm rounded-3 mb-3 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('monitoring.invoice-vendor') }}" id="invoiceFilterForm" class="row g-2 align-items-center">
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari vendor, item biaya..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <select name="vendor" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Vendor --</option>
                        @foreach($vendorList as $v)
                            <option value="{{ $v }}" {{ ($filters['vendor'] ?? '') == $v ? 'selected' : '' }}>{{ Str::limit($v, 22) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <select name="project_id" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Proyek --</option>
                        @foreach($projectList as $p)
                            <option value="{{ $p }}" {{ ($filters['project_id'] ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <select name="status" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Status --</option>
                        @foreach($statusList as $st)
                            <option value="{{ $st }}" {{ ($filters['status'] ?? '') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <select name="periode" class="form-select form-select-sm bg-light border-0">
                        <option value="">-- Semua Periode --</option>
                        @foreach($periodeList as $per)
                            <option value="{{ $per }}" {{ ($filters['periode'] ?? '') == $per ? 'selected' : '' }}>{{ $per }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 col-sm-12 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold" title="Terapkan Filter">
                        <i class="bi bi-filter"></i>
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['vendor']) || !empty($filters['project_id']) || !empty($filters['status']) || !empty($filters['periode']) || !empty($filters['risk_aging']))
                        <a href="{{ route('monitoring.invoice-vendor') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 3. TABEL DATA RINCIAN TAGIHAN (OPTIMAL UNTUK LAYAR DAN CETAK FORMAL)   -->
    <!-- ======================================================================= -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center d-print-none">
            <span class="fw-bold text-dark">
                <i class="bi bi-table text-primary me-1"></i> Rincian Tagihan &amp; Invoice Vendor
                @if(($filters['risk_aging'] ?? '') == '1')
                    <span class="badge bg-danger ms-2" style="font-size: 0.7rem;"><i class="bi bi-filter me-1"></i>Filter Aktif: Risiko Hangus &gt; 90 Hari</span>
                @endif
            </span>
            <span class="text-muted small">Menampilkan {{ $vendorInvoices->firstItem() ?? 0 }}-{{ $vendorInvoices->lastItem() ?? 0 }} dari {{ $vendorInvoices->total() }} invoice</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-print-formal" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-2 text-center" style="width: 4%;">No</th>
                        <th style="width: 11%; text-align: center;">Project ID</th>
                        <th style="width: 22%;">Penyedia / Vendor &amp; SPK</th>
                        <th style="width: 27%;">Item Pengadaan &amp; No Invoice</th>
                        <th style="width: 10%; text-align: center;">Periode</th>
                        <th style="width: 14%; text-align: right;">Nilai Tagihan Aktual</th>
                        <th style="width: 12%; text-align: center;">Status Pembayaran</th>
                        <th class="text-center d-print-none" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendorInvoices as $index => $row)
                    <tr>
                        <td class="ps-2 text-center text-muted">{{ $vendorInvoices->firstItem() + $index }}</td>
                        <td style="text-align: center;">
                            <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $row->project_id ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $row->vendor ?? 'Umum / Tidak Terdata' }}</div>
                            @php
                                $matchedSpk = $row->no_spk ?: (\App\Models\Basto::where('project_id', $row->project_id)->whereNotNull('cost_no')->where('cost_no', '!=', '')->value('cost_no') ?: \App\Models\Kontrak::where('project_id', $row->project_id)->whereNotNull('contract_number')->where('contract_number', '!=', '')->value('contract_number'));
                            @endphp
                            <span class="text-muted" style="font-size: 0.68rem;">SPK: {{ $matchedSpk ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="text-dark fw-medium">{{ Str::limit($row->item_biaya ?? '-', 50) }}</div>
                            @php
                                $matchedInv = $row->no_invoice ?: \App\Models\Invoice::where('project_id', $row->project_id)->value('invoice_number');
                            @endphp
                            @if($matchedInv)
                                <span class="badge bg-secondary bg-opacity-10 text-dark border mt-0.5" style="font-size: 0.65rem;">
                                    <i class="bi bi-receipt me-0.5"></i>Inv: {{ $matchedInv }}
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="fw-semibold text-dark">{{ $row->periode }} {{ $row->tahun }}</span>
                        </td>
                        <td style="text-align: right;" class="fw-bold text-dark font-monospace">
                            Rp {{ number_format($row->realisasi_biaya_final, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">
                            @php
                                $upperSt = strtoupper($row->status ?? 'UNPAID');
                                if ($upperSt !== 'PAID') {
                                    $hasPaidInv = \App\Models\Invoice::where('project_id', $row->project_id)->where('payment_status', 'paid')->exists();
                                    if ($hasPaidInv) {
                                        $upperSt = 'PAID';
                                    }
                                }
                            @endphp
                            @if($upperSt === 'PAID')
                                <span class="status-print-tag status-print-paid">LUNAS (PAID)</span>
                            @elseif(in_array($upperSt, ['WAIT INV', 'PROSES']))
                                <span class="status-print-tag status-print-pending">{{ $upperSt }}</span>
                            @else
                                <span class="status-print-tag status-print-unpaid">UNPAID</span>
                            @endif

                            @if($row->is_overdue_90_days)
                                <div class="text-danger fw-bold mt-0.5" style="font-size: 6.5pt;">
                                    &bull; Aging &gt;90 Hari
                                </div>
                            @endif
                        </td>
                        <td class="text-center d-print-none">
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('dmo'))
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle py-0.5 px-2 rounded-2 fw-semibold" style="font-size: 0.75rem;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ubah
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-1" style="font-size: 0.78rem;">
                                        <li><h6 class="dropdown-header text-uppercase" style="font-size: 0.65rem;">Pilih Status Baru</h6></li>
                                        @foreach(['PAID' => ['text-success', 'bi-check-circle-fill'], 'WAIT INV' => ['text-warning', 'bi-clock-fill'], 'PROSES' => ['text-primary', 'bi-hourglass-split'], 'UNPAID' => ['text-danger', 'bi-x-circle-fill']] as $stKey => $stMeta)
                                            @if(strtoupper($row->status) !== $stKey)
                                                <li>
                                                    <form method="POST" action="{{ route('monitoring.invoice-vendor.update-status', $row->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ $stKey }}">
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-1.5 {{ $stMeta[0] }}">
                                                            <i class="bi {{ $stMeta[1] }}"></i> Ubah ke {{ $stKey }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(auth()->user()->hasRole('procurement'))
                                <a href="{{ route('procurement.verification', ['search' => $row->vendor]) }}" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.72rem;" title="Verifikasi 3-Way Match">
                                    <i class="bi bi-shield-check"></i> Match
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted mb-2 d-block"></i>
                            Tidak ada data invoice vendor yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendorInvoices->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center d-print-none">
            <span class="small text-muted">Halaman {{ $vendorInvoices->currentPage() }} dari {{ $vendorInvoices->lastPage() }}</span>
            {{ $vendorInvoices->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    <!-- ======================================================================= -->
    <!-- 4. LEMBAR TANDA TANGAN RESMI (HANYA MUNCUL SAAT CETAK / PDF)             -->
    <!-- ======================================================================= -->
    <div class="print-signature-section">
        <div class="sig-block">
            <div class="sig-title">Dipersiapkan Oleh:</div>
            <div class="sig-name">{{ auth()->user()->name }}</div>
            <div class="sig-role">Procurement / Data Management Officer</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Diverifikasi Oleh:</div>
            <div class="sig-name">Service Manager / PM Lead</div>
            <div class="sig-role">Pengawas Pekerjaan &amp; Rekanan</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Mengetahui &amp; Menyetujui:</div>
            <div class="sig-name">Manager Procurement &amp; SCM</div>
            <div class="sig-role">Supply Chain Management Head</div>
        </div>
    </div>

    <!-- Formal Print Footer -->
    <div class="print-footer-notice">
        <span>Dicetak melalui Sistem Monitoring Realisasi &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)</span>
        <span>Dokumen Pengadaan &amp; Tagihan Sah &bull; Internal Control</span>
    </div>

    </div> {{-- /#printableReportArea --}}

</div>

@push('scripts')
<script>
function cetakLaporanInvoiceVendor() {
    const form = document.getElementById('invoiceFilterForm');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('print', '1');
        window.open(`${window.location.pathname}?${params.toString()}`, '_blank');
    } else {
        window.print();
    }
}
</script>
@endpush
@endsection
