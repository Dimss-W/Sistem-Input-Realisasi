@extends('layouts.main')

@section('title', 'Dashboard Realisasi & Monitoring')
@section('page-title', 'Dashboard Realisasi & Monitoring')

@section('breadcrumb')
    <li class="breadcrumb-item active"><i class="bi bi-speedometer2 me-1"></i>Dashboard Realisasi</li>
@endsection

@push('styles')
<style>
    /* Exact Reference 5-KPI Cards (Compact, Neat, Pastel-Tinted, Zero Truncation) */
    .kpi-exact-grid {
        display: grid;
        grid-template-columns: 1fr 1.25fr 1.25fr 1.2fr 1fr;
        gap: 12px;
        margin-bottom: 22px;
    }
    .kpi-exact-card {
        border-radius: 10px;
        padding: 11px 8px 10px;
        text-align: center;
        border-width: 1px;
        border-style: solid;
        border-top-width: 4px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .kpi-exact-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .kpi-exact-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin-bottom: 4px;
        line-height: 1.2;
    }
    .kpi-exact-val {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(0.88rem, 1.0vw, 1.08rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
        line-height: 1.25;
        white-space: nowrap;
        margin-bottom: 6px;
    }
    .kpi-exact-pill {
        display: inline-block;
        font-size: 0.67rem;
        font-weight: 700;
        padding: 2.5px 11px;
        border-radius: 20px;
        line-height: 1.35;
    }

    /* 1. JUMLAH KONTRAK (Dark Slate Theme) */
    .kpi-card-kontrak {
        background: #F8FAFC;
        border-color: #E2E8F0;
        border-top-color: #1E293B;
    }
    .kpi-card-kontrak .kpi-exact-title { color: #334155; }
    .kpi-card-kontrak .kpi-exact-val   { color: #0F172A; }
    .kpi-card-kontrak .kpi-unit        { font-weight: 600; font-size: 0.85em; color: #64748B; margin-left: 3px; }
    .kpi-card-kontrak .kpi-exact-pill  { background: #F1F5F9; color: #334155; }

    /* 2. TOTAL NILAI KONTRAK (Blue Theme) */
    .kpi-card-pagu {
        background: #F0F7FF;
        border-color: #BFDBFE;
        border-top-color: #0284C7;
    }
    .kpi-card-pagu .kpi-exact-title { color: #0369A1; }
    .kpi-card-pagu .kpi-exact-val   { color: #0284C7; }
    .kpi-card-pagu .kpi-exact-pill  { background: #DBEAFE; color: #1D4ED8; }

    /* 3. TOTAL REALISASI (Green Theme) */
    .kpi-card-real {
        background: #F0FDF4;
        border-color: #BBF7D0;
        border-top-color: #16A34A;
    }
    .kpi-card-real .kpi-exact-title { color: #15803D; }
    .kpi-card-real .kpi-exact-val   { color: #15803D; }
    .kpi-card-real .kpi-exact-pill  { background: #DCFCE7; color: #15803D; }

    /* 4. TOTAL PROGNOSA (Amber Theme) */
    .kpi-card-prognosa {
        background: #FFFBEB;
        border-color: #FDE68A;
        border-top-color: #D97706;
    }
    .kpi-card-prognosa .kpi-exact-title { color: #B45309; }
    .kpi-card-prognosa .kpi-exact-val   { color: #D97706; }
    .kpi-card-prognosa .kpi-exact-pill  { background: #FEF3C7; color: #B45309; }

    /* 5. % CAPAIAN REALISASI (Purple Theme) */
    .kpi-card-ratio {
        background: #FAF5FF;
        border-color: #E9D5FF;
        border-top-color: #7C3AED;
    }
    .kpi-card-ratio .kpi-exact-title { color: #6D28D9; }
    .kpi-card-ratio .kpi-exact-val   { color: #7C3AED; }
    .kpi-card-ratio .kpi-exact-pill  { background: #EDE9FE; color: #6D28D9; }

    @media (max-width: 1099.98px) {
        .kpi-exact-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 640px) {
        .kpi-exact-grid {
            grid-template-columns: 1fr;
        }
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748B;
        white-space: nowrap;
    }
    .kpi-value {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 6px;
        letter-spacing: -0.02em;
        color: #0F172A;
    }
    .kpi-icon-glossy {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    .kpi-icon-glossy i { font-size: 1.15rem; color: #FFFFFF; }
    .kpi-badge-pill {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 6px;
    }

    .filter-card {
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .filter-card .form-select, .filter-card .form-control {
        font-size: 0.82rem; font-weight: 600;
        border-color: #E2E8F0; border-radius: 10px;
        height: 38px; padding-top: 0; padding-bottom: 0; color: #0F172A;
    }
    .filter-card .form-label {
        font-size: 0.7rem; font-weight: 800;
        letter-spacing: 0.06em; text-transform: uppercase;
        color: #64748B; margin-bottom: 5px;
    }

    /* Hero Action Buttons */
    .hero-actions-container {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .btn-hero-action {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 38px;
        padding: 0 14px;
        border-radius: 9px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        text-decoration: none;
    }
    .btn-hero-print {
        background: #FFFFFF;
        color: #0F172A !important;
        border: 1px solid #FFFFFF;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    }
    .btn-hero-print:hover {
        background: #F8FAFC;
        color: #0284C7 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    }
    .btn-hero-glass {
        background: rgba(255, 255, 255, 0.14);
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.26);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .btn-hero-glass:hover {
        background: rgba(255, 255, 255, 0.24);
        color: #FFFFFF !important;
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.45);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }

    .section-card {
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .section-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #F1F5F9;
        border-radius: 16px 16px 0 0;
        padding: 16px 20px 14px;
    }
    .section-card .card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem; font-weight: 800; color: #0F172A; margin: 0;
    }
    .section-card .card-subtitle {
        font-size: 0.75rem; color: #64748B; font-weight: 500; margin-top: 2px;
    }

    /* Custom Thin Scrollbar for Tables */
    .custom-scroll-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scroll-container::-webkit-scrollbar-track {
        background: #F8FAFC;
        border-radius: 4px;
    }
    .custom-scroll-container::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }
    .custom-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Modern Matrix Table Styling */
    .matrix-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .matrix-table thead th {
        background: #F8FAFC;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #E2E8F0;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .matrix-row-parent {
        cursor: pointer;
        transition: all 0.15s ease;
        border-bottom: 1px solid #F1F5F9;
    }
    .matrix-row-parent:hover {
        background: #F8FAFC;
    }
    .matrix-row-parent.expanded-row {
        background: #F0F9FF !important;
    }
    .matrix-row-parent td {
        padding: 13px 16px;
        vertical-align: middle;
        font-size: 0.82rem;
    }
    .matrix-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-variant-numeric: tabular-nums;
        font-weight: 700;
        white-space: nowrap;
    }
    .matrix-avatar {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0A2540 0%, #005A9C 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 90, 156, 0.2);
    }
    .matrix-rank-badge {
        font-size: 0.68rem;
        font-weight: 800;
        width: 22px; height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center; justify-content: center;
        margin-right: 8px;
    }
    .rank-gold { background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; }
    .rank-silver { background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; }
    .rank-bronze { background: #FFEDD5; color: #9A3412; border: 1px solid #FED7AA; }
    .rank-normal { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

    .matrix-progress-track {
        height: 7px;
        background: #E2E8F0;
        border-radius: 6px;
        overflow: hidden;
        min-width: 80px;
    }
    .matrix-progress-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.6s ease;
    }

    /* Accordion Rotation */
    .collapse-toggle-btn {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #0284C7;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .collapse-toggle-btn:hover {
        background: #F0F9FF;
        border-color: #BAE6FD;
        color: #0369A1;
    }
    .collapse-toggle-btn .chevron-icon {
        transition: transform 0.2s ease;
        font-size: 0.7rem;
    }
    .collapse-toggle-btn[aria-expanded="true"] {
        background: #0284C7;
        border-color: #0284C7;
        color: #FFFFFF;
    }
    .collapse-toggle-btn[aria-expanded="true"] .chevron-icon {
        transform: rotate(180deg);
    }

    /* Child Project Table */
    .matrix-child-container {
        background: #F8FAFC;
        border-bottom: 2px solid #E2E8F0;
        padding: 14px 20px;
    }
    .project-subtable {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        overflow: hidden;
    }
    .project-subtable thead th {
        background: #F1F5F9;
        color: #475569;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 8px 12px;
        border-bottom: 1px solid #E2E8F0;
    }
    .project-subtable tbody td {
        padding: 9px 12px;
        font-size: 0.78rem;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }
    .project-subtable tbody tr:hover {
        background: #F8FAFC;
    }

    /* Tab Switcher Pills */
    .nav-pills-matrix .nav-link {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748B;
        border-radius: 8px;
        padding: 6px 14px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .nav-pills-matrix .nav-link.active {
        background: #0284C7;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }
    .nav-pills-matrix .nav-link:hover:not(.active) {
        background: #F1F5F9;
        color: #0F172A;
    }

    .donut-wrap { position: relative; width: 150px; height: 150px; }
    .donut-center {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
    }
    .donut-center .pct {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1;
    }
    .donut-center .lbl {
        font-size: 0.62rem; font-weight: 800; letter-spacing: 0.08em;
        text-transform: uppercase; color: #64748B; margin-top: 3px;
    }
    .legend-pill { display: inline-flex; align-items: center; gap: 8px; font-size: 0.78rem; font-weight: 700; color: #334155; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    @media (max-width: 991.98px) {
        .kpi-value { font-size: 1.1rem; }
        .donut-wrap { width: 135px; height: 135px; }
        .donut-center .pct { font-size: 1.15rem; }
    }
    @media (max-width: 767.98px) {
        .kpi-card .card-body { padding: 14px 14px; }
        .kpi-value { font-size: 1rem; }
    }
    @media (max-width: 575.98px) {
        .kpi-row .col { flex: 0 0 50%; max-width: 50%; }
    }
</style>
@endpush

@section('content')

<div class="screen-only-dashboard">

<!-- Hero Executive Banner (Clean Sleek Modern Design) -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" 
     style="background: linear-gradient(135deg, #0A192F 0%, #112240 55%, #1E3A8A 100%);">
    <div class="card-body p-4 text-white position-relative">
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2.5" 
                     style="background: rgba(255, 255, 255, 0.12); font-size: 0.74rem; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.2); color: #E0F2FE;">
                    <span class="badge bg-success rounded-circle p-1" style="width:6px; height:6px;"></span>
                    PT PGAS Telekomunikasi Nusantara (PGNCOM)
                </div>
                <h3 class="fw-bold text-white mb-1.5" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                    Dashboard Realisasi &amp; Monitoring Finansial
                </h3>
                <p class="text-white text-opacity-80 mb-0 small" style="max-width: 580px; line-height: 1.5; font-size: 0.82rem;">
                    Monitoring terintegrasi nilai kontrak, progres penyerapan realisasi biaya, dan evaluasi prognosa anggaran berjalan.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="hero-actions-container justify-content-lg-end">
                    <a href="{{ route('sm.executive-summary', request()->only(['service_manager', 'tahun'])) }}" target="_blank" class="btn-hero-action btn-hero-print btn-print-hide" title="Buka & Cetak Ringkasan Eksekutif Resmi Portofolio (Format A4)">
                        <i class="bi bi-printer-fill text-primary"></i>
                        <span>Cetak Ringkasan (A4)</span>
                    </a>
                    <a href="{{ route('realisasi.index') }}" class="btn-hero-action btn-hero-glass btn-print-hide" title="Buka Tabel Realisasi Lengkap">
                        <i class="bi bi-table"></i>
                        <span>Tabel Realisasi</span>
                    </a>
                    <a href="{{ route('calendar.index') }}" class="btn-hero-action btn-hero-glass btn-print-hide" title="Jadwal Termin & Jatuh Tempo">
                        <i class="bi bi-calendar3"></i>
                        <span>Kalender</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->hasRole(['dmo', 'admin']))
{{-- DMO ACTION CENTER & OPERATIONAL COCKPIT --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); border: 1px solid #E2E8F0 !important;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom border-light-subtle">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284C7, #0369A1); color: #fff;">
                    <i class="bi bi-speedometer2 fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold text-dark mb-0 fs-6">DMO Action Center &amp; Operational Cockpit</h6>
                        <span class="badge rounded-pill" style="background: rgba(2, 132, 199, 0.12); color: #0284C7; font-weight: 700; font-size: 0.68rem; letter-spacing: 0.04em;">
                            <i class="bi bi-person-badge me-1"></i>DATA MANAGEMENT OFFICER
                        </span>
                    </div>
                    <p class="text-muted mb-0 small" style="font-size: 0.78rem;">Pusat kendali operasional BASTO, tindak lanjut revisi QC, dan verifikasi kelengkapan transaksi proyek.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('basto.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Buat BASTO Baru</span>
                </a>
                <a href="{{ route('realisasi.import.form') }}" class="btn btn-sm btn-outline-primary bg-white rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    <span>Import Realisasi</span>
                </a>
            </div>
        </div>

        <div class="row g-3">
            {{-- Card 1: Perlu Revisi QC --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-3 h-100 shadow-sm transition-all position-relative overflow-hidden" 
                     style="background: {{ ($dmoRevisionCount ?? 0) > 0 ? '#FFF1F2' : '#FFFFFF' }}; border: 1px solid {{ ($dmoRevisionCount ?? 0) > 0 ? '#FECDD3' : '#F1F5F9' }} !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: {{ ($dmoRevisionCount ?? 0) > 0 ? '#E11D48' : '#64748B' }};">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i>Perlu Revisi QC
                            </span>
                            @if(($dmoRevisionCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill px-2 py-1 fs-8 fw-bold animate-pulse">Action Required</span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8 fw-bold"><i class="bi bi-check2"></i> Clear</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <h3 class="fw-extrabold mb-0" style="color: {{ ($dmoRevisionCount ?? 0) > 0 ? '#BE123C' : '#0F172A' }}; font-size: 1.75rem;">
                                {{ $dmoRevisionCount ?? 0 }}
                            </h3>
                            <span class="text-muted small">dokumen</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                {{ ($dmoRevisionCount ?? 0) > 0 ? 'Catatan verifikasi QC menunggu perbaikan' : 'Semua catatan QC terselesaikan' }}
                            </small>
                            <a href="{{ route('basto.index', ['tab' => 'revision_needed']) }}" class="text-decoration-none fw-bold small ms-1" style="font-size: 0.74rem; color: #BE123C;">
                                Tindak Lanjut &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Draf BASTO Siap Submit --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-3 h-100 shadow-sm transition-all position-relative overflow-hidden" 
                     style="background: {{ ($dmoDraftCount ?? 0) > 0 ? '#FFFBEB' : '#FFFFFF' }}; border: 1px solid {{ ($dmoDraftCount ?? 0) > 0 ? '#FDE68A' : '#F1F5F9' }} !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: {{ ($dmoDraftCount ?? 0) > 0 ? '#D97706' : '#64748B' }};">
                                <i class="bi bi-file-earmark-text-fill me-1"></i>Draf BASTO Tersimpan
                            </span>
                            @if(($dmoDraftCount ?? 0) > 0)
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 fs-8 fw-bold">Siap Diajukan</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 fs-8 fw-bold">Nol Draf</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <h3 class="fw-extrabold mb-0" style="color: {{ ($dmoDraftCount ?? 0) > 0 ? '#B45309' : '#0F172A' }}; font-size: 1.75rem;">
                                {{ $dmoDraftCount ?? 0 }}
                            </h3>
                            <span class="text-muted small">draf tersimpan</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                {{ ($dmoDraftCount ?? 0) > 0 ? 'Dapat diajukan serentak via Batch Submit' : 'Semua draf telah diajukan ke SM' }}
                            </small>
                            <a href="{{ route('basto.index', ['tab' => 'draft']) }}" class="text-decoration-none fw-bold small ms-1" style="font-size: 0.74rem; color: #B45309;">
                                Batch Submit &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Proyek Tanpa BASTO (Unlinked Tracker) --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-3 h-100 shadow-sm transition-all position-relative overflow-hidden" 
                     style="background: #FFFFFF; border: 1px solid #F1F5F9 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #475569;">
                                <i class="bi bi-link-45deg me-1"></i>Proyek Belum Ber-BASTO
                            </span>
                            <span class="badge bg-info-subtle text-info rounded-pill px-2 py-0.5 fs-8 fw-bold">Unlinked Tracker</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <h3 class="fw-extrabold text-dark mb-0" style="font-size: 1.75rem;">
                                {{ $dmoProjectsWithoutBastoCount ?? 0 }}
                            </h3>
                            <span class="text-muted small">ID Proyek</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                Memiliki realisasi namun berkas BASTO belum ada
                            </small>
                            <a href="{{ route('realisasi.index', ['basto_status' => 'no_basto']) }}" class="text-decoration-none fw-bold small ms-1 text-primary" style="font-size: 0.74rem;">
                                Lacak Proyek &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Transaksi Bulan Berjalan --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-3 h-100 shadow-sm transition-all position-relative overflow-hidden" 
                     style="background: #FFFFFF; border: 1px solid #F1F5F9 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #15803D;">
                                <i class="bi bi-calendar-check-fill me-1"></i>Transaksi Realisasi
                            </span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fs-8 fw-bold">Bulan Berjalan</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <h3 class="fw-extrabold mb-0 text-dark" style="font-size: 1.75rem;">
                                {{ number_format($dmoMonthlyTransactionsCount ?? 0, 0, ',', '.') }}
                            </h3>
                            <span class="text-muted small">entri tercatat</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                Total record transaksi di periode aktif saat ini
                            </small>
                            <a href="{{ route('realisasi.index') }}" class="text-decoration-none fw-bold small ms-1 text-success" style="font-size: 0.74rem;">
                                Buka Tabel &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- FILTER BAR --}}
<div class="filter-card mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">
                <i class="bi bi-funnel-fill text-primary me-1.5"></i>Filter Parameter Monitoring
            </h6>
            @if(isset($tahunList) && $tahunList->count() > 0)
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <span class="text-muted fw-bold small me-1" style="font-size: 0.72rem;">
                    <i class="bi bi-calendar3 me-1"></i>Pilih Cepat Tahun:
                </span>
                <a href="{{ route('dashboard', array_merge(request()->except('tahun'))) }}" 
                   class="badge rounded-pill text-decoration-none px-2.5 py-1 transition-all {{ empty($filterTahun) ? 'bg-primary text-white shadow-xs' : 'bg-light text-secondary border' }}" 
                   style="font-size: 0.72rem;">
                    Semua Tahun
                </a>
                @foreach($tahunList as $th)
                    <a href="{{ route('dashboard', array_merge(request()->all(), ['tahun' => $th])) }}" 
                       class="badge rounded-pill text-decoration-none px-2.5 py-1 transition-all {{ ($filterTahun ?? '') == $th ? 'bg-primary text-white shadow-xs' : 'bg-light text-secondary border' }}" 
                       style="font-size: 0.72rem;">
                        Tahun {{ $th }}
                    </a>
                @endforeach
            </div>
            @endif
        </div>
        <form action="{{ route('dashboard') }}" method="GET">
            <div class="row g-2.5 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Project Name</label>
                    <select name="project_name" class="form-select">
                        <option value="">Semua Project</option>
                        @foreach($projectNameList as $name)
                            <option value="{{ $name }}" {{ $filterProject === $name ? 'selected' : '' }}>{{ Str::limit($name, 42) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Service Manager / PIC</label>
                    <select name="service_manager" class="form-select" {{ auth()->user()->hasRole(['osm_service_manager', 'sales']) ? 'disabled' : '' }}>
                        @if(auth()->user()->hasRole(['osm_service_manager', 'sales']))
                            <option value="{{ auth()->user()->name }}" selected>{{ auth()->user()->name }}</option>
                        @else
                            <option value="">Semua SM &amp; PIC</option>
                            @foreach($serviceManagerList as $sm)
                                <option value="{{ $sm }}" {{ $filterSM === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label">Client / Pelanggan</label>
                    <select name="client" class="form-select">
                        <option value="">Semua Client</option>
                        @foreach($clientList as $client)
                            <option value="{{ $client }}" {{ $filterClient === $client ? 'selected' : '' }}>{{ $client }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label">Periode / Kuartal</label>
                    <select name="periode" class="form-select">
                        <option value="">Semua Periode</option>
                        <optgroup label="Rentang Kuartal &amp; Semester">
                            <option value="Q1" {{ $filterPeriode === 'Q1' ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                            <option value="Q2" {{ $filterPeriode === 'Q2' ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                            <option value="Q3" {{ $filterPeriode === 'Q3' ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                            <option value="Q4" {{ $filterPeriode === 'Q4' ? 'selected' : '' }}>Q4 (Okt - Des)</option>
                            <option value="SEMESTER 1" {{ $filterPeriode === 'SEMESTER 1' ? 'selected' : '' }}>Semester 1 (Jan - Jun)</option>
                            <option value="SEMESTER 2" {{ $filterPeriode === 'SEMESTER 2' ? 'selected' : '' }}>Semester 2 (Jul - Des)</option>
                        </optgroup>
                        <optgroup label="Bulan Spesifik">
                            @foreach($periodeList as $p)
                                <option value="{{ $p }}" {{ $filterPeriode === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua</option>
                        @foreach($tahunList ?? [] as $th)
                            <option value="{{ $th }}" {{ ($filterTahun ?? '') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="d-flex justify-content-end align-items-center mb-1" style="min-height: 20px;">
                        @if($filterProject || $filterSM || $filterClient || $filterPeriode || !empty($filterTahun))
                            <a href="{{ route('dashboard') }}" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem; border-radius: 6px;" title="Reset Semua Filter">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-md w-100 fw-bold rounded-3 d-flex align-items-center justify-content-center gap-1.5" style="height:38px; font-size:0.85rem;">
                        <i class="bi bi-funnel-fill"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ACTION CENTER FOR SERVICE MANAGER / ADMIN --}}
@if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff; border-left: 5px solid #0284c7 !important;">
    <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill" style="font-size:0.75rem;">
                <i class="bi bi-lightning-charge-fill me-1"></i>Action Center
            </span>
            <span class="small fw-semibold text-dark">Perlu Perhatian & Tindakan Anda:</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('basto.index', ['tab' => 'pending_review']) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs {{ ($actionCounts['pending_basto'] ?? 0) > 0 ? 'btn-danger' : 'btn-light text-muted' }}" title="Lihat BASTO yang menunggu approval Anda">
                <i class="bi bi-clipboard-check"></i>
                <span>BASTO Review</span>
                <span class="badge {{ ($actionCounts['pending_basto'] ?? 0) > 0 ? 'bg-white text-danger' : 'bg-secondary text-white' }} rounded-pill ms-1">{{ $actionCounts['pending_basto'] ?? 0 }}</span>
            </a>
            <a href="#sectionProjectHealth" class="btn btn-sm rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs {{ ($actionCounts['critical_projects'] ?? 0) > 0 ? 'btn-warning text-dark' : 'btn-light text-muted' }}" title="Proyek dengan penyerapan pagu >= 90%">
                <i class="bi bi-exclamation-octagon"></i>
                <span>Proyek Kritis</span>
                <span class="badge {{ ($actionCounts['critical_projects'] ?? 0) > 0 ? 'bg-dark text-white' : 'bg-secondary text-white' }} rounded-pill ms-1">{{ $actionCounts['critical_projects'] ?? 0 }}</span>
            </a>
            <a href="{{ route('realisasi.index', ['status' => 'PENDING']) }}" class="btn btn-sm btn-light text-dark border rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs" title="Realisasi belum ber-invoice">
                <i class="bi bi-receipt"></i>
                <span>Realisasi Unbilled</span>
                <span class="badge bg-secondary rounded-pill ms-1">{{ $actionCounts['unbilled_realisasi'] ?? 0 }}</span>
            </a>
            <a href="{{ route('sm.executive-summary', request()->only(['service_manager', 'tahun'])) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs ms-lg-2">
                <i class="bi bi-printer me-1"></i> Cetak Ringkasan Eksekutif
            </a>
        </div>
    </div>
</div>
@endif

{{-- 5 KPI CARDS (Exact Reference Style: Modern, Centered, Zero Truncation, Space-Efficient) --}}
<div class="kpi-exact-grid">

    {{-- 1. Jumlah Kontrak --}}
    <div class="kpi-exact-card kpi-card-kontrak">
        <div class="kpi-exact-title">Jumlah Kontrak</div>
        <div class="kpi-exact-val">
            <span>{{ number_format($stats['total_kontrak']) }}</span>
            <span class="kpi-unit">Proyek</span>
        </div>
        <div>
            <span class="kpi-exact-pill">Portofolio Aktif</span>
        </div>
    </div>

    {{-- 2. Total Nilai Kontrak --}}
    <div class="kpi-exact-card kpi-card-pagu">
        <div class="kpi-exact-title">Total Nilai Kontrak</div>
        <div class="kpi-exact-val">
            Rp {{ number_format($stats['total_nilai_kontrak'], 0, ',', '.') }}
        </div>
        <div>
            <span class="kpi-exact-pill">Pagu Anggaran</span>
        </div>
    </div>

    {{-- 3. Total Realisasi --}}
    <div class="kpi-exact-card kpi-card-real">
        <div class="kpi-exact-title">Total Realisasi</div>
        <div class="kpi-exact-val">
            Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}
        </div>
        <div>
            <span class="kpi-exact-pill">Biaya Terpakai</span>
        </div>
    </div>

    {{-- 4. Total Prognosa --}}
    <div class="kpi-exact-card kpi-card-prognosa">
        <div class="kpi-exact-title">Total Prognosa</div>
        <div class="kpi-exact-val">
            Rp {{ number_format($stats['total_prognosa'], 0, ',', '.') }}
        </div>
        <div>
            <span class="kpi-exact-pill">Estimasi Selesai</span>
        </div>
    </div>

    {{-- 5. % Capaian Realisasi --}}
    <div class="kpi-exact-card kpi-card-ratio">
        <div class="kpi-exact-title">% Capaian Realisasi</div>
        <div class="kpi-exact-val">
            {{ number_format($stats['persentase_realisasi'], 2) }}%
        </div>
        <div>
            <span class="kpi-exact-pill">Rasio Capaian</span>
        </div>
    </div>

</div>

{{-- MONITORING KESEHATAN ANGGARAN PORTOFOLIO PROYEK --}}
@if(!empty($projectHealthList) && count($projectHealthList) > 0)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="sectionProjectHealth">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="card-title mb-0 fs-6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-shield-heart-fill text-danger"></i>
                <span>Monitoring Kesehatan Anggaran Proyek (Project Budget Health)</span>
            </div>
            <div class="small text-muted mt-0.5">Sistem deteksi dini penyerapan pagu kontrak: Sehat (&lt;75%), Waspada (75-90%), dan Kritis (&gt;90%).</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold small">
                <i class="bi bi-check-circle-fill me-1"></i>{{ $healthCounts['safe'] ?? 0 }} Sehat
            </span>
            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $healthCounts['warning'] ?? 0 }} Waspada
            </span>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill fw-bold small">
                <i class="bi bi-exclamation-octagon-fill me-1"></i>{{ $healthCounts['critical'] ?? 0 }} Kritis
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">
                <tr>
                    <th class="ps-4 py-3">Project ID &amp; Nama</th>
                    <th>Service Manager</th>
                    <th class="text-end">Pagu Kontrak</th>
                    <th class="text-end">Realisasi Biaya</th>
                    <th class="text-end">Sisa Saldo</th>
                    <th style="width: 180px;">Rasio Penyerapan</th>
                    <th class="text-center pe-4">Status &amp; Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projectHealthList as $ph)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $ph['project_name'] }}</div>
                        <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.7rem;">{{ $ph['project_id'] }}</span>
                    </td>
                    <td>{{ $ph['service_manager'] ?? '—' }}</td>
                    <td class="text-end font-monospace fw-semibold">Rp {{ number_format($ph['pagu'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace fw-bold text-primary">Rp {{ number_format($ph['realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace fw-semibold {{ $ph['sisa'] < 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($ph['sisa'], 0, ',', '.') }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold fs-9 {{ $ph['status'] === 'critical' ? 'text-danger' : ($ph['status'] === 'warning' ? 'text-warning' : 'text-success') }}">
                                {{ $ph['serapan_pct'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                            <div class="progress-bar bg-{{ $ph['badge_class'] }}" role="progressbar" 
                                 style="width: {{ min(100, $ph['serapan_pct']) }}%;" 
                                 aria-valuenow="{{ $ph['serapan_pct'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <span class="badge bg-{{ $ph['badge_class'] }} bg-opacity-10 text-{{ $ph['badge_class'] }} border border-{{ $ph['badge_class'] }} px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                            {{ $ph['status_label'] }}
                        </span>
                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                            @if($ph['status'] === 'critical')
                                <span class="text-danger fw-semibold"><i class="bi bi-arrow-up-right-circle me-0.5"></i>Perlu Addendum Pagu</span>
                            @elseif($ph['status'] === 'warning')
                                <span class="text-warning text-opacity-90 fw-semibold"><i class="bi bi-eye me-0.5"></i>Pantau Pengeluaran</span>
                            @else
                                <span class="text-success"><i class="bi bi-shield-check me-0.5"></i>Anggaran Aman</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ROW 1: TREN BULANAN (REALISASI VS PROGNOSA) + GAUGE CAPAIAN ANGGARAN --}}
<div class="row g-3 mb-4">

    {{-- Tren Bulanan (Realisasi vs Prognosa) --}}
    <div class="col-xl-8 col-lg-8">
        <div class="section-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="card-title"><i class="bi bi-graph-up text-primary me-2"></i>Tren Realisasi vs Prognosa Bulanan</div>
                    <div class="card-subtitle">Perbandingan pengeluaran riil aktual vs estimasi anggaran (Januari - Desember {{ date('Y') }})</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill" style="background:#eff6ff; color:#0284c7; font-size:0.7rem; font-weight:700; padding:5px 12px; border:1px solid #bfdbfe;">
                        <i class="bi bi-calendar3 me-1"></i>Tahun {{ date('Y') }}
                    </span>
                </div>
            </div>
            <div class="card-body p-3" style="height:320px;">
                <canvas id="chartMonthlyTrend"></canvas>
            </div>
        </div>
    </div>

    {{-- Gauge Capaian Anggaran --}}
    <div class="col-xl-4 col-lg-4">
        <div class="section-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="card-title"><i class="bi bi-pie-chart-fill text-success me-2"></i>Rasio Penyerapan Anggaran</div>
                    <div class="card-subtitle">Realisasi kumulatif terhadap total nilai kontrak</div>
                </div>
                <span class="badge rounded-pill {{ $stats['persentase_realisasi'] > 100 ? 'bg-danger text-white' : 'bg-success-subtle text-success border border-success-subtle' }}" style="font-size:0.7rem; font-weight:700; padding:5px 10px;">
                    {{ $stats['persentase_realisasi'] > 100 ? 'Over Budget' : 'On Track' }}
                </span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3 p-4">
                <div class="donut-wrap">
                    <canvas id="chartComparisonGauge"></canvas>
                    <div class="donut-center">
                        <div class="pct">{{ number_format($stats['persentase_realisasi'], 1) }}%</div>
                        <div class="lbl">Rasio Capaian</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <div class="legend-pill" style="font-size:0.75rem;">
                        <span class="legend-dot" style="background:#10b981;"></span>
                        Realisasi: <strong>Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="legend-pill" style="font-size:0.75rem;">
                        <span class="legend-dot" style="background:#e2e8f0; border:1px solid #cbd5e1;"></span>
                        Sisa Pagu: <strong>Rp {{ number_format(max(0, $stats['total_nilai_kontrak'] - $stats['total_realisasi']), 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ROW 2: TOP KLIEN (KONTRAK VS REALISASI) + BEBAN KERJA SERVICE MANAGER --}}
<div class="row g-3 mb-4">

    {{-- Top Klien: Nilai Kontrak vs Realisasi (Horizontal Grouped Bar) --}}
    <div class="col-xl-6 col-lg-6">
        <div class="section-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="card-title"><i class="bi bi-buildings text-primary me-2"></i>Top Klien: Nilai Kontrak vs Realisasi</div>
                    <div class="card-subtitle">Perbandingan pagu kontrak dan penyerapan biaya per klien utama</div>
                </div>
                <span class="badge rounded-pill" style="background:#e0f2fe; color:#0369a1; font-size:0.7rem; font-weight:700; padding:5px 12px; border:1px solid #bae6fd;">
                    Top {{ min(6, $nilaiKontrakPerClient->count()) }} Klien
                </span>
            </div>
            <div class="card-body p-3" style="height:320px;">
                <canvas id="chartTopClients"></canvas>
            </div>
        </div>
    </div>

    {{-- Beban Kerja & Nilai Kontrak per Service Manager --}}
    <div class="col-xl-6 col-lg-6">
        <div class="section-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="card-title"><i class="bi bi-person-badge text-primary me-2"></i>Beban Kerja per Service Manager</div>
                    <div class="card-subtitle">Jumlah kontrak (batang) dan total nilai rupiah (garis)</div>
                </div>
                <span class="badge rounded-pill" style="background:#fef3c7; color:#92400e; font-size:0.7rem; font-weight:700; padding:5px 12px; border:1px solid #fde68a;">
                    {{ $smStats->count() }} SM / PIC
                </span>
            </div>
            <div class="card-body p-3" style="height:320px;">
                <canvas id="chartSMStats"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- MASTER PORTOFOLIO & REALISASI KONTRAK PER VENDOR / KLIEN --}}
@php
    $realLookup     = $realisasiPerClient->keyBy('project_client');
    $contractLookup = $nilaiKontrakPerClient->keyBy('project_client');
    $allVendorClients = $nilaiKontrakPerClient->pluck('project_client')
        ->merge($realisasiPerClient->pluck('project_client'))
        ->unique()
        ->values();

    // Sort by contract total descending
    $sortedVendorClients = $allVendorClients->sortByDesc(function($client) use ($contractLookup) {
        return $contractLookup->get($client)->total ?? 0;
    })->values();

    $grandContract = $stats['total_nilai_kontrak'] ?: 1;
    $grandReal     = $stats['total_realisasi'] ?: 0;
@endphp

<div class="card section-card shadow-sm mb-4 border-0">
    {{-- Card Header with Tabs --}}
    <div class="card-header bg-white border-bottom p-3 p-md-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3" style="background:#EFF6FF; color:#0284C7;">
                        <i class="bi bi-diagram-3-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0" style="font-size: 1.05rem;">
                            Matriks Kontrak &amp; Realisasi per Vendor / Klien
                        </h5>
                        <div class="card-subtitle text-muted" style="font-size: 0.78rem;">
                            Monitoring komparasi pagu kontrak, serapan realisasi biaya, sisa anggaran, dan rincian per proyek
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Switcher --}}
            <ul class="nav nav-pills nav-pills-matrix" id="vendorMatrixTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-matrix-btn" data-bs-toggle="pill" data-bs-target="#tab-matrix-pane" type="button" role="tab" aria-selected="true">
                        <i class="bi bi-grid-1x2-fill me-1"></i>Matriks Terpadu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-contract-btn" data-bs-toggle="pill" data-bs-target="#tab-contract-pane" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-wallet2 me-1"></i>Pagu Kontrak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-realisasi-btn" data-bs-toggle="pill" data-bs-target="#tab-realisasi-pane" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-check2-circle me-1"></i>Realisasi Biaya
                    </button>
                </li>
            </ul>
        </div>

        {{-- Mini Summary Ribbon Strip --}}
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: #F1F5F9 !important;">
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.73rem; font-weight: 700; color: #475569;">
                <i class="bi bi-buildings text-primary"></i>
                <span>Total: <strong class="text-dark">{{ $sortedVendorClients->count() }} Klien / Vendor</strong></span>
            </div>
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 0.73rem; font-weight: 700; color: #1E3A8A;">
                <i class="bi bi-cash-stack text-primary"></i>
                <span>Total Pagu: <strong class="text-primary">Rp {{ number_format($grandContract, 0, ',', '.') }}</strong></span>
            </div>
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #ECFDF5; border: 1px solid #A7F3D0; font-size: 0.73rem; font-weight: 700; color: #065F46;">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span>Total Realisasi: <strong class="text-success">Rp {{ number_format($grandReal, 0, ',', '.') }}</strong></span>
            </div>
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #FAF5FF; border: 1px solid #DDD6FE; font-size: 0.73rem; font-weight: 700; color: #6D28D9;">
                <i class="bi bi-pie-chart-fill" style="color:#7C3AED;"></i>
                <span>Rata-rata Serapan: <strong>{{ number_format($stats['persentase_realisasi'], 1) }}%</strong></span>
            </div>
        </div>
    </div>

    {{-- Card Body: Tab Content --}}
    <div class="card-body p-0">
        <div class="tab-content" id="vendorMatrixTabsContent">

            {{-- TAB 1: MATRIKS TERPADU (UNIFIED COMPARATIVE MATRIX) --}}
            <div class="tab-pane fade show active" id="tab-matrix-pane" role="tabpanel" tabindex="0">
                <div class="custom-scroll-container" style="max-height: 540px; overflow-y: auto;">
                    <table class="table matrix-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 24%; min-width: 220px;"># / Vendor &amp; Klien</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Pagu Kontrak</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Realisasi Biaya</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Sisa Pagu Anggaran</th>
                                <th style="width: 14%; min-width: 130px;">% Penyerapan</th>
                                <th style="width: 8%; min-width: 110px;" class="text-center">Rincian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sortedVendorClients as $index => $client)
                                @php
                                    $cVal    = $contractLookup->get($client)->total ?? 0;
                                    $rVal    = $realLookup->get($client)->total ?? 0;
                                    $sisaVal = $cVal - $rVal;
                                    $pctSerap = $cVal > 0 ? min(100, round(($rVal / $cVal) * 100, 1)) : ($rVal > 0 ? 100 : 0);
                                    $pctShare = round(($cVal / $grandContract) * 100, 1);

                                    $cProjects = $vendorContractsBreakdown[$client] ?? collect();
                                    $rProjects = ($vendorRealisasiBreakdown[$client] ?? collect())->keyBy('project_id');
                                    $allProjIds = $cProjects->pluck('project_id')->merge($rProjects->pluck('project_id'))->unique()->values();

                                    $collapseId = 'matrix_client_' . $index;
                                    $rankBadgeClass = $index === 0 ? 'rank-gold' : ($index === 1 ? 'rank-silver' : ($index === 2 ? 'rank-bronze' : 'rank-normal'));

                                    // Progress bar gradient based on serapan
                                    if ($pctSerap >= 80) {
                                        $barGradient = 'linear-gradient(90deg, #10B981, #059669)';
                                        $badgeBg = '#ECFDF5'; $badgeColor = '#065F46';
                                    } elseif ($pctSerap >= 40) {
                                        $barGradient = 'linear-gradient(90deg, #38BDF8, #0284C7)';
                                        $badgeBg = '#F0F9FF'; $badgeColor = '#0284C7';
                                    } else {
                                        $barGradient = 'linear-gradient(90deg, #818CF8, #4F46E5)';
                                        $badgeBg = '#EEF2FF'; $badgeColor = '#4338CA';
                                    }
                                @endphp
                                <tr class="matrix-row-parent" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="matrix-rank-badge {{ $rankBadgeClass }}">{{ $index + 1 }}</span>
                                            <div class="matrix-avatar me-2.5">
                                                {{ strtoupper(substr($client, 0, min(3, strlen($client)))) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $client }}</div>
                                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                                    <span class="badge rounded-pill" style="background:#F1F5F9; color:#475569; font-size:0.65rem; font-weight:700; padding: 2px 7px;">
                                                        {{ $allProjIds->count() }} Proyek
                                                    </span>
                                                    <span class="text-muted" style="font-size:0.68rem;">({{ $pctShare }}% portofolio)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num text-dark" style="font-size: 0.85rem;">
                                            Rp {{ number_format($cVal, 0, ',', '.') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Pagu Total</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num text-success" style="font-size: 0.85rem;">
                                            Rp {{ number_format($rVal, 0, ',', '.') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Biaya Terpakai</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num {{ $sisaVal >= 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 0.85rem;">
                                            Rp {{ number_format($sisaVal, 0, ',', '.') }}
                                        </div>
                                        <div style="font-size: 0.68rem;">
                                            @if($sisaVal >= 0)
                                                <span class="badge rounded-pill bg-light text-success" style="font-size:0.62rem; font-weight:700; border:1px solid #D1FAE5;">Sisa Aman</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger-subtle text-danger" style="font-size:0.62rem; font-weight:700;">Over Budget</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge rounded-pill" style="background:{{ $badgeBg }}; color:{{ $badgeColor }}; font-size:0.68rem; font-weight:800; padding:2px 7px;">
                                                {{ $pctSerap }}%
                                            </span>
                                        </div>
                                        <div class="matrix-progress-track">
                                            <div class="matrix-progress-fill" style="width: {{ $pctSerap }}%; background: {{ $barGradient }};"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="collapse-toggle-btn" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                            <span>Rincian</span>
                                            <i class="bi bi-chevron-down chevron-icon"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- EXPANDED CHILD LEDGER ROW --}}
                                <tr class="collapse-child-row">
                                    <td colspan="6" class="p-0 border-0">
                                        <div class="collapse" id="{{ $collapseId }}">
                                            <div class="matrix-child-container">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-bold text-dark" style="font-size: 0.78rem;">
                                                        <i class="bi bi-folder2-open text-primary me-1.5"></i>
                                                        Rincian Kontrak &amp; Realisasi Proyek Klien: <strong>{{ $client }}</strong>
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.72rem;">
                                                        Total {{ $allProjIds->count() }} Proyek Terdaftar
                                                    </span>
                                                </div>
                                                <div class="table-responsive project-subtable shadow-none">
                                                    <table class="table table-sm mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 14%;">Kode Proyek</th>
                                                                <th style="width: 34%;">Nama Proyek</th>
                                                                <th style="width: 16%;" class="text-end">Nilai Kontrak</th>
                                                                <th style="width: 16%;" class="text-end">Realisasi</th>
                                                                <th style="width: 12%;" class="text-end">Sisa Pagu</th>
                                                                <th style="width: 8%;" class="text-center">Serapan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($allProjIds as $pid)
                                                                @php
                                                                    $cP = $cProjects->firstWhere('project_id', $pid);
                                                                    $rP = $rProjects->get($pid);
                                                                    $pName = $cP->project_name ?? ($rP->project_name ?? 'Proyek Tanpa Nama');
                                                                    $pValue = $cP->project_value ?? 0;
                                                                    $rValue = $rP->total ?? 0;
                                                                    $pSisa  = $pValue - $rValue;
                                                                    $pPct   = $pValue > 0 ? min(100, round(($rValue / $pValue) * 100, 1)) : ($rValue > 0 ? 100 : 0);
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        <span class="badge" style="background:#E0F2FE; color:#0284C7; font-size:0.7rem; font-weight:700;">
                                                                            {{ $pid }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="text-dark fw-semibold text-truncate" style="max-width: 360px;" title="{{ $pName }}">
                                                                            {{ $pName }}
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end matrix-num text-muted" style="font-size: 0.76rem;">
                                                                        Rp {{ number_format($pValue, 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-end matrix-num text-success" style="font-size: 0.76rem;">
                                                                        Rp {{ number_format($rValue, 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-end matrix-num {{ $pSisa >= 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 0.76rem;">
                                                                        Rp {{ number_format($pSisa, 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge rounded-pill" style="background: {{ $pPct >= 80 ? '#ECFDF5; color:#065F46;' : ($pPct >= 40 ? '#F0F9FF; color:#0284C7;' : '#F8FAFC; color:#475569; border:1px solid #E2E8F0;') }} font-size:0.68rem; font-weight:700;">
                                                                            {{ $pPct }}%
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada rincian proyek ditemukan.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: PAGU KONTRAK SAJA --}}
            <div class="tab-pane fade" id="tab-contract-pane" role="tabpanel" tabindex="0">
                <div class="custom-scroll-container" style="max-height: 540px; overflow-y: auto;">
                    <table class="table matrix-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Rank</th>
                                <th style="width: 32%;">Vendor / Klien</th>
                                <th style="width: 15%;" class="text-center">Jumlah Proyek</th>
                                <th style="width: 25%;">Distribusi Portofolio</th>
                                <th style="width: 20%;" class="text-end">Total Pagu Kontrak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxContract = $nilaiKontrakPerClient->max('total') ?: 1; @endphp
                            @foreach($nilaiKontrakPerClient as $idx => $vc)
                                @php
                                    $subC = $vendorContractsBreakdown[$vc->project_client] ?? collect();
                                    $pctShare = round(($vc->total / $grandContract) * 100, 1);
                                    $pctBar = round(($vc->total / $maxContract) * 100);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="matrix-rank-badge {{ $idx < 3 ? ($idx === 0 ? 'rank-gold' : ($idx === 1 ? 'rank-silver' : 'rank-bronze')) : 'rank-normal' }}">
                                            {{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="matrix-avatar me-2.5">
                                                {{ strtoupper(substr($vc->project_client, 0, min(3, strlen($vc->project_client)))) }}
                                            </div>
                                            <div class="fw-bold text-dark">{{ $vc->project_client }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-dark fw-bold border">
                                            {{ $subC->count() }} Proyek
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <small class="text-muted" style="font-size:0.7rem;">Porsi Portofolio</small>
                                            <strong class="text-primary" style="font-size:0.75rem;">{{ $pctShare }}%</strong>
                                        </div>
                                        <div class="matrix-progress-track">
                                            <div class="matrix-progress-fill" style="width: {{ $pctBar }}%; background: linear-gradient(90deg, #0284C7, #38BDF8);"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num text-dark" style="font-size: 0.9rem;">
                                            Rp {{ number_format($vc->total, 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: REALISASI BIAYA SAJA --}}
            <div class="tab-pane fade" id="tab-realisasi-pane" role="tabpanel" tabindex="0">
                <div class="custom-scroll-container" style="max-height: 540px; overflow-y: auto;">
                    <table class="table matrix-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Rank</th>
                                <th style="width: 32%;">Vendor / Klien</th>
                                <th style="width: 15%;" class="text-center">Jumlah Proyek</th>
                                <th style="width: 25%;">Skala Realisasi</th>
                                <th style="width: 20%;" class="text-end">Total Realisasi Terpakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxRealTotal = $realisasiPerClient->max('total') ?: 1; @endphp
                            @foreach($realisasiPerClient as $idx => $rc)
                                @php
                                    $subR = $vendorRealisasiBreakdown[$rc->project_client] ?? collect();
                                    $pctRealBar = round(($rc->total / $maxRealTotal) * 100);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="matrix-rank-badge {{ $idx < 3 ? ($idx === 0 ? 'rank-gold' : ($idx === 1 ? 'rank-silver' : 'rank-bronze')) : 'rank-normal' }}">
                                            {{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="matrix-avatar me-2.5" style="background: linear-gradient(135deg, #064E3B 0%, #059669 100%);">
                                                {{ strtoupper(substr($rc->project_client, 0, min(3, strlen($rc->project_client)))) }}
                                            </div>
                                            <div class="fw-bold text-dark">{{ $rc->project_client }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-success fw-bold border border-success-subtle">
                                            {{ $subR->count() }} Proyek
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <small class="text-muted" style="font-size:0.7rem;">Skala terhadap Realisasi Tertinggi</small>
                                            <strong class="text-success" style="font-size:0.75rem;">{{ $pctRealBar }}%</strong>
                                        </div>
                                        <div class="matrix-progress-track">
                                            <div class="matrix-progress-fill" style="width: {{ $pctRealBar }}%; background: linear-gradient(90deg, #10B981, #059669);"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num text-success" style="font-size: 0.9rem;">
                                            Rp {{ number_format($rc->total, 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
</div> <!-- End .screen-only-dashboard -->

{{-- ========================================================================= --}}
{{-- DEDICATED PRINT EXECUTIVE REPORT LAYOUT (VISIBLE ONLY ON PRINT/PDF)       --}}
{{-- ========================================================================= --}}
<div class="print-report-container d-none">
    <!-- PGNCOM Corporate Brand Ribbon (Indigo-Teal-Blue) -->
    <div style="height: 4px; width: 100%; background: linear-gradient(to right, #ED1B24 0%, #ED1B24 33.3%, #10B981 33.3%, #10B981 66.6%, #005A9C 66.6%, #005A9C 100%) !important; margin-bottom: 10px; border-radius: 2px;"></div>

    <!-- Corporate Letterhead -->
    <div class="print-header avoid-break">
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 2px solid #005A9C;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM Logo" style="height: 44px; width: auto; object-fit: contain;">
                <div>
                    <div style="font-size: 12.5pt; font-weight: 800; color: #0A2540; letter-spacing: -0.01em; line-height: 1.2;">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</div>
                    <div style="font-size: 8pt; font-weight: 700; color: #0284C7; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;">PGNCOM &bull; Project Management Office</div>
                    <div style="font-size: 7pt; color: #64748B;">Gedung Graha PGAS, Jl. K.H. Zainul Arifin No.20, Jakarta Barat 11140 | www.pgncom.co.id</div>
                </div>
            </div>
            <div style="text-align: right; border: 1.5px solid #EF4444; padding: 5px 10px; border-radius: 6px; background: #FEF2F2 !important; flex-shrink: 0;">
                <div style="font-size: 6.5pt; font-weight: 800; color: #991B1B; text-transform: uppercase; margin-bottom: 2px;">Klasifikasi Dokumen</div>
                <div style="font-size: 7.5pt; font-weight: 800; color: #FFFFFF !important; background: #DC2626 !important; padding: 2px 7px; border-radius: 4px; display: inline-block; letter-spacing: 0.04em;">TERBATAS / CONFIDENTIAL</div>
                <div style="font-size: 6.5pt; color: #B91C1C; font-weight: 600; margin-top: 3px;">Ref: PGN/MON-EKS/{{ date('Y/m') }}/{{ str_pad(auth()->id(), 3, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    <!-- Corporate Hero Title Banner -->
    <div style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 55%, #0284C7 100%) !important; color: #FFFFFF !important; border-radius: 6px; padding: 10px 14px; margin-top: 9px; margin-bottom: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);" class="avoid-break">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="background: rgba(255,255,255,0.2) !important; color: #FFFFFF !important; padding: 2px 8px; border-radius: 10px; font-size: 6.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 3px;">
                    Laporan Monitoring Eksekutif Terpadu
                </span>
                <h2 style="font-size: 12pt; font-weight: 800; color: #FFFFFF !important; margin: 0 0 2px; letter-spacing: -0.01em;">
                    RINGKASAN EKSEKUTIF REALISASI &amp; PROGNOSA ANGGARAN
                </h2>
                <p style="font-size: 7.4pt; color: #E0F2FE !important; margin: 0; opacity: 0.95;">
                    Evaluasi Komprehensif Nilai Kontrak, Penyerapan Realisasi Finansial, dan Prognosa Berjalan Seluruh Proyek
                </p>
            </div>
            <div style="text-align: right; border-left: 1px solid rgba(255,255,255,0.25); padding-left: 14px; flex-shrink: 0;">
                <div style="font-size: 6.8pt; color: #BAE6FD;">STATUS SISTEM</div>
                <div style="font-size: 8.5pt; font-weight: 800; color: #FFFFFF !important;">VERIFIED DATA</div>
                <div style="font-size: 6.5pt; color: #E0F2FE;">{{ date('Y') }} Fiscal Year</div>
            </div>
        </div>
    </div>

    <!-- Parameter Filter Box with Vibrant Badges -->
    <div style="background: #F0F9FF !important; border: 1.5px solid #BAE6FD !important; border-left: 4px solid #005A9C !important; border-radius: 6px; padding: 6px 8px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px; font-size: 7.2pt; margin-bottom: 10px; box-sizing: border-box;" class="avoid-break">
        <div>
            <span style="background: #0284C7 !important; color: #FFFFFF !important; font-size: 6pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">PERIODE</span>
            <strong style="color: #0F172A; display: block; margin-top: 2px; font-size: 7.4pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $filterPeriode ?: 'Semua Periode (YTD '.date('Y').')' }}</strong>
        </div>
        <div>
            <span style="background: #4F46E5 !important; color: #FFFFFF !important; font-size: 6pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">SERVICE MANAGER / PIC</span>
            <strong style="color: #0F172A; display: block; margin-top: 2px; font-size: 7.4pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $filterSM ?: 'Semua SM & PIC' }}</strong>
        </div>
        <div>
            <span style="background: #0D9488 !important; color: #FFFFFF !important; font-size: 6pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">KLIEN / PELANGGAN</span>
            <strong style="color: #0F172A; display: block; margin-top: 2px; font-size: 7.4pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $filterClient ?: 'Semua Klien' }}</strong>
        </div>
        <div>
            <span style="background: #475569 !important; color: #FFFFFF !important; font-size: 6pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">PENYUSUN &amp; WAKTU</span>
            <strong style="color: #0F172A; display: block; margin-top: 2px; font-size: 7.4pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }} &bull; {{ auth()->user()->name }}</strong>
        </div>
    </div>

    <!-- Executive KPI Scorecard Grid (5 Rich Vibrant Corporate Cards) -->
    <div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 5px; margin-bottom: 11px; box-sizing: border-box;" class="avoid-break">
        <!-- Card 1: Jumlah Kontrak -->
        <div style="border: 1.5px solid #CBD5E1 !important; border-top: 3.5px solid #1E293B !important; border-radius: 6px; padding: 5px 3px; text-align: center; background: #F8FAFC !important; overflow: hidden;">
            <div style="font-size: 6.2pt; font-weight: 800; color: #475569 !important; text-transform: uppercase; letter-spacing: 0.03em;">JUMLAH KONTRAK</div>
            <div style="font-size: 10.5pt; font-weight: 800; color: #1E293B !important; margin: 1px 0; white-space: nowrap;">{{ number_format($stats['total_kontrak']) }} <span style="font-size: 6.8pt; font-weight: 600; color: #64748B;">Proyek</span></div>
            <span style="background: #E2E8F0 !important; color: #1E293B !important; font-size: 5.8pt; font-weight: 700; padding: 1px 5px; border-radius: 8px; display: inline-block;">Portofolio Aktif</span>
        </div>

        <!-- Card 2: Total Nilai Kontrak -->
        <div style="border: 1.5px solid #BFDBFE !important; border-top: 3.5px solid #005A9C !important; border-radius: 6px; padding: 5px 3px; text-align: center; background: #EFF6FF !important; overflow: hidden;">
            <div style="font-size: 6.2pt; font-weight: 800; color: #005A9C !important; text-transform: uppercase; letter-spacing: 0.03em;">TOTAL NILAI KONTRAK</div>
            <div style="font-size: 8pt; font-weight: 800; color: #005A9C !important; margin: 1px 0; letter-spacing: -0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Rp {{ number_format($stats['total_nilai_kontrak'], 0, ',', '.') }}">Rp {{ number_format($stats['total_nilai_kontrak'], 0, ',', '.') }}</div>
            <span style="background: #DBEAFE !important; color: #1D4ED8 !important; font-size: 5.8pt; font-weight: 700; padding: 1px 5px; border-radius: 8px; display: inline-block;">Pagu Anggaran</span>
        </div>

        <!-- Card 3: Total Realisasi -->
        <div style="border: 1.5px solid #BBF7D0 !important; border-top: 3.5px solid #16A34A !important; border-radius: 6px; padding: 5px 3px; text-align: center; background: #F0FDF4 !important; overflow: hidden;">
            <div style="font-size: 6.2pt; font-weight: 800; color: #15803D !important; text-transform: uppercase; letter-spacing: 0.03em;">TOTAL REALISASI</div>
            <div style="font-size: 8pt; font-weight: 800; color: #15803D !important; margin: 1px 0; letter-spacing: -0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}">Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}</div>
            <span style="background: #DCFCE7 !important; color: #166534 !important; font-size: 5.8pt; font-weight: 700; padding: 1px 5px; border-radius: 8px; display: inline-block;">Biaya Terpakai</span>
        </div>

        <!-- Card 4: Total Prognosa -->
        <div style="border: 1.5px solid #FDE68A !important; border-top: 3.5px solid #D97706 !important; border-radius: 6px; padding: 5px 3px; text-align: center; background: #FFFBEB !important; overflow: hidden;">
            <div style="font-size: 6.2pt; font-weight: 800; color: #B45309 !important; text-transform: uppercase; letter-spacing: 0.03em;">TOTAL PROGNOSA</div>
            <div style="font-size: 8pt; font-weight: 800; color: #B45309 !important; margin: 1px 0; letter-spacing: -0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Rp {{ number_format($stats['total_prognosa'], 0, ',', '.') }}">Rp {{ number_format($stats['total_prognosa'], 0, ',', '.') }}</div>
            <span style="background: #FEF3C7 !important; color: #92400E !important; font-size: 5.8pt; font-weight: 700; padding: 1px 5px; border-radius: 8px; display: inline-block;">Estimasi Selesai</span>
        </div>

        <!-- Card 5: % Capaian Realisasi -->
        <div style="border: 1.5px solid #DDD6FE !important; border-top: 3.5px solid #7C3AED !important; border-radius: 6px; padding: 5px 3px; text-align: center; background: #FAF5FF !important; overflow: hidden;">
            <div style="font-size: 6.2pt; font-weight: 800; color: #6D28D9 !important; text-transform: uppercase; letter-spacing: 0.03em;">% CAPAIAN REALISASI</div>
            <div style="font-size: 10.5pt; font-weight: 800; color: #6D28D9 !important; margin: 1px 0; white-space: nowrap;">{{ number_format($stats['persentase_realisasi'], 2) }}%</div>
            <span style="background: #F3E8FF !important; color: #6B21A8 !important; font-size: 5.8pt; font-weight: 700; padding: 1px 5px; border-radius: 8px; display: inline-block;">Rasio Capaian</span>
        </div>
    </div>

    <!-- Table 1: Performa Service Manager (Official PGN Table Design) -->
    <div style="margin-bottom: 10px;" class="avoid-break">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
            <div style="font-size: 7.6pt; font-weight: 800; color: #0A2540; text-transform: uppercase; letter-spacing: 0.03em; display: flex; align-items: center; gap: 5px;">
                <span style="width: 4px; height: 11px; background: #005A9C !important; display: inline-block; border-radius: 2px;"></span>
                1. Rekapitulasi Kontrak &amp; Beban Kerja per Service Manager
            </div>
            <span style="font-size: 6.5pt; color: #64748B; font-weight: 600;">Total: {{ $smStats->count() }} Service Manager</span>
        </div>
        <table class="print-table" style="width: 100% !important; table-layout: fixed !important;">
            <thead>
                <tr>
                    <th style="width: 26px; text-align: center;">No</th>
                    <th>Nama Service Manager / PIC</th>
                    <th style="width: 80px; text-align: center;">Jumlah Kontrak</th>
                    <th style="width: 140px; text-align: right;">Total Nilai Kontrak (Rp)</th>
                    <th style="width: 70px; text-align: center;">Pangsa Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($smStats as $idx => $sm)
                    @php
                        $pctSm = $stats['total_nilai_kontrak'] > 0 ? ($sm->total_value / $stats['total_nilai_kontrak'] * 100) : 0;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 700;">
                            <span style="background: #EFF6FF !important; color: #005A9C !important; width: 17px; height: 17px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 6.5pt; font-weight: 800; border: 1px solid #BFDBFE !important;">
                                {{ $idx + 1 }}
                            </span>
                        </td>
                        <td style="font-weight: 700; color: #0F172A; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $sm->service_manager }}</td>
                        <td style="text-align: center;">
                            <span style="background: #F1F5F9 !important; color: #334155 !important; padding: 1.5px 5px; border-radius: 5px; font-weight: 700; font-size: 6.5pt; border: 1px solid #E2E8F0 !important;">
                                {{ $sm->contract_count }} Proyek
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #005A9C; white-space: nowrap;">
                            Rp {{ number_format($sm->total_value, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #EFF6FF !important; color: #1D4ED8 !important; padding: 1.5px 5px; border-radius: 8px; font-weight: 700; font-size: 6.5pt; border: 1px solid #BFDBFE !important;">
                                {{ number_format($pctSm, 1) }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748B;">Tidak ada data Service Manager</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #E0F2FE !important; font-weight: 800; color: #0A2540;">
                    <td colspan="2" style="text-align: right; padding-right: 8px;">TOTAL KESELURUHAN:</td>
                    <td style="text-align: center;">
                        <span style="background: #005A9C !important; color: #FFFFFF !important; padding: 2px 7px; border-radius: 5px; font-size: 6.8pt;">
                            {{ $smStats->sum('contract_count') }} Proyek
                        </span>
                    </td>
                    <td style="text-align: right; color: #0A2540 !important; font-size: 7.8pt; white-space: nowrap;">
                        Rp {{ number_format($smStats->sum('total_value'), 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        <span style="background: #005A9C !important; color: #FFFFFF !important; padding: 2px 7px; border-radius: 8px; font-size: 6.5pt;">
                            100%
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Table 2 & 3: Top 10 Vendor (Nilai Kontrak vs Realisasi) -->
    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 8px; margin-bottom: 10px; box-sizing: border-box;" class="avoid-break">
        <!-- Top 10 Nilai Kontrak -->
        <div>
            <div style="font-size: 7.5pt; font-weight: 800; color: #0284C7; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.03em; display: flex; align-items: center; gap: 5px;">
                <span style="width: 4px; height: 10px; background: #0284C7 !important; display: inline-block; border-radius: 2px;"></span>
                2. Top 10 Nilai Kontrak per Klien
            </div>
            <table class="print-table print-table-sky" style="width: 100% !important; table-layout: fixed !important;">
                <thead>
                    <tr>
                        <th style="width: 24px; text-align: center;">Rank</th>
                        <th>Klien / Vendor</th>
                        <th style="width: 100px; text-align: right;">Nilai Kontrak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nilaiKontrakPerClient->take(10) as $i => $vc)
                        @php
                            $rankBg = $i === 0 ? '#FEF3C7' : ($i === 1 ? '#F1F5F9' : ($i === 2 ? '#FFEDD5' : '#F8FAFC'));
                            $rankColor = $i === 0 ? '#92400E' : ($i === 1 ? '#334155' : ($i === 2 ? '#9A3412' : '#64748B'));
                            $rankBorder = $i === 0 ? '#F59E0B' : ($i === 1 ? '#94A3B8' : ($i === 2 ? '#F97316' : '#CBD5E1'));
                        @endphp
                        <tr>
                            <td style="text-align: center;">
                                <span style="background: {{ $rankBg }} !important; color: {{ $rankColor }} !important; border: 1px solid {{ $rankBorder }} !important; width: 15px; height: 15px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 6.2pt; font-weight: 800;">
                                    {{ $i + 1 }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #0F172A; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $vc->project_client }}">{{ $vc->project_client }}</td>
                            <td style="text-align: right; font-weight: 700; color: #0284C7; white-space: nowrap; font-size: 7pt;">Rp {{ number_format($vc->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Top 10 Realisasi -->
        <div>
            <div style="font-size: 7.5pt; font-weight: 800; color: #059669; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.03em; display: flex; align-items: center; gap: 5px;">
                <span style="width: 4px; height: 10px; background: #059669 !important; display: inline-block; border-radius: 2px;"></span>
                3. Top 10 Realisasi per Klien
            </div>
            <table class="print-table print-table-emerald" style="width: 100% !important; table-layout: fixed !important;">
                <thead>
                    <tr>
                        <th style="width: 24px; text-align: center;">Rank</th>
                        <th>Klien / Vendor</th>
                        <th style="width: 100px; text-align: right;">Total Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($realisasiPerClient->take(10) as $j => $rc)
                        @php
                            $rankBgR = $j === 0 ? '#DCFCE7' : ($j === 1 ? '#F1F5F9' : ($j === 2 ? '#FEF3C7' : '#F8FAFC'));
                            $rankColorR = $j === 0 ? '#166534' : ($j === 1 ? '#334155' : ($j === 2 ? '#92400E' : '#64748B'));
                            $rankBorderR = $j === 0 ? '#86EFAC' : ($j === 1 ? '#94A3B8' : ($j === 2 ? '#FDE68A' : '#CBD5E1'));
                        @endphp
                        <tr>
                            <td style="text-align: center;">
                                <span style="background: {{ $rankBgR }} !important; color: {{ $rankColorR }} !important; border: 1px solid {{ $rankBorderR }} !important; width: 15px; height: 15px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 6.2pt; font-weight: 800;">
                                    {{ $j + 1 }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #0F172A; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $rc->project_client }}">{{ $rc->project_client }}</td>
                            <td style="text-align: right; font-weight: 700; color: #059669; white-space: nowrap; font-size: 7pt;">Rp {{ number_format($rc->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lembar Pengesahan / Signatures (Prestigious Executive Block) -->
    <div style="margin-top: 10px;" class="avoid-break">
        <div style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%) !important; color: #FFFFFF !important; font-size: 7pt; font-weight: 800; text-transform: uppercase; padding: 4.5px 10px; text-align: center; letter-spacing: 0.08em; border-radius: 6px 6px 0 0;">
            LEMBAR PENGESAHAN LAPORAN MONITORING EKSEKUTIF
        </div>
        <div style="border: 1.5px solid #CBD5E1; border-top: none; border-radius: 0 0 6px 6px; padding: 10px 12px 8px; background: #F8FAFC !important; box-sizing: border-box;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; text-align: center;">
                <div>
                    <div style="font-size: 6.8pt; color: #64748B;">Disiapkan Oleh,</div>
                    <div style="font-size: 7pt; font-weight: 700; color: #0A2540; margin-top: 1px;">Pembuat Laporan</div>
                    <div style="height: 30px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1px dashed #CBD5E1; border-radius: 4px; padding: 1.5px 5px; font-size: 5.8pt; color: #94A3B8; text-transform: uppercase;">Paraf Pembuat</span>
                    </div>
                    <div style="font-size: 7.8pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 10px;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size: 6.5pt; color: #0284C7; font-weight: 600; margin-top: 2px;">{{ strtoupper(auth()->user()->role) }}</div>
                </div>

                <div>
                    <div style="font-size: 6.8pt; color: #64748B;">Diperiksa Oleh,</div>
                    <div style="font-size: 7pt; font-weight: 700; color: #0A2540; margin-top: 1px;">Service Manager / Project Control</div>
                    <div style="height: 30px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1px dashed #CBD5E1; border-radius: 4px; padding: 1.5px 5px; font-size: 5.8pt; color: #94A3B8; text-transform: uppercase;">Paraf Reviewer</span>
                    </div>
                    <div style="font-size: 7.8pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 10px;">
                        {{ $filterSM && $filterSM !== 'Semua SM & PIC' ? $filterSM : '...........................................' }}
                    </div>
                    <div style="font-size: 6.5pt; color: #0284C7; font-weight: 600; margin-top: 2px;">OSM Service Manager</div>
                </div>

                <div>
                    <div style="font-size: 6.8pt; color: #64748B;">Disetujui Oleh,</div>
                    <div style="font-size: 7pt; font-weight: 700; color: #0A2540; margin-top: 1px;">VP / Group Head Project Management</div>
                    <div style="height: 30px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1.5px dashed #005A9C; border-radius: 4px; padding: 1.5px 5px; font-size: 5.8pt; color: #005A9C; font-weight: 700; text-transform: uppercase;">[ STEMPEL SAH RESMI ]</span>
                    </div>
                    <div style="font-size: 7.8pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 10px;">
                        ...........................................
                    </div>
                    <div style="font-size: 6.5pt; color: #0284C7; font-weight: 600; margin-top: 2px;">PT PGAS Telekomunikasi Nusantara (PGNCOM)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Corporate Print Footer with Colored Ribbon -->
    <div style="margin-top: 10px; padding: 6px 10px; background: #F1F5F9 !important; border: 1px solid #CBD5E1 !important; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; font-size: 6.8pt; color: #475569;" class="avoid-break">
        <div>
            <strong style="color: #0A2540;">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</strong> &bull; Divisi Service Management &amp; Operation (SMO) &bull; Sistem Monitoring Realisasi &amp; Anggaran
        </div>
        <div style="font-weight: 600;">
            Dicetak: {{ date('d/m/Y H:i:s') }} WIB &bull; Dokumen Sah Internal
        </div>
    </div>
    <div style="height: 3px; width: 100%; background: linear-gradient(to right, #ED1B24 0%, #ED1B24 33.3%, #10B981 33.3%, #10B981 66.6%, #005A9C 66.6%, #005A9C 100%) !important; margin-top: 3px; border-radius: 2px;"></div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Plus Jakarta Sans','Inter',sans-serif";
Chart.defaults.color = "#64748B";
Chart.defaults.plugins.tooltip.backgroundColor = "rgba(15,23,42,0.92)";
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { size: 11, weight: '700' };
Chart.defaults.plugins.tooltip.bodyFont  = { size: 10 };

function fmtRp(v) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
}

// --- 1. TREN BULANAN REALISASI VS PROGNOSA (COMBO BAR + LINE) ---
const allMonths = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

const realisasiMonthRaw = @json($realisasiPerBulan);
const prognosaMonthRaw  = @json($prognosaPerBulan);

const realisasiMap = {};
realisasiMonthRaw.forEach(item => {
    if (item.periode) realisasiMap[item.periode.toUpperCase()] = parseFloat(item.total) || 0;
});
const realisasiMonthlyData = allMonths.map(m => realisasiMap[m] || 0);

const prognosaMap = {};
prognosaMonthRaw.forEach(item => {
    if (item.periode) prognosaMap[item.periode.toUpperCase()] = parseFloat(item.total) || 0;
});
const prognosaMonthlyData = allMonths.map(m => prognosaMap[m] || 0);

new Chart(document.getElementById('chartMonthlyTrend'), {
    data: {
        labels: monthLabels,
        datasets: [
            {
                type: 'bar',
                label: 'Realisasi Aktual',
                data: realisasiMonthlyData,
                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                hoverBackgroundColor: '#0284c7',
                borderRadius: 6,
                barPercentage: 0.58,
                order: 2
            },
            {
                type: 'line',
                label: 'Prognosa (Estimasi)',
                data: prognosaMonthlyData,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                borderWidth: 2.5,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4.5,
                pointHoverRadius: 6,
                tension: 0.35,
                fill: true,
                order: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { boxWidth: 10, boxHeight: 10, padding: 14, font: { size: 10.5, weight: '700' } }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.dataset.label + ': ' + fmtRp(ctx.raw)
                }
            }
        },
        scales: {
            y: {
                grid: { color: '#F1F5F9' },
                ticks: {
                    callback: v => {
                        if (v >= 1e9) return (v / 1e9).toFixed(1) + ' M';
                        if (v >= 1e6) return (v / 1e6).toFixed(0) + ' Jt';
                        return v;
                    },
                    font: { size: 9, weight: '600' }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 9, weight: '700' } }
            }
        }
    }
});

// --- 2. DOUGHNUT GAUGE: RASIO PENYERAPAN ANGGARAN ---
const totalK = {{ $stats['total_nilai_kontrak'] }};
const totalR = {{ $stats['total_realisasi'] }};
const sisa   = Math.max(0, totalK - totalR);

new Chart(document.getElementById('chartComparisonGauge'), {
    type: 'doughnut',
    data: {
        labels: ['Realisasi Terpakai', 'Sisa Pagu'],
        datasets: [{
            data: [totalR, sisa],
            backgroundColor: ['#10b981', '#e2e8f0'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '76%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + fmtRp(ctx.raw) } }
        }
    }
});

// --- 3. TOP KLIEN: KONTRAK VS REALISASI (HORIZONTAL BAR) ---
const clientContracts = @json($nilaiKontrakPerClient);
const clientRealisasi = @json($realisasiPerClient);

const realisasiByClient = {};
clientRealisasi.forEach(item => {
    if (item.project_client) {
        realisasiByClient[item.project_client] = parseFloat(item.total) || 0;
    }
});

const topClients = clientContracts.slice(0, 6);
const clientLabels = topClients.map(c => c.project_client || 'Tanpa Klien');
const contractValues = topClients.map(c => parseFloat(c.total) || 0);
const realisasiValues = topClients.map(c => realisasiByClient[c.project_client] || 0);

new Chart(document.getElementById('chartTopClients'), {
    type: 'bar',
    data: {
        labels: clientLabels,
        datasets: [
            {
                label: 'Nilai Kontrak',
                data: contractValues,
                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                hoverBackgroundColor: '#0284c7',
                borderRadius: 4,
                barPercentage: 0.7,
                categoryPercentage: 0.75
            },
            {
                label: 'Realisasi Finansial',
                data: realisasiValues,
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                hoverBackgroundColor: '#10b981',
                borderRadius: 4,
                barPercentage: 0.7,
                categoryPercentage: 0.75
            }
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { boxWidth: 10, boxHeight: 10, padding: 14, font: { size: 10.5, weight: '700' } }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.dataset.label + ': ' + fmtRp(ctx.raw)
                }
            }
        },
        scales: {
            x: {
                grid: { color: '#F1F5F9' },
                ticks: {
                    callback: v => {
                        if (v >= 1e9) return (v / 1e9).toFixed(1) + ' M';
                        if (v >= 1e6) return (v / 1e6).toFixed(0) + ' Jt';
                        return v;
                    },
                    font: { size: 9, weight: '600' }
                }
            },
            y: {
                grid: { display: false },
                ticks: { font: { size: 9.5, weight: '700' } }
            }
        }
    }
});

// --- 4. BEBAN KERJA & NILAI KONTRAK SERVICE MANAGER ---
const smData = @json($smStats);
new Chart(document.getElementById('chartSMStats'), {
    type: 'bar',
    data: {
        labels: smData.map(d => d.service_manager),
        datasets: [
            {
                label: 'Jumlah Kontrak',
                data: smData.map(d => d.contract_count),
                backgroundColor: 'rgba(2,132,199,0.85)',
                hoverBackgroundColor: '#0284c7',
                borderRadius: 6,
                barPercentage: 0.55,
                yAxisID: 'yCount',
                order: 2
            },
            {
                label: 'Total Nilai Kontrak',
                data: smData.map(d => parseFloat(d.total_value)),
                type: 'line',
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4.5,
                pointHoverRadius: 6,
                tension: 0.35,
                fill: true,
                yAxisID: 'yValue',
                order: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top', align: 'end',
                labels: { boxWidth: 10, boxHeight: 10, padding: 14, font: { size: 10.5, weight: '700' } }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.datasetIndex === 0 ? ' ' + ctx.raw + ' kontrak' : ' ' + fmtRp(ctx.raw)
                }
            }
        },
        scales: {
            yCount: {
                type: 'linear', position: 'left',
                grid: { color: '#F1F5F9' },
                ticks: { font: { size: 9, weight: '600' }, stepSize: 1 },
                title: { display: true, text: 'Kontrak', font: { size: 9, weight: '700' }, color: '#64748B' }
            },
            yValue: {
                type: 'linear', position: 'right',
                grid: { display: false },
                ticks: {
                    callback: v => {
                        if (v >= 1e9) return (v / 1e9).toFixed(1) + ' M';
                        if (v >= 1e6) return (v / 1e6).toFixed(0) + ' Jt';
                        return v;
                    },
                    font: { size: 9, weight: '600' }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 9, weight: '700' }, maxRotation: 40, minRotation: 30 }
            }
        }
    }
});

document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => {
    const targetId = el.getAttribute('data-bs-target');
    const target = document.querySelector(targetId);
    if (!target) return;
    target.addEventListener('show.bs.collapse', () => {
        const ic = el.querySelector('.collapse-icon');
        if (ic) ic.style.transform = 'rotate(90deg)';
    });
    target.addEventListener('hide.bs.collapse', () => {
        const ic = el.querySelector('.collapse-icon');
        if (ic) ic.style.transform = 'rotate(0deg)';
    });
});
</script>
@endpush
