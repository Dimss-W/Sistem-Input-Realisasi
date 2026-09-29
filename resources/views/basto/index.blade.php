@extends('layouts.main')

@section('title', 'BASTO — Berita Acara Serah Terima Operasi')
@section('page-title', 'Manajemen BASTO')

@section('breadcrumb')
    <li class="breadcrumb-item active">BASTO</li>
@endsection

@push('styles')
<style>
    /* ===== PRINT STYLES UNTUK REKAP LAPORAN BASTO ===== */
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

        .d-print-none,
        .sidebar,
        .topbar,
        .btn,
        .filter-card,
        .card-header,
        .navbar,
        #sidebarToggle,
        .modal,
        .alert,
        .stagger-row input[type="checkbox"],
        #selectAllBasto {
            display: none !important;
        }

        .main-content,
        .page-entrance,
        .container-fluid,
        .card {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .table-responsive {
            max-height: none !important;
            overflow: visible !important;
        }

        .print-only-block {
            display: block !important;
        }

        .print-only-flex {
            display: flex !important;
        }

        /* Kop Surat Resmi PGN */
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
            grid-template-columns: repeat(5, 1fr);
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
            font-size: 11pt;
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
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 7.5pt !important;
            margin-bottom: 14px;
        }

        .table th {
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

        .table td {
            border: 1px solid #94a3b8 !important;
            padding: 3.5px 5px !important;
            vertical-align: middle;
            color: #0f172a !important;
        }

        .table tr:nth-child(even) td {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table tr {
            page-break-inside: avoid !important;
        }

        .badge-print {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 6.8pt;
            font-weight: 700;
            border: 1px solid #475569;
            color: #0f172a !important;
            background: #ffffff !important;
            text-transform: uppercase;
        }

        /* Lembar Tanda Tangan Formal 3 Kolom */
        .signature-section {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 18px;
            page-break-inside: avoid !important;
        }

        .sig-box {
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
            background: #ffffff;
        }

        .sig-role {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.04em;
        }

        .sig-space {
            height: 48px;
        }

        .sig-name {
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
            line-height: 1.1;
        }

        .sig-nip {
            font-size: 6.8pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Footer Cetak */
        .corp-print-footer {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            margin-top: 14px;
            font-size: 6.8pt;
            color: #64748b;
        }
    }

    /* Mode Pratinjau di Layar Monitor */
    @media screen {
        @if(request('print') == '1')
        .sidebar, .topbar, #sidebarToggle, .filter-card, .btn-print-hide, .pagination-container, .nav-tabs, .alert, .card-header, #batchActionBar, #batchSubmitActionBar, .row.g-3.mb-3 {
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
        .doc-title-block { display: block !important; }
        .meta-param-grid { display: grid !important; }
        .print-summary-box { display: grid !important; }
        .signature-section { display: grid !important; }
        .corp-print-footer { display: flex !important; }
        @else
        .corp-print-header,
        .doc-title-block,
        .meta-param-grid,
        .print-summary-box,
        .signature-section,
        .corp-print-footer {
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
        'filename'    => 'Laporan_Rekapitulasi_BASTO_A4_' . date('Ymd_His'),
        'orientation' => 'landscape'
    ])
@endif

{{-- BASTO Status Summary (Interactive Cards) --}}
<div class="row g-3 mb-3 d-print-none">
    @php
        $cards = [
            ['label' => 'Draft',     'count' => $statusCounts['draft'],     'icon' => 'bi-file-earmark',      'color' => 'secondary', 'tab' => 'dmo_draft'],
            ['label' => 'Submitted', 'count' => $statusCounts['submitted'], 'icon' => 'bi-send-fill',         'color' => 'primary',   'tab' => 'pending_review'],
            ['label' => 'Approved',  'count' => $statusCounts['approved'],  'icon' => 'bi-check-circle-fill', 'color' => 'success',   'tab' => 'approved'],
            ['label' => 'Rejected',  'count' => $statusCounts['rejected'],  'icon' => 'bi-x-circle-fill',     'color' => 'danger',    'tab' => 'rejected'],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('basto.index', ['tab' => $card['tab']]) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 {{ ($filters['tab'] ?? '') === $card['tab'] ? 'ring-2 border-2 border-' . $card['color'] . ' bg-' . $card['color'] . ' bg-opacity-10' : '' }}" style="transition: transform 0.15s ease, box-shadow 0.15s ease;">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 p-3 bg-{{ $card['color'] }} bg-opacity-10">
                        <i class="bi {{ $card['icon'] }} fs-4 text-{{ $card['color'] }}"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-dark">{{ $card['count'] }}</div>
                        <div class="text-muted small mt-1">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Unified Clean Filter Card --}}
<div class="card border-0 shadow-sm rounded-4 mb-3 d-print-none">
    <div class="card-body py-3 px-4">
        <form method="GET" action="{{ route('basto.index') }}" id="bastoFilterForm" class="row g-2 align-items-center">
            @if(!empty($filters['tab']))
                <input type="hidden" name="tab" value="{{ $filters['tab'] }}">
            @endif
            <div class="col-lg-4 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari No. BASTO, Project, atau SPK..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <select name="status" class="form-select form-select-sm bg-light">
                    <option value="">Semua Status</option>
                    @foreach(['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $sVal => $sLbl)
                        <option value="{{ $sVal }}" {{ ($filters['status'] ?? '') === $sVal ? 'selected' : '' }}>{{ $sLbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <select name="qc_status" class="form-select form-select-sm bg-light">
                    <option value="">Status Mutu QC</option>
                    <option value="pending" {{ ($filters['qc_status'] ?? '') === 'pending' ? 'selected' : '' }}>Menunggu QC</option>
                    <option value="verified" {{ ($filters['qc_status'] ?? '') === 'verified' ? 'selected' : '' }}>Lolos Verifikasi QC</option>
                    <option value="revision_needed" {{ ($filters['qc_status'] ?? '') === 'revision_needed' ? 'selected' : '' }}>Perlu Revisi Mutu</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6">
                <select name="project_id" class="form-select form-select-sm bg-light">
                    <option value="">Semua Project</option>
                    @foreach($projectIds as $pid)
                        <option value="{{ $pid }}" {{ ($filters['project_id'] ?? '') === $pid ? 'selected' : '' }}>{{ $pid }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill fw-semibold rounded-3 shadow-sm" type="submit">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('basto.index') }}" class="btn btn-light btn-sm px-2.5 rounded-3 text-secondary border" title="Reset Semua Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Single Sleek Pipeline Tab Bar & Batch Action Bars (Screen Only) --}}
<div class="d-print-none">
@php
    $currentTab = $filters['tab'] ?? '';
    $user = auth()->user();
@endphp

@if($user->hasRole('admin'))
    {{-- ADMIN: Alur Pipa Terpadu (Satu Baris Bersih & Terstruktur) --}}
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <span class="text-secondary small fw-bold text-uppercase me-2 d-none d-md-inline" style="font-size: 0.72rem; letter-spacing: 0.05em;">
            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Alur BASTO:
        </span>
        <a href="{{ route('basto.index') }}" class="btn btn-sm {{ empty($currentTab) ? 'btn-dark fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            Semua Dokumen
        </a>
        <a href="{{ route('basto.index', ['tab' => 'dmo_draft']) }}" class="btn btn-sm {{ $currentTab === 'dmo_draft' ? 'btn-secondary text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-pencil-square me-1"></i> Draf
            @if(($statusCounts['draft'] ?? 0) > 0)
                <span class="badge bg-secondary text-white ms-1">{{ $statusCounts['draft'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'waiting_qc']) }}" class="btn btn-sm {{ in_array($currentTab, ['waiting_qc', 'qc_pending']) ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-hourglass-split me-1"></i> Menunggu QC
            @if(($qcCounts['pending'] ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $qcCounts['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'revision_needed']) }}" class="btn btn-sm {{ in_array($currentTab, ['revision_needed', 'qc_revision']) ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Revisi QC
            @if(($dmoTabCounts['revision_needed'] ?? 0) > 0)
                <span class="badge bg-danger text-white ms-1">{{ $dmoTabCounts['revision_needed'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'ready_to_approve']) }}" class="btn btn-sm {{ $currentTab === 'ready_to_approve' ? 'btn-info text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-patch-check-fill me-1"></i> Siap Disetujui SM (Lolos QC)
            @if(($smTabCounts['ready_to_approve'] ?? 0) > 0)
                <span class="badge bg-info text-white ms-1">{{ $smTabCounts['ready_to_approve'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'approved']) }}" class="btn btn-sm {{ $currentTab === 'approved' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
            @if(($statusCounts['approved'] ?? 0) > 0)
                <span class="badge bg-success text-white ms-1">{{ $statusCounts['approved'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'rejected']) }}" class="btn btn-sm {{ $currentTab === 'rejected' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Ditolak
            @if(($statusCounts['rejected'] ?? 0) > 0)
                <span class="badge bg-danger text-white ms-1">{{ $statusCounts['rejected'] }}</span>
            @endif
        </a>
    </div>
@elseif($user->hasRole('dmo'))
    {{-- ROLE DMO: Tab Berkas Khusus DMO --}}
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <span class="text-secondary small fw-bold text-uppercase me-2 d-none d-md-inline" style="font-size: 0.72rem; letter-spacing: 0.05em;">
            <i class="bi bi-person-workspace text-primary me-1"></i> Berkas DMO:
        </span>
        <a href="{{ route('basto.index') }}" class="btn btn-sm {{ empty($currentTab) ? 'btn-primary fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            Semua BASTO
        </a>
        <a href="{{ route('basto.index', ['tab' => 'revision_needed']) }}" class="btn btn-sm {{ $currentTab === 'revision_needed' ? 'btn-danger fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> ⚠️ Perlu Revisi QC
            @if(($dmoTabCounts['revision_needed'] ?? 0) > 0)
                <span class="badge bg-danger text-white ms-1">{{ $dmoTabCounts['revision_needed'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'dmo_draft']) }}" class="btn btn-sm {{ $currentTab === 'dmo_draft' ? 'btn-secondary text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-pencil-square me-1"></i> Draf Belum Diajukan
            @if(($dmoTabCounts['draft'] ?? 0) > 0)
                <span class="badge bg-secondary text-white ms-1">{{ $dmoTabCounts['draft'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'dmo_in_progress']) }}" class="btn btn-sm {{ $currentTab === 'dmo_in_progress' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-hourglass-split me-1"></i> Sedang Diproses
            @if(($dmoTabCounts['in_progress'] ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $dmoTabCounts['in_progress'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'approved']) }}" class="btn btn-sm {{ $currentTab === 'approved' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
        </a>
    </div>
@elseif($user->hasRole('osm_service_manager'))
    {{-- ROLE SERVICE MANAGER: Tab Persetujuan Khusus SM --}}
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <span class="text-secondary small fw-bold text-uppercase me-2 d-none d-md-inline" style="font-size: 0.72rem; letter-spacing: 0.05em;">
            <i class="bi bi-person-check text-primary me-1"></i> Persetujuan SM:
        </span>
        <a href="{{ route('basto.index') }}" class="btn btn-sm {{ empty($currentTab) ? 'btn-primary fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            Semua BASTO
        </a>
        <a href="{{ route('basto.index', ['tab' => 'ready_to_approve']) }}" class="btn btn-sm {{ $currentTab === 'ready_to_approve' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-patch-check-fill text-warning me-1"></i> ⭐ Siap Disetujui (Lolos QC)
            @if(($smTabCounts['ready_to_approve'] ?? 0) > 0)
                <span class="badge bg-white text-success ms-1">{{ $smTabCounts['ready_to_approve'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'waiting_qc']) }}" class="btn btn-sm {{ $currentTab === 'waiting_qc' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-hourglass-split me-1"></i> Menunggu QC
            @if(($smTabCounts['waiting_qc'] ?? 0) > 0)
                <span class="badge bg-dark text-white ms-1">{{ $smTabCounts['waiting_qc'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'pending_review']) }}" class="btn btn-sm {{ $currentTab === 'pending_review' ? 'btn-secondary text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-inbox me-1"></i> Semua Menunggu Review
        </a>
        <a href="{{ route('basto.index', ['tab' => 'approved']) }}" class="btn btn-sm {{ $currentTab === 'approved' ? 'btn-outline-success fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-check-circle me-1"></i> Disetujui
        </a>
        <a href="{{ route('basto.index', ['tab' => 'rejected']) }}" class="btn btn-sm {{ $currentTab === 'rejected' ? 'btn-outline-danger fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Ditolak
        </a>
    </div>
@elseif($user->hasRole('osm_qc'))
    {{-- ROLE QC: Tab Antrean Verifikasi Mutu --}}
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <span class="text-secondary small fw-bold text-uppercase me-2 d-none d-md-inline" style="font-size: 0.72rem; letter-spacing: 0.05em;">
            <i class="bi bi-shield-check text-info me-1"></i> Antrean QC:
        </span>
        <a href="{{ route('basto.index') }}" class="btn btn-sm {{ empty($currentTab) ? 'btn-primary fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            Semua Dokumen
        </a>
        <a href="{{ route('basto.index', ['tab' => 'qc_pending']) }}" class="btn btn-sm {{ $currentTab === 'qc_pending' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-hourglass-split me-1"></i> Menunggu Inspeksi QC
            @if(($qcCounts['pending'] ?? 0) > 0)
                <span class="badge bg-danger text-white ms-1">{{ $qcCounts['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'qc_revision']) }}" class="btn btn-sm {{ $currentTab === 'qc_revision' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Revisi Mutu
            @if(($qcCounts['revision'] ?? 0) > 0)
                <span class="badge bg-danger text-white ms-1">{{ $qcCounts['revision'] }}</span>
            @endif
        </a>
        <a href="{{ route('basto.index', ['tab' => 'qc_verified']) }}" class="btn btn-sm {{ $currentTab === 'qc_verified' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-white bg-white text-secondary border' }} rounded-pill px-3">
            <i class="bi bi-patch-check-fill me-1"></i> Lolos Verifikasi QC
            @if(($qcCounts['verified'] ?? 0) > 0)
                <span class="badge bg-success text-white ms-1">{{ $qcCounts['verified'] }}</span>
            @endif
        </a>
    </div>
@endif

{{-- BATCH ACTION BAR (FOR SERVICE MANAGER / ADMIN) --}}
@if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
<div id="batchActionBar" class="card border-0 shadow-sm rounded-4 mb-3 d-none" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);">
    <div class="card-body py-2.5 px-4 d-flex align-items-center justify-content-between text-white flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold" id="selectedCountBadge">0 BASTO Terpilih</span>
            <span class="small text-white text-opacity-80 d-none d-md-inline">Persetujuan serentak dokumen BASTO yang telah diverifikasi kelengkapannya</span>
        </div>
        <form id="batchApproveForm" action="{{ route('basto.batch-approve') }}" method="POST" class="m-0">
            @csrf
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-1.5 rounded-3 shadow-sm d-flex align-items-center gap-1.5" onclick="confirmBatchApprove()">
                <i class="bi bi-check2-all fs-6"></i> Setujui BASTO Terpilih (Batch Approve)
            </button>
        </form>
    </div>
</div>
@endif

{{-- BATCH SUBMIT ACTION BAR (FOR DMO / ADMIN) --}}
@if(auth()->user()->hasRole(['dmo', 'admin']))
<div id="batchSubmitActionBar" class="card border-0 shadow-sm rounded-4 mb-3 d-none" style="background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);">
    <div class="card-body py-2.5 px-4 d-flex align-items-center justify-content-between text-white flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold" id="selectedSubmitCountBadge">0 Draf Terpilih</span>
            <span class="small text-white text-opacity-90 d-none d-md-inline">Pengajuan serentak draf BASTO fisik ke Service Manager tujuan</span>
        </div>
        <form id="batchSubmitForm" action="{{ route('basto.batch-submit') }}" method="POST" class="m-0">
            @csrf
            <button type="button" class="btn btn-warning text-dark btn-sm fw-bold px-3.5 py-1.5 rounded-3 shadow-sm d-flex align-items-center gap-1.5" onclick="confirmBatchSubmit()">
                <i class="bi bi-send-fill fs-6"></i> Ajukan Draf Terpilih ke SM (Batch Submit)
            </button>
        </form>
    </div>
</div>
@endif
<div id="printableReportArea" class="printable-report-card">
{{-- ===== PRINT ONLY OFFICIAL PGN CORPORATE HEADER & SUMMARY ===== --}}
<div class="corp-print-header">
    <div class="corp-logo-wrap">
        <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM Logo" class="corp-logo-img" style="height: 38px; width: auto; object-fit: contain;">
        <div>
            <div class="corp-name">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</div>
            <div class="corp-sub">DIVISI SERVICE MANAGEMENT &amp; OPERATION (SMO) — DIREKTORAT INFRASTRUKTUR &amp; TEKNOLOGI</div>
        </div>
    </div>
    <div class="doc-title-block">
        <div class="doc-main-title">REKAPITULASI DOKUMEN BASTO</div>
        <div class="doc-ref-number">Ref: PGN/SMO/BASTO/REKAP/{{ date('Ymd/His') }}</div>
    </div>
</div>

{{-- Print Parameter Strip --}}
<div class="meta-param-grid">
    <div class="meta-param-item">
        <strong>Filter Status BASTO</strong>
        <span>{{ !empty($filters['status']) ? ucfirst($filters['status']) : 'Semua Status' }}</span>
    </div>
    <div class="meta-param-item">
        <strong>Filter Mutu QC</strong>
        <span>{{ !empty($filters['qc_status']) ? ucfirst(str_replace('_', ' ', $filters['qc_status'])) : 'Semua Status QC' }}</span>
    </div>
    <div class="meta-param-item">
        <strong>Project ID</strong>
        <span>{{ !empty($filters['project_id']) ? $filters['project_id'] : 'Semua Project' }}</span>
    </div>
    @if(!empty($filters['tab']))
    <div class="meta-param-item">
        <strong>Kategori Tab Alur</strong>
        <span>{{ ucfirst(str_replace('_', ' ', $filters['tab'])) }}</span>
    </div>
    @endif
    @if(!empty($filters['search']))
    <div class="meta-param-item">
        <strong>Kata Kunci Pencarian</strong>
        <span>"{{ $filters['search'] }}"</span>
    </div>
    @endif
    <div class="meta-param-item">
        <strong>Dicetak Oleh / Waktu</strong>
        <span>{{ auth()->user()->name }} &bull; {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB</span>
    </div>
</div>

{{-- Formal KPI Summary Box (Print Only) --}}
<div class="print-summary-box">
    <div class="print-summary-cell">
        <div class="print-summary-label">Total BASTO</div>
        <div class="print-summary-value">{{ method_exists($bastos, 'total') ? $bastos->total() : count($bastos) }}</div>
        <div class="print-summary-sub">Dokumen Terdata</div>
    </div>
    <div class="print-summary-cell">
        <div class="print-summary-label">Draf DMO</div>
        <div class="print-summary-value">{{ $statusCounts['draft'] ?? 0 }}</div>
        <div class="print-summary-sub">Belum Diajukan</div>
    </div>
    <div class="print-summary-cell">
        <div class="print-summary-label">Menunggu Review</div>
        <div class="print-summary-value">{{ $statusCounts['submitted'] ?? 0 }}</div>
        <div class="print-summary-sub">Dalam Proses</div>
    </div>
    <div class="print-summary-cell">
        <div class="print-summary-label">Disetujui SM</div>
        <div class="print-summary-value">{{ $statusCounts['approved'] ?? 0 }}</div>
        <div class="print-summary-sub">Lolos Verifikasi</div>
    </div>
    <div class="print-summary-cell">
        <div class="print-summary-label">Ditolak / Revisi</div>
        <div class="print-summary-value">{{ ($statusCounts['rejected'] ?? 0) + ($dmoTabCounts['revision_needed'] ?? 0) }}</div>
        <div class="print-summary-sub">Perlu Perbaikan</div>
    </div>
</div>

{{-- Data Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden card-entrance">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4">
        <span class="fw-bold text-dark"><i class="bi bi-clipboard2-check me-2 text-primary"></i>Daftar Dokumen BASTO</span>
        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="cetakLaporanBasto()" class="btn btn-outline-secondary btn-sm fw-semibold rounded-3 px-3 py-2 shadow-xs d-print-none">
                <i class="bi bi-printer me-1"></i> Cetak Rekap BASTO
            </button>
            @if(auth()->user()->hasRole(['dmo', 'admin']))
            <a href="{{ route('basto.create') }}" class="btn btn-primary btn-sm fw-bold rounded-3 px-3.5 py-2 shadow-sm btn-action-animated d-print-none">
                <i class="bi bi-plus-lg me-1"></i> Buat Draf BASTO (Upload PDF)
            </a>
            @endif
        </div>
    </div>

    <div class="table-responsive" style="max-height: 720px; overflow-y: auto;">
        <table class="table table-hover table-sticky-header align-middle mb-0" style="font-size: 0.83rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold border-bottom" style="font-size: 0.75rem;">
                <tr>
                    @if(auth()->user()->hasRole(['osm_service_manager', 'dmo', 'admin']))
                        <th class="ps-3 pe-0 text-center d-print-none" style="width: 40px;">
                            <input type="checkbox" id="selectAllBasto" class="form-check-input" title="Pilih Semua">
                        </th>
                    @endif
                    <th class="py-3 ps-3 text-nowrap">
                        <span class="th-content" data-bs-toggle="tooltip" title="Berita Acara Serah Terima Operasional fisik">
                            Nomor BASTO <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-nowrap">
                        <span class="th-content" data-bs-toggle="tooltip" title="Kode proyek & lampiran berkas bukti fisik">
                            Project &amp; Dokumen <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-nowrap">
                        <span class="th-content" data-bs-toggle="tooltip" title="Staf DMO pembuat dan pengunggah berkas BASTO">
                            Pengaju (DMO) <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-nowrap">
                        <span class="th-content" data-bs-toggle="tooltip" title="Service Manager penanggung jawab approval BASTO">
                            Service Manager <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-center text-nowrap">
                        <span class="th-content justify-content-center" data-bs-toggle="tooltip" title="Hasil verifikasi mutu oleh Inspector QC (Lolos / Perlu Revisi)">
                            Status QC <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-center text-nowrap">
                        <span class="th-content justify-content-center" data-bs-toggle="tooltip" title="Alur persetujuan SM: DRAFT -> SUBMITTED -> APPROVED / REJECTED">
                            Status BASTO <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-nowrap">
                        <span class="th-content" data-bs-toggle="tooltip" title="Tanggal pengajuan dokumen & durasi penantian approval (Aging SLA)">
                            Tanggal &amp; SLA <i class="bi bi-info-circle"></i>
                        </span>
                    </th>
                    <th class="text-center pe-4 d-print-none text-nowrap" style="width:160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bastos as $index => $basto)
                <tr class="stagger-row" style="--row-delay: {{ $index * 0.03 }}s;">
                    @if(auth()->user()->hasRole(['osm_service_manager', 'dmo', 'admin']))
                        <td class="ps-3 pe-0 text-center d-print-none">
                            {{-- Checkbox Approve untuk SM / Admin --}}
                            @if($basto->status === 'submitted' && (auth()->user()->hasRole('osm_service_manager') || auth()->user()->hasRole('admin')) && ($basto->sm_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                                @if($basto->qc_status === 'verified' || auth()->user()->hasRole('admin'))
                                    <input type="checkbox" name="basto_ids[]" value="{{ $basto->id }}" form="batchApproveForm" class="form-check-input basto-checkbox basto-approve-cb" title="Pilih BASTO untuk Batch Approve">
                                @else
                                    <span class="d-inline-block" data-bs-toggle="tooltip" title="Terkunci: Belum lolos verifikasi QC">
                                        <input type="checkbox" class="form-check-input" disabled style="opacity: 0.35; cursor: not-allowed;">
                                    </span>
                                @endif
                            {{-- Checkbox Submit untuk DMO / Admin --}}
                            @elseif($basto->status === 'draft' && (auth()->user()->hasRole('dmo') || auth()->user()->hasRole('admin')) && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                                <input type="checkbox" name="basto_ids[]" value="{{ $basto->id }}" form="batchSubmitForm" class="form-check-input basto-checkbox basto-submit-cb" title="Pilih Draf BASTO untuk Diajukan ke SM">
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                    @endif
                    <td class="ps-3 fw-bold text-dark">
                        {{ $basto->basto_number }}
                    </td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="{{ $basto->project_name }}">{{ $basto->project_name }}</div>
                        <div class="small text-muted">{{ $basto->project_id }}</div>
                        @if($basto->attachment_file)
                            <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none mt-1 d-inline-flex align-items-center gap-1">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Berkas PDF Fisik
                            </a>
                        @endif
                    </td>
                    <td>{{ $basto->dmo ? $basto->dmo->name : '—' }}</td>
                    <td>{{ $basto->sm ? $basto->sm->name : '—' }}</td>
                    <td class="text-center">
                        @if($basto->qc_status === 'verified')
                            <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #dcfce7 !important; color: #166534 !important; border: 1px solid #86efac !important; font-size: 0.7rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> QC Verified
                            </span>
                        @elseif($basto->qc_status === 'revision_needed')
                            <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; font-size: 0.7rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Revisi QC
                            </span>
                        @else
                            <span class="badge px-2.5 py-1 rounded-pill fw-medium" style="background-color: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1 !important; font-size: 0.7rem;">
                                <i class="bi bi-hourglass-split me-1"></i> QC Pending
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $badgeConfig = [
                                'draft' => [
                                    'bg' => '#f1f5f9',
                                    'text' => '#334155',
                                    'border' => '#cbd5e1',
                                    'label' => 'DRAFT',
                                    'icon' => 'bi-file-earmark'
                                ],
                                'submitted' => [
                                    'bg' => '#dbeafe',
                                    'text' => '#1e40af',
                                    'border' => '#93c5fd',
                                    'label' => 'SUBMITTED',
                                    'icon' => 'bi-send-fill'
                                ],
                                'approved' => [
                                    'bg' => '#dcfce7',
                                    'text' => '#166534',
                                    'border' => '#86efac',
                                    'label' => 'APPROVED',
                                    'icon' => 'bi-check-circle-fill'
                                ],
                                'rejected' => [
                                    'bg' => '#fee2e2',
                                    'text' => '#991b1b',
                                    'border' => '#fca5a5',
                                    'label' => 'REJECTED',
                                    'icon' => 'bi-x-circle-fill'
                                ],
                            ];
                            $bCfg = $badgeConfig[$basto->status] ?? [
                                'bg' => '#f8fafc',
                                'text' => '#334155',
                                'border' => '#e2e8f0',
                                'label' => strtoupper($basto->status),
                                'icon' => 'bi-info-circle'
                            ];
                        @endphp
                        <span class="badge px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                              style="background-color: {{ $bCfg['bg'] }} !important; color: {{ $bCfg['text'] }} !important; border: 1px solid {{ $bCfg['border'] }} !important; font-size: 0.72rem; letter-spacing: 0.03em;">
                            <i class="bi {{ $bCfg['icon'] }} me-1"></i> {{ $bCfg['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $basto->submitted_at ? $basto->submitted_at->format('d/m/Y') : ($basto->created_at ? $basto->created_at->format('d/m/Y') : '—') }}</div>
                        @if($basto->status === 'submitted')
                            @php
                                $refDate = $basto->submitted_at ?? $basto->created_at;
                                $daysWaiting = $refDate ? (int) $refDate->diffInDays(now()) : 0;
                            @endphp
                            @if($daysWaiting <= 2)
                                <span class="badge sla-badge-fresh px-1.5 py-0.5 rounded-pill mt-0.5" style="font-size:0.62rem;">
                                    <i class="bi bi-clock me-0.5"></i>{{ $daysWaiting == 0 ? 'Hari ini' : $daysWaiting . ' hr lalu' }}
                                </span>
                            @elseif($daysWaiting <= 5)
                                <span class="badge sla-badge-warning px-1.5 py-0.5 rounded-pill mt-0.5" style="font-size:0.62rem;" title="Dokumen menunggu approval selama {{ $daysWaiting }} hari kerja">
                                    <i class="bi bi-hourglass-split me-0.5"></i>Tertahan {{ $daysWaiting }} hr
                                </span>
                            @else
                                <span class="badge sla-badge-danger px-1.5 py-0.5 rounded-pill mt-0.5" style="font-size:0.62rem;" title="SLA Terlewati: Tertahan lebih dari 5 hari!">
                                    <i class="bi bi-exclamation-triangle-fill me-0.5"></i>Kritis {{ $daysWaiting }} hr
                                </span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center pe-4 d-print-none">
                        <div class="d-flex gap-1 justify-content-center">
                            {{-- Quick Preview Modal Trigger --}}
                            <button type="button" class="btn btn-sm btn-light text-info rounded-3 px-2 py-1 btn-action-animated btn-quick-preview" 
                                    title="Pratinjau Cepat"
                                    data-basto-id="{{ $basto->id }}" 
                                    data-basto-num="{{ $basto->basto_number }}" 
                                    data-project-id="{{ $basto->project_id }}" 
                                    data-project-name="{{ $basto->project_name }}" 
                                    data-dmo="{{ $basto->dmo ? $basto->dmo->name : '—' }}" 
                                    data-sm="{{ $basto->sm ? $basto->sm->name : '—' }}" 
                                    data-pdf="{{ $basto->attachment_file ? route('basto.attachment', $basto->id) : '' }}" 
                                    data-status="{{ ucfirst($basto->status) }}" 
                                    data-date="{{ $basto->submitted_at ? $basto->submitted_at->format('d M Y') : ($basto->created_at ? $basto->created_at->format('d M Y') : '—') }}" 
                                    data-notes="{{ $basto->notes ?? 'Tidak ada catatan khusus.' }}" 
                                    data-can-approve="{{ ($basto->status === 'submitted' && ($basto->sm_user_id === auth()->id() || auth()->user()->hasRole('admin'))) ? '1' : '0' }}" 
                                    data-qc-status="{{ $basto->qc_status ?? 'pending' }}" 
                                    data-qc-code="{{ $basto->qc_verification_code ?? '' }}" 
                                    data-qc-notes="{{ $basto->qc_notes ?? '' }}" 
                                    data-qc-inspector="{{ $basto->qcUser ? $basto->qcUser->name : 'Tim QC' }}" 
                                    data-detail-url="{{ route('basto.show', $basto->id) }}" 
                                    data-approve-url="{{ route('basto.approve', $basto->id) }}" 
                                    data-reject-modal="#rejectModal{{ $basto->id }}">
                                <i class="bi bi-file-earmark-medical"></i>
                            </button>

                            <a href="{{ route('basto.show', $basto->id) }}" class="btn btn-sm btn-light text-primary rounded-3 px-2 py-1 btn-action-animated" title="Detail"><i class="bi bi-arrow-right-circle"></i></a>

                            {{-- QC Inspection button for OSM QC / Admin --}}
                            @if(auth()->user()->hasRole(['osm_qc', 'admin']))
                                <button class="btn btn-sm btn-light text-secondary rounded-3 px-2 py-1 btn-action-animated" title="Inspeksi QC Mutu" data-bs-toggle="modal" data-bs-target="#qcModal{{ $basto->id }}">
                                    <i class="bi bi-shield-check fs-6"></i>
                                </button>
                            @endif

                            @if(auth()->user()->hasRole('admin') || (auth()->user()->hasRole('dmo') && $basto->status === 'draft'))
                                @if($basto->status === 'draft')
                                    <a href="{{ route('basto.edit', $basto->id) }}" class="btn btn-sm btn-light text-warning rounded-3 px-2 py-1 btn-action-animated" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('basto.submit', $basto->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light text-success rounded-3 px-2 py-1 btn-action-animated" title="Ajukan ke SM" onclick="return confirm('Ajukan BASTO ini ke Service Manager?')"><i class="bi bi-send-fill"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('basto.destroy', $basto->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-3 px-2 py-1 btn-action-animated" title="Hapus BASTO" onclick="return confirm('Hapus BASTO ini secara permanen?')"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            @endif

                            {{-- DMO Tindak Lanjut Revisi QC --}}
                            @if(auth()->user()->hasRole(['dmo', 'admin']) && $basto->qc_status === 'revision_needed' && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-3 px-2.5 py-1 btn-action-animated shadow-sm" title="Unggah Berkas Revisi QC" data-bs-toggle="modal" data-bs-target="#reuploadModal{{ $basto->id }}">
                                    <i class="bi bi-arrow-repeat me-1"></i> Revisi
                                </button>
                            @endif

                            @if(auth()->user()->hasRole(['osm_service_manager', 'admin']) && $basto->status === 'submitted' && ($basto->sm_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                                @if($basto->qc_status === 'verified' || auth()->user()->hasRole('admin'))
                                    <button class="btn btn-sm btn-success rounded-3 px-2 py-1 btn-action-animated" title="Setujui BASTO (Lolos QC)" data-bs-toggle="modal" data-bs-target="#approveModal{{ $basto->id }}">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1 opacity-75" title="Terkunci: Belum lolos verifikasi QC" data-bs-toggle="modal" data-bs-target="#approveModal{{ $basto->id }}">
                                        <i class="bi bi-lock-fill text-warning"></i>
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-danger rounded-3 px-2 py-1 btn-action-animated" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $basto->id }}"><i class="bi bi-x-lg"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard2-x fs-2 d-block mb-2"></i>
                        Belum ada data BASTO.
                        @if(auth()->user()->hasRole('dmo'))
                            <a href="{{ route('basto.create') }}" class="d-block mt-2 text-decoration-none">Buat BASTO pertama →</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bastos->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3 d-print-none">
        {{ $bastos->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Lembar Pengesahan / Tanda Tangan Formal 3 Kolom (Print Only) --}}
<div class="signature-section">
    <div class="sig-box">
        <div class="sig-role">Dibuat &amp; Diajukan Oleh:</div>
        <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">Staf Administrasi Proyek / DMO</div>
        <div class="sig-space"></div>
        <div class="sig-name">( {{ auth()->user()->hasRole('dmo') ? auth()->user()->name : '..................................................' }} )</div>
        <div class="sig-nip">DMO Operations Staff</div>
    </div>
    <div class="sig-box">
        <div class="sig-role">Diverifikasi Kelayakan Mutu:</div>
        <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">Inspector Quality Control (QC)</div>
        <div class="sig-space"></div>
        <div class="sig-name">( {{ auth()->user()->hasRole('osm_qc') ? auth()->user()->name : '..................................................' }} )</div>
        <div class="sig-nip">Quality Control Officer</div>
    </div>
    <div class="sig-box">
        <div class="sig-role">Mengetahui &amp; Menyetujui:</div>
        <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">Service Manager / OSM Head</div>
        <div class="sig-space"></div>
        <div class="sig-name">( {{ auth()->user()->hasRole('osm_service_manager') ? auth()->user()->name : '..................................................' }} )</div>
        <div class="sig-nip">Operation &amp; Service Manager</div>
    </div>
</div>

{{-- Footer Dokumen Resmi Cetak --}}
<div class="corp-print-footer">
    <span>Sistem Manajemen Realisasi &amp; Monitoring BASTO &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)</span>
    <span>Dokumen Rahasia Perusahaan &bull; Halaman 1 / Rekapitulasi</span>
</div>

</div> {{-- /#printableReportArea --}}

@push('modals')
{{-- MODALS PLACED OUTSIDE CARD AND INSIDE @push('modals') TO PREVENT BACKDROP STACKING CONTEXT ISSUES --}}
@foreach($bastos as $basto)
    {{-- QC Inspection Modal --}}
    @if(auth()->user()->hasRole(['osm_qc', 'admin']))
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
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_{{ $basto->id }}" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_admin_{{ $basto->id }}">
                                                                    1. Kelengkapan Administrasi &amp; Ttd
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Kop surat, nomor BASTO, ttd basah/digital DMO, vendor &amp; tanggal sah.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_{{ $basto->id }}" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_spk_{{ $basto->id }}">
                                                                    2. Kesesuaian SPK &amp; Kontrak
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Nomor project, scope pekerjaan, dan jangka waktu sinkron dengan SPK.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_{{ $basto->id }}" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_baut_{{ $basto->id }}">
                                                                    3. BAUT &amp; Uji Fungsi Teknis
                                                                </label>
                                                                <div class="text-muted" style="font-size: 0.72rem;">Berita Acara Uji Terima teknis terpenuhi tanpa kendala operasional.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                            <div class="form-check">
                                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_{{ $basto->id }}" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_phys_{{ $basto->id }}">
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
                                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_{{ $basto->id }}" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                                            <label class="form-check-label fw-bold text-success small cursor-pointer mb-0" for="status_verified_{{ $basto->id }}">
                                                                <i class="bi bi-patch-check-fill me-1"></i> VERIFIED (Lolos)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_{{ $basto->id }}" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                                            <label class="form-check-label fw-bold text-danger small cursor-pointer mb-0" for="status_revision_{{ $basto->id }}">
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
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_{{ $basto->id }}" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_admin_{{ $basto->id }}">1. Administrasi &amp; Ttd</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_{{ $basto->id }}" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_spk_{{ $basto->id }}">2. Kesesuaian SPK &amp; Kontrak</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_{{ $basto->id }}" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_baut_{{ $basto->id }}">3. BAUT &amp; Uji Fungsi</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                                            <div class="form-check">
                                                <input class="form-check-input qc-chk-{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_{{ $basto->id }}" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark small" for="chk_phys_{{ $basto->id }}">4. Bukti Fisik Lapangan</label>
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
                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_{{ $basto->id }}" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold text-success small cursor-pointer" for="status_verified_{{ $basto->id }}">VERIFIED (Lolos)</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                            <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_{{ $basto->id }}" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold text-danger small cursor-pointer" for="status_revision_{{ $basto->id }}">REVISI (Kunci)</label>
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
    @endif

    {{-- Approve & Reject Modals --}}
    @if(auth()->user()->hasRole(['osm_service_manager', 'admin']) && $basto->status === 'submitted')
    <div class="modal fade" id="approveModal{{ $basto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Setujui BASTO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    {{-- FITUR 2: QC Gatekeeper Alert Warning --}}
                    @if($basto->qc_status === 'revision_needed')
                        <div class="alert alert-danger rounded-3 d-flex align-items-start gap-2 mb-3">
                            <i class="bi bi-shield-x fs-4 text-danger flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-danger">Persetujuan Diblokir (QC Gatekeeper)</strong>
                                <span class="small">Dokumen BASTO ini ditandai <b>Perlu Revisi Mutu</b> oleh tim QC. DMO harus memperbaiki dokumen dan mendapatkan verifikasi QC sebelum Service Manager dapat menyetujui.</span>
                                @if($basto->qc_notes)
                                    <div class="mt-2 p-2 bg-white rounded border border-danger border-opacity-25 small text-dark">
                                        <b>Temuan QC:</b> {{ $basto->qc_notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($basto->qc_status === 'verified')
                        <div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-patch-check-fill fs-4 text-success flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-success">Lolos Verifikasi QC Mutu</strong>
                                <span class="small">Sertifikat: <code>{{ $basto->qc_verification_code }}</code> • Diverifikasi oleh {{ $basto->qcUser ? $basto->qcUser->name : 'Tim QC' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning rounded-3 d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-shield-exclamation fs-4 text-warning flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-warning">Persetujuan Terkunci (QC Belum Verifikasi)</strong>
                                <span class="small">Dokumen BASTO ini belum diperiksa &amp; dinyatakan lolos oleh tim Quality Control. Harap tunggu verifikasi QC terlebih dahulu.</span>
                            </div>
                        </div>
                    @endif

                    <p>Anda akan menyetujui <strong>{{ $basto->basto_number }}</strong> untuk project <strong>{{ $basto->project_name }}</strong>.</p>
                    <p class="text-muted small mb-0">Aksi ini tidak dapat dibatalkan setelah disetujui.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('basto.approve', $basto->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success rounded-3 fw-bold px-4" {{ ($basto->qc_status !== 'verified' && !auth()->user()->hasRole('admin')) ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-1"></i> Setujui BASTO
                        </button>
                    </form>
                </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal{{ $basto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak BASTO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('basto.reject', $basto->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body pt-2">
                        <p>Masukkan alasan penolakan untuk <strong>{{ $basto->basto_number }}</strong>:</p>
                        <textarea name="rejection_reason" class="form-control rounded-3" rows="4" placeholder="Alasan penolakan..." required></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal" type="button">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 fw-bold px-4">Tolak BASTO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL UNGGAH REVISI BASTO (DMO ACTION) --}}
    @if(auth()->user()->hasRole(['dmo', 'admin']) && $basto->qc_status === 'revision_needed' && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
    <div class="modal fade" id="reuploadModal{{ $basto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <form action="{{ route('basto.reupload-revision', $basto->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                        <div class="text-white">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-white text-warning fw-bold px-2.5 py-1 rounded-pill">TINDAK LANJUT REVISI DMO</span>
                                <span class="fw-bold fs-6">{{ $basto->basto_number }}</span>
                            </div>
                            <div class="small text-white text-opacity-85">{{ $basto->project_name }} ({{ $basto->project_id }})</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- QC Finding Alert --}}
                        <div class="alert alert-danger rounded-3 mb-4 border border-danger border-opacity-25" style="background: #fef2f2;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-0.5"></i>
                                <div class="w-100">
                                    <div class="fw-bold text-danger mb-1">Catatan Hasil Pemeriksaan Mutu (QC):</div>
                                    <div class="text-dark small mb-2" style="white-space: pre-line;">{{ $basto->qc_notes ?: 'Dokumen fisik BASTO belum memenuhi standar atau terdapat ketidaksesuaian administrasi/teknis.' }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        Diperiksa oleh: <b>{{ $basto->qcUser ? $basto->qcUser->name : 'Tim Quality Control' }}</b> &bull; {{ $basto->qc_verified_at ? $basto->qc_verified_at->format('d/m/Y H:i') : 'Tanggal tidak tersedia' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Berkas Saat Ini --}}
                        @if($basto->attachment_file)
                        <div class="mb-3 p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                <div>
                                    <span class="text-muted d-block" style="font-size: 0.7rem;">Berkas Terunggah Saat Ini (Akan digantikan):</span>
                                    <span class="fw-bold text-dark font-monospace">{{ basename($basto->attachment_file) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-pill" style="font-size:0.75rem;">
                                <i class="bi bi-eye me-1"></i>Lihat Berkas Lama
                            </a>
                        </div>
                        @endif

                        {{-- Form Unggah Revisi --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-cloud-arrow-up-fill text-primary me-1"></i> Unggah Berkas Fisik Pengganti / Revisi <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="attachment_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                            <div class="form-text text-muted" style="font-size:0.75rem;">Format diperbolehkan: PDF, JPG, PNG, WebP. Ukuran maks: 10MB. Pastikan tanda tangan & cap basah terbaca jelas.</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-pencil-square text-secondary me-1"></i> Catatan Tindak Lanjut dari DMO (Opsional)
                            </label>
                            <textarea name="revision_notes" class="form-control" rows="3" placeholder="Jelaskan perbaikan yang telah dilakukan berdasarkan temuan QC..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 py-3 px-4 bg-light">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3 px-4 shadow-sm">
                            <i class="bi bi-send-check-fill me-1"></i> Simpan &amp; Kirim Ulang ke QC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

{{-- QUICK PREVIEW MODAL --}}
<div class="modal fade" id="quickPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold" id="qpBastoNum">BASTO</span>
                    <span class="badge bg-secondary px-2.5 py-1 rounded-pill" id="qpStatus">SUBMITTED</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <div class="small text-muted mb-1">Nama Project & ID</div>
                        <h6 class="fw-bold text-dark mb-0" id="qpProjectName">—</h6>
                        <span class="badge bg-light text-dark border mt-1" id="qpProjectId">—</span>
                    </div>
                    <div class="col-md-5">
                        <div class="small text-muted mb-1">Tanggal Pengajuan</div>
                        <div class="fw-semibold text-dark" id="qpDate">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Pengaju (DMO)</div>
                        <div class="fw-semibold text-dark" id="qpDmo">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Service Manager</div>
                        <div class="fw-semibold text-dark" id="qpSm">—</div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="small text-secondary fw-bold mb-1"><i class="bi bi-chat-left-text me-1"></i>Catatan Pengajuan DMO:</div>
                    <div class="small text-dark" id="qpNotes">—</div>
                </div>

                {{-- Status Mutu QC & Gatekeeper Warning in Quick Preview --}}
                <div id="qpQcContainer" class="p-3 rounded-3 mb-3 border">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="small fw-bold text-dark d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-check text-primary"></i> Status Pengawasan Mutu (QC):
                        </div>
                        <span id="qpQcBadge" class="badge px-2.5 py-1 rounded-pill fw-bold">—</span>
                    </div>
                    <div id="qpQcNotesBox" class="small mt-1" style="font-size: 0.8rem;">—</div>
                </div>

                <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                        <div>
                            <div class="fw-bold text-dark small">Berkas Fisik BASTO (PDF)</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Periksa dokumen berita acara yang telah ditandatangani</div>
                        </div>
                    </div>
                    <div id="qpPdfContainer">
                        <a href="#" target="_blank" id="qpPdfLink" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between">
                <a href="#" id="qpDetailBtn" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Buka Halaman Penuh
                </a>
                <div class="d-flex gap-2" id="qpActionButtons">
                    <form id="qpApproveForm" action="#" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm rounded-3 fw-bold px-3">
                            <i class="bi bi-check2 me-1"></i> Setujui BASTO
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllBasto');
    const checkboxes = document.querySelectorAll('.basto-checkbox');
    const batchBar = document.getElementById('batchActionBar');
    const countBadge = document.getElementById('selectedCountBadge');

    function updateBatchBar() {
        const checkedApprove = document.querySelectorAll('.basto-approve-cb:checked');
        const checkedSubmit = document.querySelectorAll('.basto-submit-cb:checked');
        
        if (batchBar) {
            if (checkedApprove.length > 0) {
                batchBar.classList.remove('d-none');
                countBadge.textContent = `${checkedApprove.length} BASTO Terpilih`;
            } else {
                batchBar.classList.add('d-none');
            }
        }

        const batchSubmitBar = document.getElementById('batchSubmitActionBar');
        const submitCountBadge = document.getElementById('selectedSubmitCountBadge');
        if (batchSubmitBar) {
            if (checkedSubmit.length > 0) {
                batchSubmitBar.classList.remove('d-none');
                submitCountBadge.textContent = `${checkedSubmit.length} Draf BASTO Terpilih`;
            } else {
                batchSubmitBar.classList.add('d-none');
            }
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBatchBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBatchBar();
            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && Array.from(checkboxes).every(c => c.checked);
            }
        });
    });

    // Quick Preview Modal Handler
    const previewModalEl = document.getElementById('quickPreviewModal');
    let quickModal = null;
    if (previewModalEl) {
        quickModal = new bootstrap.Modal(previewModalEl);
        document.querySelectorAll('.btn-quick-preview').forEach(btn => {
            btn.addEventListener('click', function() {
                const bastoNum = this.getAttribute('data-basto-num');
                const pId = this.getAttribute('data-project-id');
                const pName = this.getAttribute('data-project-name');
                const dmo = this.getAttribute('data-dmo');
                const sm = this.getAttribute('data-sm');
                const pdf = this.getAttribute('data-pdf');
                const status = this.getAttribute('data-status');
                const date = this.getAttribute('data-date');
                const notes = this.getAttribute('data-notes');
                const canApprove = this.getAttribute('data-can-approve') === '1';
                const detailUrl = this.getAttribute('data-detail-url');
                const approveUrl = this.getAttribute('data-approve-url');

                const qcStatus = this.getAttribute('data-qc-status') || 'pending';
                const qcCode = this.getAttribute('data-qc-code') || '';
                const qcNotes = this.getAttribute('data-qc-notes') || '';
                const qcInspector = this.getAttribute('data-qc-inspector') || '';

                document.getElementById('qpBastoNum').textContent = bastoNum;
                document.getElementById('qpStatus').textContent = status.toUpperCase();
                document.getElementById('qpProjectId').textContent = pId;
                document.getElementById('qpProjectName').textContent = pName;
                document.getElementById('qpDmo').textContent = dmo;
                document.getElementById('qpSm').textContent = sm;
                document.getElementById('qpDate').textContent = date;
                document.getElementById('qpNotes').textContent = notes;
                document.getElementById('qpDetailBtn').href = detailUrl;

                // Handle QC Status & Gatekeeper in Quick Preview
                const qcContainer = document.getElementById('qpQcContainer');
                const qcBadge = document.getElementById('qpQcBadge');
                const qcNotesBox = document.getElementById('qpQcNotesBox');

                if (qcStatus === 'verified') {
                    qcContainer.style.background = '#f0fdf4';
                    qcContainer.style.borderColor = '#86efac';
                    qcBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold';
                    qcBadge.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> VERIFIED ' + (qcCode ? '(' + qcCode + ')' : '');
                    qcNotesBox.className = 'small text-success mt-1';
                    qcNotesBox.innerHTML = 'Diverifikasi oleh <b>' + (qcInspector || 'Tim QC') + '</b>' + (qcNotes ? ': ' + qcNotes : '');
                } else if (qcStatus === 'revision_needed') {
                    qcContainer.style.background = '#fef2f2';
                    qcContainer.style.borderColor = '#fca5a5';
                    qcBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold';
                    qcBadge.innerHTML = '<i class="bi bi-shield-x me-1"></i> PERLU REVISI QC';
                    qcNotesBox.className = 'small text-danger mt-1';
                    qcNotesBox.innerHTML = '<strong>Persetujuan Diblokir (QC Gatekeeper):</strong> ' + (qcNotes ? qcNotes : 'DMO wajib melengkapi revisi berkas.');
                } else {
                    qcContainer.style.background = '#f8fafc';
                    qcContainer.style.borderColor = '#e2e8f0';
                    qcBadge.className = 'badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-pill fw-medium';
                    qcBadge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> QC Pending';
                    qcNotesBox.className = 'small text-muted mt-1';
                    qcNotesBox.innerHTML = 'Dokumen belum melalui pemeriksaan mutu teknis oleh tim QC.';
                }

                const pdfLink = document.getElementById('qpPdfLink');
                if (pdf && pdf.length > 0) {
                    pdfLink.href = pdf;
                    pdfLink.classList.remove('disabled');
                    pdfLink.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i> Buka PDF';
                } else {
                    pdfLink.removeAttribute('href');
                    pdfLink.classList.add('disabled');
                    pdfLink.innerHTML = '<i class="bi bi-slash-circle me-1"></i> Tanpa Berkas PDF';
                }

                const approveForm = document.getElementById('qpApproveForm');
                // Gatekeeper: If revision_needed, do not show approve button in preview!
                if (canApprove && qcStatus !== 'revision_needed') {
                    approveForm.action = approveUrl;
                    approveForm.classList.remove('d-none');
                } else {
                    approveForm.classList.add('d-none');
                }

                quickModal.show();
            });
        });
    }
});

// FITUR 1: Centang semua checklist QC
function checkAllQcItems(id) {
    document.querySelectorAll(`.qc-chk-${id}`).forEach(cb => cb.checked = true);
    const verifiedRadio = document.getElementById(`status_verified_${id}`);
    if (verifiedRadio) verifiedRadio.checked = true;
}

// FITUR 4: Tambahkan template temuan cepat ke textarea catatan QC
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

function confirmBatchApprove() {
    const checked = document.querySelectorAll('.basto-approve-cb:checked');
    if (checked.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Dokumen',
            text: 'Silakan pilih minimal satu BASTO terlebih dahulu untuk disetujui.'
        });
        return;
    }

    Swal.fire({
        title: 'Setujui BASTO Terpilih?',
        text: `Anda akan menyetujui ${checked.length} dokumen BASTO secara bersamaan. Aksi ini tidak dapat dibatalkan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-check2-all me-1"></i> Ya, Setujui Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('batchApproveForm').submit();
        }
    });
}

function confirmBatchSubmit() {
    const checked = document.querySelectorAll('.basto-submit-cb:checked');
    if (checked.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Draf BASTO',
            text: 'Silakan pilih minimal satu draf BASTO terlebih dahulu untuk diajukan.'
        });
        return;
    }

    Swal.fire({
        title: 'Ajukan Draf BASTO Terpilih?',
        text: `Anda akan mengajukan ${checked.length} dokumen BASTO sekaligus ke Service Manager penanggung jawab masing-masing.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d9488',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-send-fill me-1"></i> Ya, Ajukan Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('batchSubmitForm').submit();
        }
    });
}

function cetakLaporanBasto() {
    const form = document.getElementById('bastoFilterForm');
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
