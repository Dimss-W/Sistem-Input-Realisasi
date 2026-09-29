@extends('layouts.main')

@section('title', 'Monitoring Cost Kontrak (Cost vs Realisasi)')

@push('styles')
<style>
    /* ===== PRINT STYLES UNTUK LAPORAN COST KONTRAK ===== */
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

        /* Sembunyikan semua elemen navigasi & interaktif */
        .d-print-none,
        .sidebar,
        .topbar,
        .btn,
        .filter-card,
        .card-header,
        .navbar,
        #sidebarToggle,
        .modal,
        .alert {
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

        /* Tampilkan khusus elemen cetak */
        .print-only-block {
            display: block !important;
        }

        .print-only-flex {
            display: flex !important;
        }

        /* Kop Dokumen Resmi PGNCOM */
        .corp-print-header {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #0A2540;
            padding-bottom: 10px;
            margin-bottom: 12px;
            background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
            padding-top: 4px;
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
            color: #0284c7;
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

        /* Filter & Metadata Parameter Info Strip */
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

        /* Formal KPI Summary Box (Grayscale Print-Optimized) */
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

        /* Print Table Styling */
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

        /* Status Badge for Print */
        .status-print-tag {
            font-weight: 700;
            font-size: 6.8pt;
            padding: 1px 4px;
            border-radius: 3px;
            display: inline-block;
            text-align: center;
        }

        .status-print-safe {
            color: #047857;
            background: #d1fae5 !important;
            border: 1px solid #a7f3d0;
        }

        .status-print-warning {
            color: #b45309;
            background: #fef3c7 !important;
            border: 1px solid #fde68a;
        }

        .status-print-critical {
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
        .print-only-block { display: block !important; }
        .print-only-flex { display: flex !important; }
        .corp-print-header { display: flex !important; }
        .meta-param-grid { display: grid !important; }
        .print-summary-box { display: block !important; }
        .print-signature-section { display: grid !important; }
        .print-footer-notice { display: flex !important; }
        @else
        .print-only-block,
        .print-only-flex,
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
        'filename'    => 'Laporan_Monitoring_Cost_Kontrak_A4_' . date('Ymd_His'),
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
                <div class="corp-sub">DIVISI SERVICE MANAGEMENT &amp; OPERATION (SMO) &mdash; DIREKTORAT INFRASTRUKTUR &amp; TEKNOLOGI</div>
            </div>
        </div>
        <div class="doc-title-block">
            <h1 class="doc-main-title">LAPORAN MONITORING COST KONTRAK</h1>
            <div class="doc-ref-number">Ref: PGNCOM/SMO/COST-CTRL/{{ date('Ymd') }}/{{ str_pad(count($projectRows), 3, '0', STR_PAD_LEFT) }} &bull; {{ now()->locale('id')->isoFormat('D MMMM Y') }}</div>
        </div>
    </div>

    <!-- Metadata Parameter Filter (Hanya Saat Cetak) -->
    <div class="meta-param-grid">
        <div class="meta-param-item">
            <strong>Tahun Anggaran</strong>
            <span>{{ !empty($filters['tahun']) ? 'Tahun ' . $filters['tahun'] : 'Semua Tahun (Portofolio Berjalan)' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Service Manager</strong>
            <span>{{ !empty($filters['service_manager']) ? $filters['service_manager'] : 'Semua Service Manager' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Klien / Partner</strong>
            <span>{{ !empty($filters['client']) ? Str::limit($filters['client'], 28) : 'Semua Mitra Klien' }}</span>
        </div>
        <div class="meta-param-item">
            <strong>Status Kesehatan</strong>
            <span>
                @if(!empty($filters['health_status']))
                    @if($filters['health_status'] === 'safe') 🟢 SEHAT (&lt;75%)
                    @elseif($filters['health_status'] === 'warning') 🟡 WASPADA (75-90%)
                    @elseif($filters['health_status'] === 'critical') 🔴 KRITIS (&gt;90%)
                    @endif
                @else
                    Semua Kategori
                @endif
            </span>
        </div>
        @if(!empty($filters['search']))
        <div class="meta-param-item">
            <strong>Kata Kunci Pencarian</strong>
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
            <div class="print-summary-label">Total Pagu Kontrak</div>
            <div class="print-summary-value">Rp {{ number_format($totalPaguAll, 0, ',', '.') }}</div>
            <div class="print-summary-sub">{{ count($projectRows) }} Proyek Terpantau</div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Realisasi Biaya Riil</div>
            <div class="print-summary-value" style="color: #047857;">Rp {{ number_format($totalRealAll, 0, ',', '.') }}</div>
            <div class="print-summary-sub">Serapan Anggaran: <strong>{{ $totalSerapanPct }}%</strong></div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Sisa Pagu Tersedia</div>
            <div class="print-summary-value" style="color: #0369a1;">Rp {{ number_format($totalSisaAll, 0, ',', '.') }}</div>
            <div class="print-summary-sub">Kapasitas Belum Terserap: {{ $totalPaguAll > 0 ? round(($totalSisaAll / $totalPaguAll) * 100, 1) : 0 }}%</div>
        </div>
        <div class="print-summary-cell">
            <div class="print-summary-label">Kesehatan Portofolio</div>
            <div class="print-summary-value" style="font-size: 9pt;">
                <span style="color: #047857;">{{ $healthCounts['safe'] }} Sehat</span> | 
                <span style="color: #b45309;">{{ $healthCounts['warning'] }} Waspada</span> | 
                <span style="color: #be123c;">{{ $healthCounts['critical'] }} Kritis</span>
            </div>
            <div class="print-summary-sub">Evaluasi batas serapan pagu</div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 2. TAMPILAN SCREEN LAYAR MONITOR (SEMBUNYI SAAT DICETAK)                -->
    <!-- ======================================================================= -->
    
    <!-- Screen Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Monitoring Cost Kontrak
                </h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                    Cost vs Realisasi Biaya
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Pengawasan komprehensif serapan anggaran biaya riil terhadap pagu kontrak proyek.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="cetakLaporanCostKontrak()" class="btn btn-sm btn-outline-secondary fw-semibold shadow-xs">
                <i class="bi bi-printer me-1"></i> Cetak Laporan Formal
            </button>
            @if(auth()->user()->hasRole('dmo'))
                <a href="{{ route('admin.master-data.index', ['tab' => 'projects']) }}" class="btn btn-sm btn-primary fw-semibold shadow-xs">
                    <i class="bi bi-plus-circle me-1"></i> Kelola Pagu Proyek
                </a>
            @endif
        </div>
    </div>

    <!-- Screen 4 KPI Cards Strip -->
    <div class="row g-3 mb-4 d-print-none">
        <!-- KPI 1: Pagu Kontrak -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Total Pagu Kontrak</span>
                    <i class="bi bi-briefcase text-primary fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPaguAll, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <span>{{ count($projectRows) }} Proyek Terpantau</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Realisasi Aktual -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Realisasi Biaya Aktual</span>
                    <i class="bi bi-speedometer2 text-success fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-success">Rp {{ number_format($totalRealAll, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    Serapan Pagu: <strong class="text-dark">{{ $totalSerapanPct }}%</strong>
                </div>
            </div>
        </div>

        <!-- KPI 3: Sisa Pagu -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #06b6d4 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Sisa Pagu Tersedia</span>
                    <i class="bi bi-shield-check text-info fs-5"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalSisaAll, 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    Sisa Anggaran: <strong class="text-info">{{ $totalPaguAll > 0 ? round(($totalSisaAll / $totalPaguAll) * 100, 1) : 0 }}%</strong>
                </div>
            </div>
        </div>

        <!-- KPI 4: Kesehatan Portofolio -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Status Kesehatan Proyek</span>
                    <i class="bi bi-heart-pulse text-purple fs-5"></i>
                </div>
                <div class="d-flex align-items-center gap-1 my-1">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.72rem;">{{ $healthCounts['safe'] }} Sehat</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size: 0.72rem;">{{ $healthCounts['warning'] }} Waspada</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.72rem;">{{ $healthCounts['critical'] }} Kritis</span>
                </div>
                <div class="small text-muted">Berdasarkan rasio serapan pagu</div>
            </div>
        </div>
    </div>

    <!-- Screen Filter Bar (DIBUANG SAAT CETAK) -->
    <div class="card border-0 shadow-sm rounded-3 mb-3 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('monitoring.cost-kontrak') }}" id="costFilterForm" class="row g-2 align-items-center">
                <div class="col-md-3 col-sm-6">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Project ID / Nama Proyek / Klien..." value="{{ $filters['search'] ?? '' }}">
                </div>

                @if(!auth()->user()->hasRole('osm_service_manager'))
                    <div class="col-md-2 col-sm-6">
                        <select name="service_manager" class="form-select form-select-sm">
                            <option value="">-- Semua SM --</option>
                            @foreach($smList as $sm)
                                <option value="{{ $sm }}" {{ ($filters['service_manager'] ?? '') === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-2 col-sm-6">
                    <select name="client" class="form-select form-select-sm">
                        <option value="">-- Semua Klien --</option>
                        @foreach($clientList as $cl)
                            <option value="{{ $cl }}" {{ ($filters['client'] ?? '') === $cl ? 'selected' : '' }}>{{ $cl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <select name="health_status" class="form-select form-select-sm">
                        <option value="">-- Status Kesehatan --</option>
                        <option value="safe" {{ ($filters['health_status'] ?? '') === 'safe' ? 'selected' : '' }}>🟢 Sehat (&lt; 75%)</option>
                        <option value="warning" {{ ($filters['health_status'] ?? '') === 'warning' ? 'selected' : '' }}>🟡 Waspada (75 - 90%)</option>
                        <option value="critical" {{ ($filters['health_status'] ?? '') === 'critical' ? 'selected' : '' }}>🔴 Kritis (&gt; 90%)</option>
                    </select>
                </div>

                <div class="col-md-1 col-sm-6">
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Tahun</option>
                        @foreach($tahunList as $th)
                            <option value="{{ $th }}" {{ ($filters['tahun'] ?? '') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-12 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['service_manager']) || !empty($filters['client']) || !empty($filters['health_status']) || !empty($filters['tahun']))
                        <a href="{{ route('monitoring.cost-kontrak') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 3. TABEL DATA UTAMA (OPTIMAL UNTUK LAYAR DAN CETAK FORMAL)              -->
    <!-- ======================================================================= -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center d-print-none">
            <span class="fw-bold small text-muted text-uppercase">Daftar Komparasi Pagu Kontrak vs Realisasi Riil</span>
            <span class="small text-muted">{{ count($projectRows) }} Baris Data Ditemukan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 table-print-formal" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 4%; text-align: center;">No</th>
                        <th style="width: 11%; text-align: center;">Project ID</th>
                        <th style="width: 26%;">Nama Proyek &amp; Klien</th>
                        <th style="width: 13%;">Service Manager</th>
                        <th style="width: 13%; text-align: right;">Pagu Kontrak</th>
                        <th style="width: 13%; text-align: right;">Realisasi Biaya</th>
                        <th style="width: 7%; text-align: right;">Serapan</th>
                        <th style="width: 13%; text-align: right;">Sisa Pagu</th>
                        <th style="width: 8%; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectRows as $idx => $r)
                        <tr>
                            <td style="text-align: center;" class="text-muted">{{ $idx + 1 }}</td>
                            <td style="text-align: center;">
                                <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $r['project_id'] }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $r['project_name'] }}</div>
                                <div class="small text-muted" style="font-size: 0.72rem;">{{ $r['client'] }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $r['service_manager'] }}</span>
                            </td>
                            <td style="text-align: right;" class="font-monospace fw-bold text-dark">
                                Rp {{ number_format($r['pagu'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right;" class="font-monospace fw-bold text-primary">
                                Rp {{ number_format($r['realisasi'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right;" class="font-monospace fw-bold">
                                {{ $r['serapan_pct'] }}%
                            </td>
                            <td style="text-align: right;" class="font-monospace text-muted">
                                Rp {{ number_format($r['sisa'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                @if($r['status'] === 'critical')
                                    <span class="status-print-tag status-print-critical">KRITIS</span>
                                @elseif($r['status'] === 'warning')
                                    <span class="status-print-tag status-print-warning">WASPADA</span>
                                @else
                                    <span class="status-print-tag status-print-safe">SEHAT</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                                Tidak ada proyek yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 4. LEMBAR TANDA TANGAN RESMI (HANYA MUNCUL SAAT CETAK / PDF)             -->
    <!-- ======================================================================= -->
    <div class="print-signature-section">
        <div class="sig-block">
            <div class="sig-title">Dipersiapkan Oleh:</div>
            <div class="sig-name">{{ auth()->user()->name }}</div>
            <div class="sig-role">Staf Data Management Officer (DMO)</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Diverifikasi Oleh:</div>
            <div class="sig-name">
                {{ !empty($filters['service_manager']) ? $filters['service_manager'] : 'Service Manager Terkait' }}
            </div>
            <div class="sig-role">Service Manager / PM Lead</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Mengetahui &amp; Menyetujui:</div>
            <div class="sig-name">Group Head / OSM PGN</div>
            <div class="sig-role">Operations &amp; Service Management</div>
        </div>
    </div>

    <!-- Formal Print Footer -->
    <div class="print-footer-notice">
        <span>Dicetak melalui Sistem Monitoring Realisasi &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)</span>
        <span>Dokumen Resmi Internal Perusahaan &bull; Halaman Sah</span>
    </div>

    </div> {{-- /#printableReportArea --}}

</div>

@push('scripts')
<script>
function cetakLaporanCostKontrak() {
    const form = document.getElementById('costFilterForm');
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
