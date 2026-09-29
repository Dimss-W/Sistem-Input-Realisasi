@extends('layouts.main')

@section('title', 'Dashboard Quality Control & Monitoring Portofolio')
@section('page-title', 'Dashboard Quality Control & Monitoring')

@section('breadcrumb')
    <li class="breadcrumb-item active"><i class="bi bi-shield-check me-1"></i>Dashboard QC &amp; Monitoring</li>
@endsection

@push('styles')
<style>
    /* =========================================================================
       1. EXACT 5-KPI REFERENCE CARDS (PASTEL TINTED, ZERO TRUNCATION)
       ========================================================================= */
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

    /* 1. JUMLAH KONTRAK */
    .kpi-card-kontrak { background: #F8FAFC; border-color: #E2E8F0; border-top-color: #1E293B; }
    .kpi-card-kontrak .kpi-exact-title { color: #334155; }
    .kpi-card-kontrak .kpi-exact-val   { color: #0F172A; }
    .kpi-card-kontrak .kpi-unit        { font-weight: 600; font-size: 0.85em; color: #64748B; margin-left: 3px; }
    .kpi-card-kontrak .kpi-exact-pill  { background: #F1F5F9; color: #334155; }

    /* 2. TOTAL NILAI KONTRAK */
    .kpi-card-pagu { background: #F0F7FF; border-color: #BFDBFE; border-top-color: #0284C7; }
    .kpi-card-pagu .kpi-exact-title { color: #0369A1; }
    .kpi-card-pagu .kpi-exact-val   { color: #0284C7; }
    .kpi-card-pagu .kpi-exact-pill  { background: #DBEAFE; color: #1D4ED8; }

    /* 3. TOTAL REALISASI */
    .kpi-card-real { background: #F0FDF4; border-color: #BBF7D0; border-top-color: #16A34A; }
    .kpi-card-real .kpi-exact-title { color: #15803D; }
    .kpi-card-real .kpi-exact-val   { color: #15803D; }
    .kpi-card-real .kpi-exact-pill  { background: #DCFCE7; color: #15803D; }

    /* 4. TOTAL PROGNOSA */
    .kpi-card-prognosa { background: #FFFBEB; border-color: #FDE68A; border-top-color: #D97706; }
    .kpi-card-prognosa .kpi-exact-title { color: #B45309; }
    .kpi-card-prognosa .kpi-exact-val   { color: #D97706; }
    .kpi-card-prognosa .kpi-exact-pill  { background: #FEF3C7; color: #B45309; }

    /* 5. % CAPAIAN REALISASI */
    .kpi-card-ratio { background: #FAF5FF; border-color: #E9D5FF; border-top-color: #7C3AED; }
    .kpi-card-ratio .kpi-exact-title { color: #6D28D9; }
    .kpi-card-ratio .kpi-exact-val   { color: #7C3AED; }
    .kpi-card-ratio .kpi-exact-pill  { background: #EDE9FE; color: #6D28D9; }

    @media (max-width: 1099.98px) { .kpi-exact-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px) { .kpi-exact-grid { grid-template-columns: 1fr; } }

    /* Filter Card */
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

    /* Section Cards */
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
    .custom-scroll-container::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll-container::-webkit-scrollbar-track { background: #F8FAFC; border-radius: 4px; }
    .custom-scroll-container::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    .custom-scroll-container::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* Matrix Table Styling */
    .matrix-table { border-collapse: separate; border-spacing: 0; width: 100%; }
    .matrix-table thead th {
        background: #F8FAFC; color: #475569; font-size: 0.72rem; font-weight: 800;
        letter-spacing: 0.05em; text-transform: uppercase; padding: 12px 16px;
        border-bottom: 2px solid #E2E8F0; white-space: nowrap; position: sticky; top: 0; z-index: 2;
    }
    .matrix-row-parent { cursor: pointer; transition: all 0.15s ease; border-bottom: 1px solid #F1F5F9; }
    .matrix-row-parent:hover { background: #F8FAFC; }
    .matrix-row-parent td { padding: 13px 16px; vertical-align: middle; font-size: 0.82rem; }
    .matrix-num { font-family: 'Plus Jakarta Sans', sans-serif; font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
    
    .matrix-rank-badge {
        width: 24px; height: 24px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 800; margin-right: 10px; flex-shrink: 0;
    }
    .rank-gold   { background: #FEF3C7; color: #92400E; border: 1.5px solid #FDE68A; }
    .rank-silver { background: #F1F5F9; color: #334155; border: 1.5px solid #CBD5E1; }
    .rank-bronze { background: #FFEDD5; color: #9A3412; border: 1.5px solid #FED7AA; }
    .rank-normal { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

    .matrix-avatar {
        width: 32px; height: 32px; border-radius: 8px;
        background: #0284C7; color: #FFFFFF; font-weight: 800; font-size: 0.72rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .matrix-progress-track { height: 6px; background: #F1F5F9; border-radius: 10px; overflow: hidden; }
    .matrix-progress-fill { height: 100%; border-radius: 10px; transition: width 0.4s ease; }
    
    .collapse-toggle-btn {
        background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px;
        padding: 5px 12px; font-size: 0.72rem; font-weight: 700; color: #475569;
        transition: all 0.2s; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    }
    .collapse-toggle-btn:hover { background: #F0F9FF; border-color: #BAE6FD; color: #0369A1; }
    .matrix-child-container { background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 14px 20px; }
    .project-subtable { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; }
    .project-subtable thead th { background: #F1F5F9; color: #475569; font-size: 0.68rem; font-weight: 800; padding: 8px 12px; border-bottom: 1px solid #E2E8F0; }
    .project-subtable tbody td { padding: 9px 12px; font-size: 0.78rem; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }

    .donut-wrap { position: relative; width: 150px; height: 150px; }
    .donut-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none; }
    .donut-center .pct { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1; }
    .donut-center .lbl { font-size: 0.62rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #64748B; margin-top: 3px; }
    .legend-pill { display: inline-flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 700; color: #334155; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    /* Nav View Switcher Pills */
    .view-mode-pill {
        cursor: pointer;
        padding: 7px 15px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #475569;
        background: transparent;
        white-space: nowrap;
    }
    .view-mode-pill:hover {
        background: #F1F5F9;
        color: #0F172A;
    }
    .view-mode-pill.active {
        background: #0284C7;
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .view-mode-pill.active .badge-mode {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
    }

    /* Mini Finance Strip on QC View */
    .mini-finance-strip {
        background: linear-gradient(90deg, #F8FAFC 0%, #F1F5F9 100%);
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 10px 16px;
    }
    .mini-stat-item {
        display: flex;
        flex-direction: column;
    }
    .mini-stat-title {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
    }
    .mini-stat-val {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0F172A;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')

@php
    $initialTab = request('tab') ?? (($filterProject || $filterSM || $filterClient || $filterPeriode) ? 'financial' : 'qc');
@endphp

{{-- =========================================================================
     ON-SCREEN INTERACTIVE DASHBOARD CONTAINER (HIDDEN ON PRINT)
     ========================================================================= --}}
<div class="screen-only-dashboard">

    {{-- 1. Header Banner Terintegrasi (QC + Finansial) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3" style="background: linear-gradient(135deg, #0A192F 0%, #112240 55%, #1E3A8A 100%);">
        <div class="card-body p-3.5 text-white">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" 
                         style="background: rgba(255, 255, 255, 0.12); font-size: 0.74rem; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.2); color: #E0F2FE;">
                        <span class="badge bg-info rounded-circle p-1" style="width:6px; height:6px;"></span>
                        Divisi SMO &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)
                    </div>
                    <h4 class="fw-bold text-white mb-1" style="font-size: 1.35rem; letter-spacing: -0.02em;">
                        Pusat Kendali Pengawasan Mutu &amp; Monitoring Portofolio
                    </h4>
                    <p class="text-white text-opacity-80 mb-0 small" style="max-width: 620px; line-height: 1.5; font-size: 0.8rem;">
                        Verifikasi kepatuhan teknis BASTO, proteksi gatekeeper approval, dan monitoring penyerapan pagu kontrak proyek.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" onclick="window.print()" class="btn btn-light btn-sm fw-bold rounded-pill px-3 py-1.5 shadow-sm text-dark" title="Cetak Ringkasan Eksekutif Resmi (PDF)">
                        <i class="bi bi-printer-fill me-1 text-primary"></i> Cetak Laporan Eksekutif
                    </button>
                    <a href="{{ route('basto.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5">
                        <i class="bi bi-folder2-open me-1"></i> Kelola BASTO Penuh
                    </a>
                    <a href="{{ route('realisasi.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5">
                        <i class="bi bi-table me-1"></i> Tabel Realisasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Modern View Switcher Pill Bar (Pilihan Tampilan: QC, Finansial Sebelumnya, atau Gabungan) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 p-1.5 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex flex-wrap gap-1" id="viewModeContainer">
                {{-- Mode 1: QC Mutu --}}
                <button type="button" class="view-mode-pill {{ $initialTab === 'qc' ? 'active' : '' }}" onclick="switchDashboardView('qc', this)">
                    <i class="bi bi-shield-check"></i>
                    <span>Pengawasan Mutu &amp; BASTO</span>
                    @if($qcPendingCount > 0)
                        <span class="badge bg-danger text-white rounded-pill px-2 py-0.5 badge-mode" style="font-size: 0.7rem;">{{ $qcPendingCount }} Menunggu</span>
                    @endif
                </button>

                {{-- Mode 2: Finansial & Proyek (Tampilan Sebelumnya) --}}
                <button type="button" class="view-mode-pill {{ $initialTab === 'financial' ? 'active' : '' }}" onclick="switchDashboardView('financial', this)">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Monitoring Realisasi &amp; Finansial (Tampilan Sebelumnya)</span>
                    <span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 badge-mode" style="font-size: 0.7rem;">{{ number_format($stats['total_kontrak']) }} Proyek</span>
                </button>

                {{-- Mode 3: Gabungan Lengkap --}}
                <button type="button" class="view-mode-pill {{ $initialTab === 'all' ? 'active' : '' }}" onclick="switchDashboardView('all', this)">
                    <i class="bi bi-layers-fill"></i>
                    <span>Semua Tampilan (Gabungan Lengkap)</span>
                </button>
            </div>

            {{-- Quick Stat Pill Indicator --}}
            <div class="d-none d-xl-flex align-items-center gap-2 pe-2 text-muted" style="font-size: 0.75rem;">
                <span>Rasio Capaian: <strong class="text-dark">{{ number_format($stats['persentase_realisasi'], 1) }}%</strong></span>
                <span>&bull;</span>
                <span>Pass Rate QC: <strong class="text-success">{{ $passRate }}%</strong></span>
            </div>
        </div>
    </div>

    {{-- SECTION A: WORKSPACE PENGAWASAN MUTU (QC OPERATIONS) --}}
    <div id="sectionQcView" class="{{ in_array($initialTab, ['qc', 'all']) ? '' : 'd-none' }}">

        {{-- Mini Finance Strip --}}
        <div class="mini-finance-strip mb-3 shadow-xs">
            <div class="row g-2 align-items-center text-center text-md-start">
                <div class="col-6 col-md-2 border-end-md">
                    <div class="mini-stat-item">
                        <span class="mini-stat-title"><i class="bi bi-briefcase me-1 text-secondary"></i>Jumlah Kontrak</span>
                        <span class="mini-stat-val text-dark">{{ number_format($stats['total_kontrak']) }} <span class="small text-muted fw-normal" style="font-size:0.75rem;">Proyek</span></span>
                    </div>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <div class="mini-stat-item">
                        <span class="mini-stat-title"><i class="bi bi-wallet2 me-1 text-primary"></i>Total Nilai Kontrak</span>
                        <span class="mini-stat-val text-primary" style="font-size: 0.9rem;">Rp {{ number_format($stats['total_nilai_kontrak'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <div class="mini-stat-item">
                        <span class="mini-stat-title"><i class="bi bi-cash-coin me-1 text-success"></i>Total Realisasi</span>
                        <span class="mini-stat-val text-success" style="font-size: 0.9rem;">Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-2 border-end-md">
                    <div class="mini-stat-item">
                        <span class="mini-stat-title"><i class="bi bi-hourglass-top me-1 text-warning"></i>Total Prognosa</span>
                        <span class="mini-stat-val text-warning" style="font-size: 0.9rem;">Rp {{ number_format($stats['total_prognosa'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-12 col-md-2 text-center">
                    <div class="mini-stat-item">
                        <span class="mini-stat-title"><i class="bi bi-pie-chart-fill me-1" style="color:#7C3AED;"></i>Serapan Biaya</span>
                        <span class="mini-stat-val fw-bold" style="color:#7C3AED;">{{ number_format($stats['persentase_realisasi'], 2) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 KPI Cards Khusus Pengawasan Mutu (QC) --}}
        <div class="row g-2.5 mb-3">
            {{-- KPI 1: Menunggu Inspeksi --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #FFFBEB; border-left: 4px solid #D97706 !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold" style="font-size: 0.7rem; color: #B45309; letter-spacing: 0.05em;">Menunggu Inspeksi</div>
                            <div class="fs-3 fw-bold my-0.5" style="color: #92400E; line-height: 1.2;">{{ $qcPendingCount }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Dokumen BASTO perlu verifikasi</div>
                        </div>
                        <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="background: rgba(217, 119, 6, 0.15); width: 44px; height: 44px;">
                            <i class="bi bi-hourglass-split fs-4" style="color: #D97706;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Perlu Revisi Mutu --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #FEF2F2; border-left: 4px solid #DC2626 !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold" style="font-size: 0.7rem; color: #991B1B; letter-spacing: 0.05em;">Perlu Revisi Mutu</div>
                            <div class="fs-3 fw-bold my-0.5" style="color: #7F1D1D; line-height: 1.2;">{{ $qcRevisionCount }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Approval SM dikunci gatekeeper</div>
                        </div>
                        <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.15); width: 44px; height: 44px;">
                            <i class="bi bi-shield-x fs-4" style="color: #DC2626;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Lolos Verifikasi (Passed) --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #F0FDF4; border-left: 4px solid #16A34A !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold" style="font-size: 0.7rem; color: #15803D; letter-spacing: 0.05em;">Lolos Sertifikasi QC</div>
                            <div class="fs-3 fw-bold my-0.5" style="color: #14532D; line-height: 1.2;">{{ $qcVerifiedCount }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Stempel kode resmi terbit</div>
                        </div>
                        <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.15); width: 44px; height: 44px;">
                            <i class="bi bi-patch-check-fill fs-4" style="color: #16A34A;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 4: QC Pass Rate --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #F0F9FF; border-left: 4px solid #0284C7 !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold" style="font-size: 0.7rem; color: #0369A1; letter-spacing: 0.05em;">QC Pass Rate</div>
                            <div class="fs-3 fw-bold my-0.5" style="color: #0C4A6E; line-height: 1.2;">{{ $passRate }}%</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Rasio kelolosan dokumen</div>
                        </div>
                        <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.15); width: 44px; height: 44px;">
                            <i class="bi bi-award-fill fs-4" style="color: #0284C7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Area Kerja QC: Antrean Dokumen Prioritas (7 Kolom) + Kepatuhan Kriteria & Log (5 Kolom) --}}
        <div class="row g-3 mb-4">
            {{-- Kolom Kiri: Antrean BASTO Prioritas --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-primary fs-5"></i>
                            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.03em;">Antrean Dokumen BASTO Prioritas</span>
                        </div>
                        
                        {{-- Quick Filter Pills --}}
                        <div class="d-flex gap-1" id="qcFilterPills">
                            <button type="button" class="btn btn-xs btn-primary fw-bold rounded-pill px-2.5 py-1" onclick="filterQueueTable('all', this)" style="font-size: 0.72rem;">
                                Semua ({{ $qcQueue->count() }})
                            </button>
                            <button type="button" class="btn btn-xs btn-light text-dark rounded-pill px-2.5 py-1" onclick="filterQueueTable('pending', this)" style="font-size: 0.72rem;">
                                Menunggu ({{ $qcPendingCount }})
                            </button>
                            <button type="button" class="btn btn-xs btn-light text-dark rounded-pill px-2.5 py-1" onclick="filterQueueTable('revision', this)" style="font-size: 0.72rem;">
                                Revisi ({{ $qcRevisionCount }})
                            </button>
                            <button type="button" class="btn btn-xs btn-light text-dark rounded-pill px-2.5 py-1" onclick="filterQueueTable('verified', this)" style="font-size: 0.72rem;">
                                Lolos ({{ $qcVerifiedCount }})
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="qcQueueTable" style="font-size: 0.8rem;">
                            <thead class="table-light text-secondary sticky-top border-bottom" style="font-size: 0.72rem;">
                                <tr>
                                    <th class="ps-3 py-2">Dokumen BASTO</th>
                                    <th>Pengaju (DMO)</th>
                                    <th class="text-center">Status QC</th>
                                    <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($qcQueue as $basto)
                                @php
                                    $rowStatus = 'pending';
                                    if ($basto->qc_status === 'verified') $rowStatus = 'verified';
                                    elseif ($basto->qc_status === 'revision_needed') $rowStatus = 'revision';
                                @endphp
                                <tr class="queue-row queue-{{ $rowStatus }}">
                                    <td class="ps-3 py-2.5">
                                        <div class="fw-bold text-dark">{{ $basto->basto_number }}</div>
                                        <div class="text-muted text-truncate" style="max-width: 220px; font-size: 0.74rem;" title="{{ $basto->project_name }}">
                                            {{ $basto->project_name }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">ID: {{ $basto->project_id }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $basto->dmo ? $basto->dmo->name : '—' }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $basto->submitted_at ? $basto->submitted_at->format('d/m/Y') : ($basto->created_at ? $basto->created_at->format('d/m/Y') : '—') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($basto->qc_status === 'verified')
                                            <span class="badge px-2 py-1 rounded-pill fw-bold" style="background-color: #dcfce7 !important; color: #166534 !important; border: 1px solid #86efac !important; font-size: 0.68rem;" title="{{ $basto->qc_verification_code }}">
                                                <i class="bi bi-patch-check-fill me-1"></i> VERIFIED
                                            </span>
                                        @elseif($basto->qc_status === 'revision_needed')
                                            <span class="badge px-2 py-1 rounded-pill fw-bold" style="background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fca5a5 !important; font-size: 0.68rem;">
                                                <i class="bi bi-shield-x me-1"></i> REVISI
                                            </span>
                                        @else
                                            <span class="badge px-2 py-1 rounded-pill fw-medium" style="background-color: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1 !important; font-size: 0.68rem;">
                                                <i class="bi bi-hourglass-split me-1"></i> PENDING
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if($basto->attachment_file)
                                                <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-outline-danger btn-xs rounded-3 px-2 py-1" title="Buka PDF">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </a>
                                            @endif
                                            
                                            <button type="button" class="btn btn-primary btn-xs rounded-3 px-2.5 py-1 fw-bold shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#qcModal{{ $basto->id }}" title="Inspeksi / Checklist Mutu">
                                                <i class="bi bi-shield-check"></i>
                                                <span>Inspeksi</span>
                                            </button>

                                            <a href="{{ route('basto.show', $basto->id) }}" class="btn btn-light btn-xs rounded-3 px-2 py-1 text-secondary" title="Detail BASTO">
                                                <i class="bi bi-arrow-right-short fs-6"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-shield-check fs-2 text-secondary opacity-50 d-block mb-1"></i>
                                        <span class="small">Belum ada dokumen BASTO dalam antrean.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Kepatuhan 4 Standar Kriteria & Temuan Log Terkini --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-3 h-100">
                    
                    {{-- WIDGET 1: Kepatuhan 4 Standar Kriteria Checklist --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-2.5 px-3">
                            <span class="fw-bold text-dark small text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.03em;">
                                <i class="bi bi-pie-chart text-info"></i> Kepatuhan 4 Kriteria Mutu Teknis
                            </span>
                        </div>
                        <div class="card-body p-3">
                            @php
                                $baseCount = $totalWithChecklist > 0 ? $totalWithChecklist : 1;
                                $pctAdmin = $totalWithChecklist > 0 ? round(($criteriaCompliance['admin_doc'] / $baseCount) * 100) : 0;
                                $pctSpk   = $totalWithChecklist > 0 ? round(($criteriaCompliance['spk_compliance'] / $baseCount) * 100) : 0;
                                $pctBaut  = $totalWithChecklist > 0 ? round(($criteriaCompliance['baut_teknis'] / $baseCount) * 100) : 0;
                                $pctPhys  = $totalWithChecklist > 0 ? round(($criteriaCompliance['physical_evidence'] / $baseCount) * 100) : 0;
                            @endphp

                            @if($totalWithChecklist === 0)
                                <div class="alert alert-light border py-2 px-2.5 rounded-3 mb-2.5 d-flex align-items-center gap-2 text-muted" style="font-size: 0.72rem; background: #F8FAFC;">
                                    <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
                                    <span>Belum ada data dokumen BASTO yang diinspeksi. Persentase akan otomatis terhitung saat verifikasi mutu dilakukan.</span>
                                </div>
                            @endif

                            {{-- Kriteria 1 --}}
                            <div class="mb-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-dark"><i class="bi bi-file-earmark-check me-1 text-primary"></i>1. Administrasi &amp; Ttd</span>
                                    <span class="fw-bold {{ $totalWithChecklist > 0 ? ($pctAdmin >= 80 ? 'text-success' : 'text-danger') : 'text-muted' }}">{{ $pctAdmin }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background-color: #E2E8F0;">
                                    <div class="progress-bar bg-success" style="width: {{ $pctAdmin }}%;"></div>
                                </div>
                            </div>

                            {{-- Kriteria 2 --}}
                            <div class="mb-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-dark"><i class="bi bi-receipt me-1 text-info"></i>2. Kesesuaian SPK &amp; Kontrak</span>
                                    <span class="fw-bold {{ $totalWithChecklist > 0 ? ($pctSpk >= 80 ? 'text-success' : 'text-danger') : 'text-muted' }}">{{ $pctSpk }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background-color: #E2E8F0;">
                                    <div class="progress-bar bg-info" style="width: {{ $pctSpk }}%;"></div>
                                </div>
                            </div>

                            {{-- Kriteria 3 --}}
                            <div class="mb-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-dark"><i class="bi bi-gear-wide-connected me-1 text-warning"></i>3. BAUT &amp; Uji Fungsi Teknis</span>
                                    <span class="fw-bold {{ $totalWithChecklist > 0 ? ($pctBaut >= 80 ? 'text-success' : 'text-warning') : 'text-muted' }}">{{ $pctBaut }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background-color: #E2E8F0;">
                                    <div class="progress-bar bg-warning" style="width: {{ $pctBaut }}%;"></div>
                                </div>
                            </div>

                            {{-- Kriteria 4 --}}
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-dark"><i class="bi bi-camera me-1 text-secondary"></i>4. Bukti Fisik Lapangan</span>
                                    <span class="fw-bold {{ $totalWithChecklist > 0 ? ($pctPhys >= 80 ? 'text-success' : 'text-danger') : 'text-muted' }}">{{ $pctPhys }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background-color: #E2E8F0;">
                                    <div class="progress-bar bg-primary" style="width: {{ $pctPhys }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WIDGET 2: Temuan / Log Inspeksi Terkini --}}
                    <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                        <div class="card-header bg-white border-bottom py-2.5 px-3">
                            <span class="fw-bold text-dark small text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.03em;">
                                <i class="bi bi-clock-history text-secondary"></i> Log Inspeksi QC Terkini
                            </span>
                        </div>
                        <div class="card-body p-3">
                            @forelse($recentInspections as $rec)
                                <div class="d-flex align-items-start gap-2.5 pb-2.5 mb-2.5 border-bottom border-light">
                                    <div class="rounded-circle p-1.5 {{ $rec->qc_status === 'verified' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                                        <i class="bi {{ $rec->qc_status === 'verified' ? 'bi-check-lg' : 'bi-x-lg' }} fs-6"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-dark small text-truncate" style="max-width: 150px;">{{ $rec->basto_number }}</span>
                                            <span class="text-muted" style="font-size: 0.68rem;">{{ $rec->qc_verified_at ? $rec->qc_verified_at->diffForHumans() : '—' }}</span>
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                            {{ $rec->qc_notes ? $rec->qc_notes : ($rec->qc_status === 'verified' ? 'Lolos verifikasi tanpa catatan.' : 'Perlu revisi kelengkapan dokumen.') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted small">
                                    <i class="bi bi-journal-check fs-4 d-block opacity-40 mb-1"></i>
                                    Belum ada riwayat verifikasi inspeksi.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- SECTION B: TAMPILAN DASHBOARD SEBELUMNYA (MONITORING FINANSIAL & PORTOFOLIO LENGKAP) --}}
    <div id="sectionFinancialView" class="{{ in_array($initialTab, ['financial', 'all']) ? '' : 'd-none' }}">

        {{-- FILTER PARAMETER MONITORING --}}
        <div class="filter-card mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">
                        <i class="bi bi-funnel-fill text-primary me-1.5"></i>Filter Parameter Monitoring Proyek
                    </h6>
                </div>
                <form action="{{ route('dashboard') }}" method="GET">
                    <input type="hidden" name="tab" value="financial">
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Service Manager / PIC</label>
                            <select name="service_manager" class="form-select">
                                <option value="">Semua Service Manager</option>
                                @foreach($serviceManagerList as $sm)
                                    <option value="{{ $sm }}" {{ $filterSM === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="form-label">Vendor / Klien</label>
                            <select name="client" class="form-select">
                                <option value="">Semua Klien</option>
                                @foreach($clientList as $c)
                                    <option value="{{ $c }}" {{ $filterClient === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="form-label">Periode Waktu</label>
                            <select name="periode" class="form-select">
                                <option value="">Semua Periode</option>
                                <optgroup label="Kuartal &amp; Semester">
                                    <option value="Q1" {{ $filterPeriode === 'Q1' ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                                    <option value="Q2" {{ $filterPeriode === 'Q2' ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                                    <option value="Q3" {{ $filterPeriode === 'Q3' ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                                    <option value="Q4" {{ $filterPeriode === 'Q4' ? 'selected' : '' }}>Q4 (Okt - Des)</option>
                                    <option value="SEMESTER 1" {{ $filterPeriode === 'SEMESTER 1' ? 'selected' : '' }}>Semester 1 (Jan - Jun)</option>
                                    <option value="SEMESTER 2" {{ $filterPeriode === 'SEMESTER 2' ? 'selected' : '' }}>Semester 2 (Jul - Des)</option>
                                </optgroup>
                                <optgroup label="Bulanan">
                                    @foreach($periodeList as $p)
                                        <option value="{{ $p }}" {{ $filterPeriode === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="d-flex justify-content-end align-items-center mb-1" style="min-height: 20px;">
                                @if($filterProject || $filterSM || $filterClient || $filterPeriode)
                                    <a href="{{ route('dashboard', ['tab' => 'financial']) }}" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem; border-radius: 6px;" title="Reset Filter">
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

        {{-- 5 KPI CARDS (Exact Reference Style: 5 Kolom Pastel Cantik) --}}
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

        {{-- ROW 1 CHARTS: TREN BULANAN + GAUGE CAPAIAN ANGGARAN --}}
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

        {{-- ROW 2 CHARTS: TOP KLIEN + BEBAN KERJA SERVICE MANAGER --}}
        <div class="row g-3 mb-4">
            {{-- Top Klien --}}
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

            {{-- Beban Kerja per SM --}}
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

            $sortedVendorClients = $allVendorClients->sortByDesc(function($client) use ($contractLookup) {
                return $contractLookup->get($client)->total ?? 0;
            })->values();

            $grandContract = $stats['total_nilai_kontrak'] ?: 1;
            $grandReal     = $stats['total_realisasi'] ?: 0;
        @endphp

        <div class="card section-card shadow-sm mb-4 border-0">
            <div class="card-header bg-white border-bottom p-3 p-md-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3" style="background:#EFF6FF; color:#0284C7;">
                            <i class="bi bi-diagram-3-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0" style="font-size: 1.05rem;">
                                Matriks Kontrak &amp; Realisasi per Vendor / Klien
                            </h5>
                            <div class="card-subtitle text-muted" style="font-size: 0.78rem;">
                                Monitoring komparasi pagu kontrak, serapan realisasi biaya, dan sisa anggaran proyek
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.73rem; font-weight: 700; color: #475569;">
                            <span>Total: <strong class="text-dark">{{ $sortedVendorClients->count() }} Klien</strong></span>
                        </div>
                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 0.73rem; font-weight: 700; color: #1E3A8A;">
                            <span>Pagu: <strong class="text-primary">Rp {{ number_format($grandContract, 0, ',', '.') }}</strong></span>
                        </div>
                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #ECFDF5; border: 1px solid #A7F3D0; font-size: 0.73rem; font-weight: 700; color: #065F46;">
                            <span>Realisasi: <strong class="text-success">Rp {{ number_format($grandReal, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="custom-scroll-container" style="max-height: 480px; overflow-y: auto;">
                    <table class="table matrix-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 26%; min-width: 220px;"># / Vendor &amp; Klien</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Pagu Kontrak</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Realisasi Biaya</th>
                                <th style="width: 18%; min-width: 160px;" class="text-end">Sisa Pagu</th>
                                <th style="width: 12%; min-width: 120px;">% Serapan</th>
                                <th style="width: 8%; min-width: 100px;" class="text-center">Rincian</th>
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

                                    $collapseId = 'matrix_qc_client_' . $index;
                                    $rankBadgeClass = $index === 0 ? 'rank-gold' : ($index === 1 ? 'rank-silver' : ($index === 2 ? 'rank-bronze' : 'rank-normal'));

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
                                        <div class="matrix-num text-dark" style="font-size: 0.85rem;">Rp {{ number_format($cVal, 0, ',', '.') }}</div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Pagu Total</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num text-success" style="font-size: 0.85rem;">Rp {{ number_format($rVal, 0, ',', '.') }}</div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Biaya Terpakai</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="matrix-num {{ $sisaVal >= 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 0.85rem;">Rp {{ number_format($sisaVal, 0, ',', '.') }}</div>
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

                                {{-- Expanded Child Row --}}
                                <tr class="collapse-child-row">
                                    <td colspan="6" class="p-0 border-0">
                                        <div class="collapse" id="{{ $collapseId }}">
                                            <div class="matrix-child-container">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-bold text-dark" style="font-size: 0.78rem;">
                                                        <i class="bi bi-folder2-open text-primary me-1.5"></i>
                                                        Rincian Proyek Klien: <strong>{{ $client }}</strong>
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.72rem;">Total {{ $allProjIds->count() }} Proyek Terdaftar</span>
                                                </div>
                                                <div class="table-responsive project-subtable shadow-none">
                                                    <table class="table table-sm mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 14%;">Kode Proyek</th>
                                                                <th style="width: 36%;">Nama Proyek</th>
                                                                <th style="width: 16%;" class="text-end">Nilai Kontrak</th>
                                                                <th style="width: 16%;" class="text-end">Realisasi</th>
                                                                <th style="width: 10%;" class="text-end">Sisa Pagu</th>
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
                                                                    <td><span class="badge" style="background:#E0F2FE; color:#0284C7; font-size:0.7rem; font-weight:700;">{{ $pid }}</span></td>
                                                                    <td><div class="text-dark fw-semibold text-truncate" style="max-width: 360px;" title="{{ $pName }}">{{ $pName }}</div></td>
                                                                    <td class="text-end matrix-num text-muted" style="font-size: 0.76rem;">Rp {{ number_format($pValue, 0, ',', '.') }}</td>
                                                                    <td class="text-end matrix-num text-success" style="font-size: 0.76rem;">Rp {{ number_format($rValue, 0, ',', '.') }}</td>
                                                                    <td class="text-end matrix-num {{ $pSisa >= 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 0.76rem;">Rp {{ number_format($pSisa, 0, ',', '.') }}</td>
                                                                    <td class="text-center">
                                                                        <span class="badge rounded-pill" style="background: {{ $pPct >= 80 ? '#ECFDF5; color:#065F46;' : ($pPct >= 40 ? '#F0F9FF; color:#0284C7;' : '#F8FAFC; color:#475569; border:1px solid #E2E8F0;') }} font-size:0.68rem; font-weight:700;">
                                                                            {{ $pPct }}%
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada rincian proyek.</td></tr>
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
        </div>

        {{-- TRANSAKSI REALISASI TERBARU --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0">Aktivitas &amp; Transaksi Realisasi Terbaru</h6>
                </div>
                <a href="{{ route('realisasi.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-bold text-primary" style="font-size:0.75rem;">
                    Lihat Seluruh Realisasi <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                    <thead class="table-light text-secondary text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="ps-4">No. Kontrak / Transaksi</th>
                            <th>Project &amp; Vendor</th>
                            <th>PIC</th>
                            <th>Periode</th>
                            <th class="text-end">Realisasi Biaya</th>
                            <th class="text-center pe-4">Status Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransaksi as $rt)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $rt->no_transaksi ?: ($rt->no_kontrak ?: '—') }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Updated: {{ $rt->updated_at ? $rt->updated_at->diffForHumans() : '—' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 250px;" title="{{ $rt->project_name }}">{{ $rt->project_name }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $rt->vendor ?: 'Tanpa Vendor' }}</div>
                            </td>
                            <td>{{ $rt->pic ?: '—' }}</td>
                            <td><span class="badge bg-light text-secondary border">{{ $rt->periode }} {{ $rt->tahun }}</span></td>
                            <td class="text-end font-monospace fw-bold text-primary">Rp {{ number_format($rt->realisasi_biaya_final, 0, ',', '.') }}</td>
                            <td class="text-center pe-4">
                                @if(in_array(strtoupper($rt->status ?? ''), ['PAID', 'LUNAS', 'DONE']))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size:0.7rem;">{{ $rt->status }}</span>
                                @elseif(in_array(strtoupper($rt->status ?? ''), ['WAIT INV', 'WAITING', 'IN PROGRES']))
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size:0.7rem;">{{ $rt->status }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0.5" style="font-size:0.7rem;">{{ $rt->status ?: 'PENDING' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi realisasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div> <!-- End .screen-only-dashboard -->


{{-- =========================================================================
     DEDICATED FORMAL CORPORATE PRINT LAYOUT (VISIBLE ONLY ON PRINT/PDF)
     ========================================================================= --}}
<div class="print-report-container d-none">

    <!-- PGNCOM Corporate Brand Ribbon (Navy-Teal) -->
    <div style="height: 4px; width: 100%; background: linear-gradient(to right, #ED1B24 0%, #ED1B24 33.3%, #10B981 33.3%, #10B981 66.6%, #005A9C 66.6%, #005A9C 100%) !important; margin-bottom: 9px; border-radius: 2px;"></div>

    <!-- 1. Corporate Official Letterhead -->
    <div class="print-header avoid-break" style="margin-bottom: 8px;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; border-bottom: 2px solid #005A9C;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM Logo" style="height: 44px; width: auto; object-fit: contain;">
                <div>
                    <div style="font-size: 12.5pt; font-weight: 800; color: #0A2540; letter-spacing: -0.01em; line-height: 1.2;">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</div>
                    <div style="font-size: 7.8pt; font-weight: 700; color: #0284C7; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 1px;">
                        PGNCOM &bull; Quality Control &amp; Project Management Office
                    </div>
                    <div style="font-size: 6.8pt; color: #64748B;">Gedung Graha PGAS, Jl. K.H. Zainul Arifin No.20, Jakarta Barat 11140 | www.pgncom.co.id</div>
                </div>
            </div>
            <div style="text-align: right; border: 1.5px solid #0284C7; padding: 4px 9px; border-radius: 6px; background: #F0F9FF !important; flex-shrink: 0;">
                <div style="font-size: 6.2pt; font-weight: 800; color: #0369A1; text-transform: uppercase; margin-bottom: 1px;">Klasifikasi Dokumen</div>
                <div style="font-size: 7.2pt; font-weight: 800; color: #FFFFFF !important; background: #0284C7 !important; padding: 1.5px 6px; border-radius: 3px; display: inline-block; letter-spacing: 0.04em;">TERBATAS / CONFIDENTIAL</div>
                <div style="font-size: 6.2pt; color: #0284C7; font-weight: 700; margin-top: 2px;">Ref: PGN/QC-MON/{{ date('Y/m') }}/{{ str_pad(auth()->id(), 3, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Corporate Hero Title Banner -->
    <div style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 55%, #0284C7 100%) !important; color: #FFFFFF !important; border-radius: 6px; padding: 9px 12px; margin-bottom: 9px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);" class="avoid-break">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="background: rgba(255,255,255,0.2) !important; color: #FFFFFF !important; padding: 1.5px 7px; border-radius: 8px; font-size: 6.2pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 2px;">
                    Laporan Eksekutif Resmi &bull; Pengawasan Mutu &amp; Portofolio
                </span>
                <h2 style="font-size: 11pt; font-weight: 800; color: #FFFFFF !important; margin: 0 0 1px; letter-spacing: -0.01em;">
                    RINGKASAN EKSEKUTIF QUALITY CONTROL &amp; REALISASI PORTOFOLIO PROYEK
                </h2>
                <p style="font-size: 7.2pt; color: #E0F2FE !important; margin: 0; opacity: 0.95;">
                    Verifikasi Kepatuhan Standar Teknis BASTO, Gatekeeper Mutu, Evaluasi Pagu Kontrak, dan Realisasi Finansial
                </p>
            </div>
            <div style="text-align: right; border-left: 1px solid rgba(255,255,255,0.25); padding-left: 12px; flex-shrink: 0;">
                <div style="font-size: 6.5pt; color: #BAE6FD;">STATUS PENGAWASAN</div>
                <div style="font-size: 8.5pt; font-weight: 800; color: #FFFFFF !important;">VERIFIED QC DATA</div>
                <div style="font-size: 6.2pt; color: #E0F2FE;">Tahun Anggaran {{ date('Y') }}</div>
            </div>
        </div>
    </div>

    <!-- 3. Parameter Informasi Laporan -->
    <div style="background: #F8FAFC !important; border: 1.5px solid #CBD5E1 !important; border-left: 4px solid #005A9C !important; border-radius: 6px; padding: 5px 8px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px; font-size: 7pt; margin-bottom: 9px; box-sizing: border-box;" class="avoid-break">
        <div>
            <span style="background: #005A9C !important; color: #FFFFFF !important; font-size: 5.8pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">PERIODE PENGAWASAN</span>
            <strong style="color: #0F172A; display: block; margin-top: 1.5px; font-size: 7.2pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $filterPeriode ?: 'Semua Periode (YTD '.date('Y').')' }}</strong>
        </div>
        <div>
            <span style="background: #0284C7 !important; color: #FFFFFF !important; font-size: 5.8pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">INSPECTOR / AUDITOR QC</span>
            <strong style="color: #0F172A; display: block; margin-top: 1.5px; font-size: 7.2pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }} (OSM QC)</strong>
        </div>
        <div>
            <span style="background: #059669 !important; color: #FFFFFF !important; font-size: 5.8pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">QC PASS RATE</span>
            <strong style="color: #059669; display: block; margin-top: 1.5px; font-size: 7.2pt;">{{ $passRate }}% Lolos Mutu</strong>
        </div>
        <div>
            <span style="background: #475569 !important; color: #FFFFFF !important; font-size: 5.8pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block; text-transform: uppercase;">WAKTU CETAK RESMI</span>
            <strong style="color: #0F172A; display: block; margin-top: 1.5px; font-size: 7.2pt;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</strong>
        </div>
    </div>

    <!-- 4. Executive KPI Scorecards: BARIS 1 (Finansial Proyek) -->
    <div style="font-size: 7.2pt; font-weight: 800; color: #0A2540; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 3.5px; display: flex; align-items: center; gap: 4px;" class="avoid-break">
        <span style="width: 3.5px; height: 10px; background: #005A9C !important; display: inline-block; border-radius: 2px;"></span>
        A. Indikator Kinerja Finansial Portofolio Proyek
    </div>
    <div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 5px; margin-bottom: 8px; box-sizing: border-box;" class="avoid-break">
        <!-- 1. Jumlah Kontrak -->
        <div style="border: 1.5px solid #CBD5E1 !important; border-top: 3px solid #1E293B !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #F8FAFC !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #475569 !important; text-transform: uppercase;">JUMLAH KONTRAK</div>
            <div style="font-size: 9.5pt; font-weight: 800; color: #1E293B !important; margin: 1px 0;">{{ number_format($stats['total_kontrak']) }} <span style="font-size: 6.2pt; font-weight: 600; color: #64748B;">Proyek</span></div>
            <span style="background: #E2E8F0 !important; color: #1E293B !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Portofolio Aktif</span>
        </div>

        <!-- 2. Nilai Kontrak -->
        <div style="border: 1.5px solid #BFDBFE !important; border-top: 3px solid #005A9C !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #EFF6FF !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #005A9C !important; text-transform: uppercase;">TOTAL PAGU KONTRAK</div>
            <div style="font-size: 7.5pt; font-weight: 800; color: #005A9C !important; margin: 1px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Rp {{ number_format($stats['total_nilai_kontrak'], 0, ',', '.') }}</div>
            <span style="background: #DBEAFE !important; color: #1D4ED8 !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Pagu Anggaran</span>
        </div>

        <!-- 3. Realisasi Biaya -->
        <div style="border: 1.5px solid #BBF7D0 !important; border-top: 3px solid #16A34A !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #F0FDF4 !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #15803D !important; text-transform: uppercase;">TOTAL REALISASI</div>
            <div style="font-size: 7.5pt; font-weight: 800; color: #15803D !important; margin: 1px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}</div>
            <span style="background: #DCFCE7 !important; color: #166534 !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Biaya Terpakai</span>
        </div>

        <!-- 4. Total Prognosa -->
        <div style="border: 1.5px solid #FDE68A !important; border-top: 3px solid #D97706 !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #FFFBEB !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #B45309 !important; text-transform: uppercase;">TOTAL PROGNOSA</div>
            <div style="font-size: 7.5pt; font-weight: 800; color: #B45309 !important; margin: 1px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Rp {{ number_format($stats['total_prognosa'], 0, ',', '.') }}</div>
            <span style="background: #FEF3C7 !important; color: #92400E !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Estimasi Selesai</span>
        </div>

        <!-- 5. Capaian Serapan -->
        <div style="border: 1.5px solid #DDD6FE !important; border-top: 3px solid #7C3AED !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #FAF5FF !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #6D28D9 !important; text-transform: uppercase;">% SERAPAN BIAYA</div>
            <div style="font-size: 9.5pt; font-weight: 800; color: #6D28D9 !important; margin: 1px 0;">{{ number_format($stats['persentase_realisasi'], 2) }}%</div>
            <span style="background: #F3E8FF !important; color: #6B21A8 !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Rasio Capaian</span>
        </div>
    </div>

    <!-- 5. Executive KPI Scorecards: BARIS 2 (Pengawasan Mutu QC) -->
    <div style="font-size: 7.2pt; font-weight: 800; color: #0A2540; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 3.5px; display: flex; align-items: center; gap: 4px;" class="avoid-break">
        <span style="width: 3.5px; height: 10px; background: #0284C7 !important; display: inline-block; border-radius: 2px;"></span>
        B. Indikator Kinerja Pengawasan Mutu &amp; Sertifikasi BASTO
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 5px; margin-bottom: 10px; box-sizing: border-box;" class="avoid-break">
        <!-- QC 1: Menunggu Inspeksi -->
        <div style="border: 1.5px solid #FDE68A !important; border-top: 3px solid #D97706 !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #FFFBEB !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #B45309 !important; text-transform: uppercase;">MENUNGGU VERIFIKASI QC</div>
            <div style="font-size: 10pt; font-weight: 800; color: #92400E !important; margin: 1px 0;">{{ $qcPendingCount }} <span style="font-size: 6.2pt; font-weight: 600; color: #B45309;">Berkas</span></div>
            <span style="background: #FEF3C7 !important; color: #92400E !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Antrean Pengujian</span>
        </div>

        <!-- QC 2: Perlu Revisi Mutu -->
        <div style="border: 1.5px solid #FECACA !important; border-top: 3px solid #DC2626 !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #FEF2F2 !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #991B1B !important; text-transform: uppercase;">PERLU REVISI MUTU</div>
            <div style="font-size: 10pt; font-weight: 800; color: #7F1D1D !important; margin: 1px 0;">{{ $qcRevisionCount }} <span style="font-size: 6.2pt; font-weight: 600; color: #991B1B;">Berkas</span></div>
            <span style="background: #FEE2E2 !important; color: #991B1B !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Approval SM Terkunci</span>
        </div>

        <!-- QC 3: Lolos Sertifikasi -->
        <div style="border: 1.5px solid #BBF7D0 !important; border-top: 3px solid #16A34A !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #F0FDF4 !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #15803D !important; text-transform: uppercase;">LOLOS SERTIFIKASI QC</div>
            <div style="font-size: 10pt; font-weight: 800; color: #14532D !important; margin: 1px 0;">{{ $qcVerifiedCount }} <span style="font-size: 6.2pt; font-weight: 600; color: #15803D;">Berkas</span></div>
            <span style="background: #DCFCE7 !important; color: #166534 !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Stempel Resmi Terbit</span>
        </div>

        <!-- QC 4: Pass Rate -->
        <div style="border: 1.5px solid #BAE6FD !important; border-top: 3px solid #0284C7 !important; border-radius: 5px; padding: 4px 2px; text-align: center; background: #F0F9FF !important;">
            <div style="font-size: 5.8pt; font-weight: 800; color: #0369A1 !important; text-transform: uppercase;">QC PASS RATE</div>
            <div style="font-size: 10pt; font-weight: 800; color: #0C4A6E !important; margin: 1px 0;">{{ $passRate }}%</div>
            <span style="background: #E0F2FE !important; color: #0284C7 !important; font-size: 5.4pt; font-weight: 700; padding: 0.5px 4px; border-radius: 6px; display: inline-block;">Tingkat Kepatuhan</span>
        </div>
    </div>

    <!-- 6. Tabel 1: Rekapitulasi Dokumen BASTO & Status QC -->
    <div style="margin-bottom: 9px;" class="avoid-break">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 3.5px;">
            <div style="font-size: 7.4pt; font-weight: 800; color: #0A2540; text-transform: uppercase; letter-spacing: 0.03em; display: flex; align-items: center; gap: 4px;">
                <span style="width: 3.5px; height: 10px; background: #005A9C !important; display: inline-block; border-radius: 2px;"></span>
                1. Daftar Dokumen BASTO &amp; Status Verifikasi Mutu Teknis
            </div>
            <span style="font-size: 6.2pt; color: #64748B; font-weight: 600;">Total: {{ $qcQueue->count() }} Dokumen Terdaftar</span>
        </div>
        <table class="print-table" style="width: 100% !important; table-layout: fixed !important;">
            <thead>
                <tr>
                    <th style="width: 22px; text-align: center;">No</th>
                    <th style="width: 120px;">Nomor BASTO</th>
                    <th>Nama Proyek &amp; Client</th>
                    <th style="width: 90px;">Pengaju (DMO)</th>
                    <th style="width: 65px; text-align: center;">Tanggal</th>
                    <th style="width: 75px; text-align: center;">Status QC</th>
                    <th style="width: 120px;">Kode Stempel / Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qcQueue->take(8) as $idx => $b)
                <tr>
                    <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #0A2540; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b->basto_number }}</td>
                    <td>
                        <div style="font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b->project_name }}</div>
                        <div style="font-size: 5.8pt; color: #64748B;">ID: {{ $b->project_id }}</div>
                    </td>
                    <td style="font-size: 6.8pt; color: #334155;">{{ $b->dmo ? $b->dmo->name : '—' }}</td>
                    <td style="text-align: center; font-size: 6.5pt; color: #64748B;">
                        {{ $b->submitted_at ? $b->submitted_at->format('d/m/Y') : ($b->created_at ? $b->created_at->format('d/m/Y') : '—') }}
                    </td>
                    <td style="text-align: center;">
                        @if($b->qc_status === 'verified')
                            <span style="background: #DCFCE7 !important; color: #166534 !important; border: 1px solid #86EFAC !important; font-size: 5.8pt; font-weight: 800; padding: 1px 4px; border-radius: 3px; display: inline-block;">
                                VERIFIED
                            </span>
                        @elseif($b->qc_status === 'revision_needed')
                            <span style="background: #FEE2E2 !important; color: #991B1B !important; border: 1px solid #FCA5A5 !important; font-size: 5.8pt; font-weight: 800; padding: 1px 4px; border-radius: 3px; display: inline-block;">
                                REVISI
                            </span>
                        @else
                            <span style="background: #F1F5F9 !important; color: #475569 !important; border: 1px solid #CBD5E1 !important; font-size: 5.8pt; font-weight: 700; padding: 1px 4px; border-radius: 3px; display: inline-block;">
                                PENDING
                            </span>
                        @endif
                    </td>
                    <td style="font-size: 6.2pt; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        @if($b->qc_status === 'verified')
                            <strong style="color: #166534;">{{ $b->qc_verification_code }}</strong>
                        @elseif($b->qc_status === 'revision_needed')
                            <span style="color: #991B1B;">{{ Str::limit($b->qc_notes ?: 'Perlu kelengkapan berkas', 35) }}</span>
                        @else
                            <span style="color: #64748B;">Menunggu pemeriksaan berkas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748B; padding: 8px;">Tidak ada antrean dokumen BASTO</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 7. Tabel 2 & Tabel 3: Kepatuhan 4 Kriteria & Monitoring Kesehatan Anggaran -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 9px;" class="avoid-break">
        
        <!-- Tabel Kepatuhan Kriteria Checklist Mutu -->
        <div>
            <div style="font-size: 7.4pt; font-weight: 800; color: #0284C7; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 3.5px; display: flex; align-items: center; gap: 4px;">
                <span style="width: 3.5px; height: 10px; background: #0284C7 !important; display: inline-block; border-radius: 2px;"></span>
                2. Kepatuhan 4 Standar Kriteria Mutu
            </div>
            <table class="print-table print-table-sky" style="width: 100% !important; table-layout: fixed !important;">
                <thead>
                    <tr>
                        <th style="width: 22px; text-align: center;">No</th>
                        <th>Kriteria Mutu Teknis</th>
                        <th style="width: 55px; text-align: center;">Target</th>
                        <th style="width: 65px; text-align: center;">Kepatuhan</th>
                        <th style="width: 65px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $baseC = $totalWithChecklist > 0 ? $totalWithChecklist : 1;
                        $kriteriaList = [
                            ['no' => 1, 'nama' => 'Kelengkapan Administrasi & Ttd', 'val' => $criteriaCompliance['admin_doc'], 'pct' => $totalWithChecklist > 0 ? round(($criteriaCompliance['admin_doc'] / $baseC) * 100) : 0],
                            ['no' => 2, 'nama' => 'Kesesuaian SPK & Kontrak', 'val' => $criteriaCompliance['spk_compliance'], 'pct' => $totalWithChecklist > 0 ? round(($criteriaCompliance['spk_compliance'] / $baseC) * 100) : 0],
                            ['no' => 3, 'nama' => 'BAUT & Uji Fungsi Teknis', 'val' => $criteriaCompliance['baut_teknis'], 'pct' => $totalWithChecklist > 0 ? round(($criteriaCompliance['baut_teknis'] / $baseC) * 100) : 0],
                            ['no' => 4, 'nama' => 'Bukti Fisik & Dokumentasi Lapangan', 'val' => $criteriaCompliance['physical_evidence'], 'pct' => $totalWithChecklist > 0 ? round(($criteriaCompliance['physical_evidence'] / $baseC) * 100) : 0],
                        ];
                    @endphp
                    @foreach($kriteriaList as $k)
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $k['no'] }}</td>
                        <td style="font-weight: 600; color: #0F172A;">{{ $k['nama'] }}</td>
                        <td style="text-align: center; font-size: 6.5pt; color: #64748B;">100%</td>
                        <td style="text-align: center; font-weight: 800; color: {{ $totalWithChecklist > 0 ? ($k['pct'] >= 80 ? '#166534' : '#991B1B') : '#64748B' }};">
                            {{ $totalWithChecklist > 0 ? $k['pct'].'%' : '0%' }}
                        </td>
                        <td style="text-align: center;">
                            @if($totalWithChecklist == 0)
                                <span style="background: #F1F5F9 !important; color: #64748B !important; font-size: 5.5pt; font-weight: 700; padding: 1px 4px; border-radius: 3px;">BELUM ADA DATA</span>
                            @elseif($k['pct'] >= 80)
                                <span style="background: #DCFCE7 !important; color: #166534 !important; font-size: 5.5pt; font-weight: 800; padding: 1px 4px; border-radius: 3px;">MEMENUHI</span>
                            @else
                                <span style="background: #FEE2E2 !important; color: #991B1B !important; font-size: 5.5pt; font-weight: 800; padding: 1px 4px; border-radius: 3px;">REVISI</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tabel Ringkasan Project Budget Health -->
        <div>
            <div style="font-size: 7.4pt; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 3.5px; display: flex; align-items: center; gap: 4px;">
                <span style="width: 3.5px; height: 10px; background: #059669 !important; display: inline-block; border-radius: 2px;"></span>
                3. Deteksi Dini Kesehatan Anggaran Proyek
            </div>
            <table class="print-table print-table-emerald" style="width: 100% !important; table-layout: fixed !important;">
                <thead>
                    <tr>
                        <th>Nama Proyek</th>
                        <th style="width: 75px; text-align: right;">Pagu Kontrak</th>
                        <th style="width: 75px; text-align: right;">Realisasi</th>
                        <th style="width: 45px; text-align: center;">Serapan</th>
                        <th style="width: 55px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(collect($projectHealthList)->take(4) as $ph)
                    <tr>
                        <td style="font-weight: 600; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $ph['project_name'] }}
                        </td>
                        <td style="text-align: right; font-size: 6.5pt; color: #475569; white-space: nowrap;">
                            Rp {{ number_format($ph['pagu'], 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-weight: 700; font-size: 6.5pt; color: #059669; white-space: nowrap;">
                            Rp {{ number_format($ph['realisasi'], 0, ',', '.') }}
                        </td>
                        <td style="text-align: center; font-weight: 800; font-size: 6.5pt; color: {{ $ph['status'] === 'critical' ? '#DC2626' : ($ph['status'] === 'warning' ? '#D97706' : '#16A34A') }};">
                            {{ $ph['serapan_pct'] }}%
                        </td>
                        <td style="text-align: center;">
                            @if($ph['status'] === 'critical')
                                <span style="background: #FEE2E2 !important; color: #991B1B !important; font-size: 5.5pt; font-weight: 800; padding: 1px 3px; border-radius: 2px;">KRITIS</span>
                            @elseif($ph['status'] === 'warning')
                                <span style="background: #FEF3C7 !important; color: #92400E !important; font-size: 5.5pt; font-weight: 800; padding: 1px 3px; border-radius: 2px;">WASPADA</span>
                            @else
                                <span style="background: #DCFCE7 !important; color: #166534 !important; font-size: 5.5pt; font-weight: 800; padding: 1px 3px; border-radius: 2px;">SEHAT</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748B;">Data monitoring kesehatan pagu tidak tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- 8. Lembar Pengesahan Resmi (Prestigious Executive Block) -->
    <div style="margin-top: 6px;" class="avoid-break">
        <div style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%) !important; color: #FFFFFF !important; font-size: 6.8pt; font-weight: 800; text-transform: uppercase; padding: 4px 8px; text-align: center; letter-spacing: 0.08em; border-radius: 5px 5px 0 0;">
            LEMBAR PENGESAHAN LAPORAN EKSEKUTIF PENGAWASAN MUTU (QC)
        </div>
        <div style="border: 1.5px solid #CBD5E1; border-top: none; border-radius: 0 0 5px 5px; padding: 8px 10px 6px; background: #F8FAFC !important; box-sizing: border-box;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; text-align: center;">
                <div>
                    <div style="font-size: 6.5pt; color: #64748B;">Diverifikasi &amp; Disusun Oleh,</div>
                    <div style="font-size: 6.8pt; font-weight: 700; color: #0A2540; margin-top: 1px;">{{ \App\Models\AppSetting::getValue('signer_qc_lead_title', 'Inspector Quality Control') }}</div>
                    <div style="height: 28px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1px dashed #CBD5E1; border-radius: 3px; padding: 1px 4px; font-size: 5.5pt; color: #94A3B8; text-transform: uppercase;">Paraf QC Inspector</span>
                    </div>
                    <div style="font-size: 7.5pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 8px;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size: 6.2pt; color: #0284C7; font-weight: 600; margin-top: 1px;">{{ \App\Models\AppSetting::getValue('signer_qc_lead_nip', 'OSM Quality Control') }}</div>
                </div>

                <div>
                    <div style="font-size: 6.5pt; color: #64748B;">Diperiksa Oleh,</div>
                    <div style="font-size: 6.8pt; font-weight: 700; color: #0A2540; margin-top: 1px;">Service Manager / Project Control</div>
                    <div style="height: 28px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1px dashed #CBD5E1; border-radius: 3px; padding: 1px 4px; font-size: 5.5pt; color: #94A3B8; text-transform: uppercase;">Paraf Service Manager</span>
                    </div>
                    <div style="font-size: 7.5pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 8px;">
                        {{ $filterSM && $filterSM !== 'Semua SM & PIC' ? $filterSM : '...........................................' }}
                    </div>
                    <div style="font-size: 6.2pt; color: #0284C7; font-weight: 600; margin-top: 1px;">OSM Service Manager</div>
                </div>

                <div>
                    <div style="font-size: 6.5pt; color: #64748B;">Disetujui Oleh,</div>
                    <div style="font-size: 6.8pt; font-weight: 700; color: #0A2540; margin-top: 1px;">{{ \App\Models\AppSetting::getValue('signer_vp_title', 'VP Information Technology & Project Management') }}</div>
                    <div style="height: 28px; display: flex; align-items: center; justify-content: center;">
                        <span style="border: 1.5px dashed #005A9C; border-radius: 3px; padding: 1px 4px; font-size: 5.5pt; color: #005A9C; font-weight: 700; text-transform: uppercase;">[ STEMPEL SAH RESMI ]</span>
                    </div>
                    <div style="font-size: 7.5pt; font-weight: 800; color: #0A2540; border-bottom: 1.5px solid #005A9C; display: inline-block; padding: 0 8px;">
                        {{ \App\Models\AppSetting::getValue('signer_vp_name', 'Dedi Suherman, S.T., M.M.') }}
                    </div>
                    <div style="font-size: 6.2pt; color: #0284C7; font-weight: 600; margin-top: 1px;">{{ \App\Models\AppSetting::getValue('signer_vp_nip', 'Vice President OSM — PGNCOM') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 9. Corporate Print Footer with Colored Ribbon -->
    <div style="margin-top: 8px; padding: 5px 8px; background: #F1F5F9 !important; border: 1px solid #CBD5E1 !important; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; font-size: 6.5pt; color: #475569;" class="avoid-break">
        <div>
            <strong style="color: #0A2540;">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</strong> &bull; Divisi Service Management &amp; Operation (SMO) &bull; Sistem Monitoring Mutu &amp; Realisasi
        </div>
        <div style="font-weight: 600;">
            Dicetak: {{ date('d/m/Y H:i:s') }} WIB &bull; Dokumen Sah Internal
        </div>
    </div>
    <div style="height: 3px; width: 100%; background: linear-gradient(to right, #ED1B24 0%, #ED1B24 33.3%, #10B981 33.3%, #10B981 66.6%, #005A9C 66.6%, #005A9C 100%) !important; margin-top: 2px; border-radius: 2px;"></div>

</div> <!-- End .print-report-container -->


{{-- MODAL INSPEKSI QC UNTUK SETIAP BASTO DALAM ANTREAN --}}
@push('modals')
@foreach($qcQueue as $basto)
    <div class="modal fade" id="qcModal{{ $basto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog {{ $basto->attachment_file ? 'modal-xl' : 'modal-lg' }} modal-dialog-centered" style="{{ $basto->attachment_file ? 'max-width: 95vw;' : '' }}">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <form action="{{ route('basto.qc-verify', $basto->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <div class="text-white">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-white text-info fw-bold px-2.5 py-1 rounded-pill">QC INSPECTION &amp; AUDIT MUTU</span>
                                <span class="fw-bold fs-6">{{ $basto->basto_number }}</span>
                                @if($basto->attachment_file)
                                    <span class="badge bg-white bg-opacity-25 text-white small"><i class="bi bi-paperclip me-1"></i>Lampiran Siap Periksa</span>
                                @endif
                            </div>
                            <div class="small text-white text-opacity-85">{{ $basto->project_name }} ({{ $basto->project_id }})</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        @if($basto->attachment_file)
                            <div class="row g-3">
                                {{-- KOLOM KIRI: IN-APP LIVE DOCUMENT & EVIDENCE PREVIEWER --}}
                                <div class="col-lg-6 col-xl-7">
                                    <div class="card border rounded-3 h-100 shadow-none d-flex flex-column" style="background: #0F172A; min-height: 590px;">
                                        <div class="card-header border-bottom border-secondary border-opacity-25 py-2 px-3 d-flex align-items-center justify-content-between bg-dark text-white">
                                            <div class="d-flex align-items-center gap-2 small">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                                <span class="fw-bold font-monospace text-truncate text-white" style="max-width: 260px;" title="{{ basename($basto->attachment_file) }}">
                                                    {{ basename($basto->attachment_file) }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1.5">
                                                <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-sm btn-outline-light py-1 px-2.5 rounded-pill" style="font-size: 0.72rem;" title="Buka di Tab Baru">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Tab Baru
                                                </a>
                                                <a href="{{ route('basto.attachment', $basto->id) }}" download class="btn btn-sm btn-light py-1 px-2.5 rounded-pill fw-bold text-dark" style="font-size: 0.72rem;" title="Unduh File">
                                                    <i class="bi bi-download me-1"></i> Unduh
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body p-0 flex-grow-1 position-relative" style="background: #1E293B;">
                                            @php
                                                $ext = strtolower(pathinfo($basto->attachment_file, PATHINFO_EXTENSION));
                                            @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                                <div class="d-flex align-items-center justify-content-center h-100 p-3" style="min-height: 530px; max-height: 650px; overflow: auto;">
                                                    <img src="{{ route('basto.attachment', $basto->id) }}" alt="Bukti Fisik BASTO" class="img-fluid rounded shadow" style="max-height: 600px; object-fit: contain;">
                                                </div>
                                            @else
                                                <iframe src="{{ route('basto.attachment', $basto->id) }}#toolbar=1&navpanes=0" class="w-100 h-100 border-0 rounded-bottom" style="min-height: 550px;" title="Preview Berkas BASTO"></iframe>
                                            @endif
                                        </div>
                                        <div class="card-footer py-1.5 px-3 bg-dark text-muted d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                                            <span class="text-white-50"><i class="bi bi-eye-fill text-info me-1"></i> Live In-App Document Viewer</span>
                                            <span class="text-white-50">Pengaju: <b>{{ $basto->dmo ? $basto->dmo->name : 'DMO' }}</b> &bull; SM: <b>{{ $basto->sm ? $basto->sm->name : 'SM' }}</b></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- KOLOM KANAN: QC CHECKLIST & VERIFIKASI MUTU --}}
                                <div class="col-lg-6 col-xl-5 d-flex flex-column">
                                    <div class="h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            {{-- Standarisasi Checklist 4 Kriteria --}}
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label text-dark fw-bold small mb-0">
                                                        <i class="bi bi-check2-square text-primary me-1"></i> 1. Standarisasi Mutu Teknis
                                                    </label>
                                                    <button type="button" class="btn btn-link text-primary p-0 small text-decoration-none fw-bold" onclick="checkAllQcItems('{{ $basto->id }}')">
                                                        <i class="bi bi-check-all me-1"></i> Centang Semua (100%)
                                                    </button>
                                                </div>
                                                
                                                @php
                                                    $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                                                @endphp
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_qc_{{ $basto->id }}" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_admin_qc_{{ $basto->id }}">
                                                                    1. Kelengkapan Administrasi &amp; Ttd
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Kop surat, nomor BASTO, ttd basah/digital DMO, vendor &amp; tanggal sah.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_qc_{{ $basto->id }}" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_spk_qc_{{ $basto->id }}">
                                                                    2. Kesesuaian SPK &amp; Kontrak
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Nomor project, scope pekerjaan, dan jangka waktu sinkron dengan SPK.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_qc_{{ $basto->id }}" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_baut_qc_{{ $basto->id }}">
                                                                    3. BAUT &amp; Uji Fungsi Teknis
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Berita Acara Uji Terima teknis terpenuhi tanpa kendala operasional.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_qc_{{ $basto->id }}" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_phys_qc_{{ $basto->id }}">
                                                                    4. Bukti Fisik Lapangan
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Foto instalasi fisik / aktivitas operasional terlampir jelas dan valid.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status Keputusan Mutu --}}
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-bold small mb-1">
                                                    <i class="bi bi-shield-check text-success me-1"></i> 2. Keputusan Verifikasi Mutu
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_qc_{{ $basto->id }}" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                                            <label class="form-check-label fw-bold text-success small cursor-pointer mb-0" for="status_verified_qc_{{ $basto->id }}">
                                                                <i class="bi bi-patch-check-fill me-1"></i> VERIFIED (Lolos)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_qc_{{ $basto->id }}" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                                            <label class="form-check-label fw-bold text-danger small cursor-pointer mb-0" for="status_revision_qc_{{ $basto->id }}">
                                                                <i class="bi bi-exclamation-octagon-fill me-1"></i> REVISI (Kunci)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Template Catatan Temuan Cepat --}}
                                            <div class="mb-2">
                                                <label class="form-label text-dark fw-bold small mb-1">
                                                    <i class="bi bi-pencil-square text-secondary me-1"></i> 3. Catatan Inspeksi &amp; Temuan
                                                </label>
                                                <div class="mb-2 p-2 rounded-3 border bg-light" style="font-size: 0.72rem;">
                                                    <span class="text-secondary fw-bold"><i class="bi bi-lightning-charge-fill text-warning"></i> Template Cepat:</span>
                                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('{{ $basto->id }}', 'Berkas BASTO fisik belum ditandatangani basah oleh pihak berwenang.')">+ Ttd Basah</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('{{ $basto->id }}', 'Lampiran Berita Acara Uji Terima (BAUT) teknis belum disertakan.')">+ BAUT Kurang</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('{{ $basto->id }}', 'Foto dokumentasi bukti fisik lapangan buram / kurang representatif.')">+ Foto Buram</button>
                                                        <button type="button" class="btn btn-outline-success btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('{{ $basto->id }}', 'Pemeriksaan fisik dan dokumen lengkap, pengujian teknis operasional lolos 100% tanpa deviasi.')">+ Lolos 100%</button>
                                                    </div>
                                                </div>
                                                <textarea id="qcNotesArea_{{ $basto->id }}" name="qc_notes" rows="2" class="form-control form-control-sm rounded-3" placeholder="Tuliskan catatan teknis pemeriksaan fisik/berkas...">{{ $basto->qc_notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- TAMPILAN JIKA BELUM ADA BERKAS LAMPIRAN FISIK --}}
                            <div class="alert alert-warning border rounded-3 d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block text-dark">Belum Ada Berkas Lampiran PDF BASTO</strong>
                                    <span class="small text-muted">DMO belum mengunggah berkas fisik bertanda tangan untuk dokumen ini. Tim QC dapat memberikan evaluasi atau meminta perbaikan berkas.</span>
                                </div>
                            </div>

                            {{-- Checklist versi Single Column --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-dark fw-bold small mb-0">
                                        <i class="bi bi-check2-square text-primary me-1"></i> Standarisasi Mutu Teknis
                                    </label>
                                    <button type="button" class="btn btn-link text-primary p-0 small text-decoration-none fw-bold" onclick="checkAllQcItems('{{ $basto->id }}')">
                                        Centang Semua (100%)
                                    </button>
                                </div>
                                @php
                                    $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                                @endphp
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_qc_sc_{{ $basto->id }}" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_admin_qc_sc_{{ $basto->id }}">1. Administrasi &amp; Ttd</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_qc_sc_{{ $basto->id }}" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_spk_qc_sc_{{ $basto->id }}">2. Kesesuaian SPK &amp; Kontrak</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_qc_sc_{{ $basto->id }}" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_baut_qc_sc_{{ $basto->id }}">3. BAUT &amp; Uji Fungsi</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_qc_sc_{{ $basto->id }}" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_phys_qc_sc_{{ $basto->id }}">4. Bukti Fisik Lapangan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold small mb-1">Keputusan Verifikasi Mutu</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_qc_sc_{{ $basto->id }}" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold text-success small cursor-pointer" for="status_verified_qc_sc_{{ $basto->id }}">VERIFIED (Lolos)</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_qc_sc_{{ $basto->id }}" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold text-danger small cursor-pointer" for="status_revision_qc_sc_{{ $basto->id }}">REVISI (Kunci)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold small mb-1">Catatan Inspeksi</label>
                                <textarea id="qcNotesArea_{{ $basto->id }}" name="qc_notes" rows="2" class="form-control rounded-3" placeholder="Masukkan catatan teknis...">{{ $basto->qc_notes }}</textarea>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Hasil Verifikasi QC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endpush

@push('scripts')
<script>
// =========================================================================
// VIEW MODE SWITCHER (QC, FINANCIAL, ALL)
// =========================================================================
function switchDashboardView(mode, btn) {
    // Reset active button state
    document.querySelectorAll('#viewModeContainer .view-mode-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const secQc = document.getElementById('sectionQcView');
    const secFin = document.getElementById('sectionFinancialView');

    if (mode === 'qc') {
        secQc.classList.remove('d-none');
        secFin.classList.add('d-none');
    } else if (mode === 'financial') {
        secQc.classList.add('d-none');
        secFin.classList.remove('d-none');
        // Trigger chart redraw when tab becomes visible
        triggerChartsRedraw();
    } else if (mode === 'all') {
        secQc.classList.remove('d-none');
        secFin.classList.remove('d-none');
        triggerChartsRedraw();
    }
}

// Filter tabel antrean BASTO via pill buttons
function filterQueueTable(filter, btn) {
    document.querySelectorAll('#qcFilterPills button').forEach(b => {
        b.classList.remove('btn-primary', 'fw-bold');
        b.classList.add('btn-light', 'text-dark');
    });
    btn.classList.remove('btn-light', 'text-dark');
    btn.classList.add('btn-primary', 'fw-bold');

    const rows = document.querySelectorAll('.queue-row');
    rows.forEach(r => {
        if (filter === 'all') {
            r.classList.remove('d-none');
        } else {
            if (r.classList.contains('queue-' + filter)) {
                r.classList.remove('d-none');
            } else {
                r.classList.add('d-none');
            }
        }
    });
}

function checkAllQcItems(id) {
    document.querySelectorAll(`.qc-chk-${id}`).forEach(cb => cb.checked = true);
    const verifiedRadio = document.getElementById(`status_verified_${id}`);
    if (verifiedRadio) verifiedRadio.checked = true;
}

function appendQcNote(id, text) {
    const textarea = document.getElementById(`qcNotesArea_${id}`);
    if (textarea) {
        if (textarea.value.trim().length > 0) {
            textarea.value += '\n• ' + text;
        } else {
            textarea.value = '• ' + text;
        }
    }
}

// =========================================================================
// CHART.JS INITIALIZATION (TAMPILAN SEBELUMNYA)
// =========================================================================
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

let chartMonthly = null;
let chartGauge   = null;
let chartTopCli  = null;
let chartSM      = null;

function initCharts() {
    // 1. TREN BULANAN REALISASI VS PROGNOSA
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

    const canvasMonthly = document.getElementById('chartMonthlyTrend');
    if (canvasMonthly) {
        chartMonthly = new Chart(canvasMonthly, {
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
    }

    // 2. DOUGHNUT GAUGE: RASIO PENYERAPAN ANGGARAN
    const totalK = {{ $stats['total_nilai_kontrak'] }};
    const totalR = {{ $stats['total_realisasi'] }};
    const sisa   = Math.max(0, totalK - totalR);

    const canvasGauge = document.getElementById('chartComparisonGauge');
    if (canvasGauge) {
        chartGauge = new Chart(canvasGauge, {
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
    }

    // 3. TOP KLIEN: KONTRAK VS REALISASI (HORIZONTAL BAR)
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

    const canvasTopCli = document.getElementById('chartTopClients');
    if (canvasTopCli) {
        chartTopCli = new Chart(canvasTopCli, {
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
    }

    // 4. BEBAN KERJA & NILAI KONTRAK SERVICE MANAGER
    const smData = @json($smStats);
    const canvasSM = document.getElementById('chartSMStats');
    if (canvasSM) {
        chartSM = new Chart(canvasSM, {
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
    }
}

function triggerChartsRedraw() {
    setTimeout(() => {
        if (chartMonthly) chartMonthly.resize();
        if (chartGauge) chartGauge.resize();
        if (chartTopCli) chartTopCli.resize();
        if (chartSM) chartSM.resize();
    }, 50);
}

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
});
</script>
@endpush

@endsection
