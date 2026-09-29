@extends('layouts.main')

@section('title', 'Data Realisasi')
@section('page-title', 'Data Realisasi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Data Realisasi</li>
@endsection

@push('styles')
<style>
    .filter-card {
        background: #fff;
        border: 1px solid #F1F5F9;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .filter-card .filter-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .filter-card .form-select,
    .filter-card .form-control {
        font-size: 0.8rem;
        border-color: #E2E8F0;
        border-radius: 6px;
        height: 34px;
        padding-top: 0;
        padding-bottom: 0;
        color: #334155;
    }

    .data-table th {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748B;
        background: #F8FAFC;
        border-bottom: 2px solid #E2E8F0 !important;
        padding: 10px 12px;
        white-space: nowrap;
    }
    .data-table td {
        font-size: 0.82rem;
        padding: 9px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9 !important;
        color: #334155;
    }
    .data-table tbody tr:hover { background: #F8FAFC; }
    .data-table tbody tr:last-child td { border-bottom: none !important; }

    .badge-paid         { background: #D1FAE5; color: #065F46; }
    .badge-unpaid       { background: #FEE2E2; color: #991B1B; }
    .badge-pending      { background: #FEF3C7; color: #92400E; }
    .badge-cancel       { background: #F1F5F9; color: #475569; }
    .badge-proses       { background: #DBEAFE; color: #1E40AF; }
    .badge-popay        { background: #FDF4FF; color: #701A75; }
    .badge-in-progres   { background: #FFF7ED; color: #9A3412; }
    .badge-wait-inv     { background: #ECFEFF; color: #083344; }
    .badge-default      { background: #F1F5F9; color: #475569; }

    .action-btn {
        width: 28px; height: 28px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px;
        font-size: 0.8rem;
        border: 1px solid transparent;
        transition: all 0.15s;
        text-decoration: none;
    }
    .action-btn-view  { color: #0284C7; background: #EFF6FF; border-color: #BFDBFE; }
    .action-btn-view:hover  { background: #DBEAFE; color: #1D4ED8; }
    .action-btn-edit  { color: #D97706; background: #FFFBEB; border-color: #FDE68A; }
    .action-btn-edit:hover  { background: #FEF3C7; color: #B45309; }
    .action-btn-delete { color: #DC2626; background: #FEF2F2; border-color: #FECACA; }
    .action-btn-delete:hover { background: #FEE2E2; color: #991B1B; }

    .number-cell { font-variant-numeric: tabular-nums; font-weight: 600; color: #0F172A; text-align: right; white-space: nowrap; }
    .project-id-link { font-family: 'Courier New', monospace; font-size: 0.78rem; font-weight: 700; color: #1D4ED8; }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')

{{-- Top Action Bar --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span style="font-size:0.82rem; color:#64748B;">
            Menampilkan <strong class="text-dark">{{ $realisasi->firstItem() ?? 0 }}–{{ $realisasi->lastItem() ?? 0 }}</strong>
            dari <strong class="text-dark">{{ number_format($totalCount) }}</strong> transaksi
        </span>
        @if($totalIDR > 0)
            <span class="d-none d-sm-inline text-muted">•</span>
            <span style="font-size:0.82rem;">
                Total: <strong class="text-primary">Rp {{ number_format($totalIDR, 0, ',', '.') }}</strong>
            </span>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap">
        {{-- Table Density Toggle --}}
        <button type="button" class="btn btn-sm btn-light border btn-density-toggle fw-semibold shadow-xs" 
                style="border-radius:7px; background:#FFFFFF; color:#475569;" 
                onclick="toggleTableDensity('.data-table')" 
                title="Beralih antara tampilan baris rapat (compact) atau luas (comfortable)">
            <i class="bi bi-arrows-collapse me-1"></i>Tampilan Rapat
        </button>

        @if(auth()->user()->hasRole(['dmo', 'admin']))
            <a href="{{ route('realisasi.create') }}" class="btn btn-primary btn-sm fw-semibold" style="border-radius:7px;">
                <i class="bi bi-plus-lg me-1"></i>Tambah Data
            </a>
        @endif
        @if(auth()->user()->hasRole(['dmo', 'admin']))
            <a href="{{ route('realisasi.import.form') }}" class="btn btn-sm fw-semibold" style="border-radius:7px; background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0;">
                <i class="bi bi-upload me-1"></i>Import Excel
            </a>
        @endif
        <a href="{{ route('realisasi.export') }}?{{ http_build_query($filters) }}" class="btn btn-sm fw-semibold" style="border-radius:7px; background:#F8FAFC; color:#475569; border:1px solid #E2E8F0;">
            <i class="bi bi-download me-1"></i>Export
        </a>
    </div>
</div>

{{-- Quick Filter Pills (Ringkas & Paling Sering Digunakan) --}}
<div class="quick-filter-pills mb-2">
    <span class="text-secondary small fw-bold text-uppercase me-1" style="font-size:0.68rem; letter-spacing:0.06em;">
        <i class="bi bi-lightning-charge-fill text-warning me-0.5"></i>Pintas:
    </span>
    <a href="{{ route('realisasi.index') }}" 
       class="filter-pill {{ empty(array_filter($filters)) ? 'active' : '' }}">
        Semua Transaksi
    </a>
    <a href="{{ route('realisasi.index', ['status' => 'PAID']) }}" 
       class="filter-pill {{ ($filters['status'] ?? '') === 'PAID' ? 'active' : '' }}">
        <span class="badge rounded-circle p-1 bg-success me-1" style="width:6px; height:6px;"></span> Lunas (PAID)
    </a>
    <a href="{{ route('realisasi.index', ['status' => 'POPAY']) }}" 
       class="filter-pill {{ ($filters['status'] ?? '') === 'POPAY' ? 'active' : '' }}">
        <span class="badge rounded-circle p-1 bg-purple me-1" style="background:#8B5CF6; width:6px; height:6px;"></span> POPAY
    </a>
    <a href="{{ route('realisasi.index', ['basto_status' => 'no_basto']) }}" 
       class="filter-pill {{ ($filters['basto_status'] ?? '') === 'no_basto' ? 'active' : '' }}">
        <i class="bi bi-exclamation-circle text-warning"></i> Belum Ada BASTO
    </a>
    <a href="{{ route('realisasi.index', ['basto_status' => 'has_basto']) }}" 
       class="filter-pill {{ ($filters['basto_status'] ?? '') === 'has_basto' ? 'active' : '' }}">
        <i class="bi bi-patch-check-fill text-success"></i> Sudah Ada BASTO
    </a>
</div>

{{-- Filter Panel (Desain Simpel 1-Baris & Ramping) --}}
@php
    $hasAdvancedFilters = !empty($filters['project_id']) || !empty($filters['pic']) || !empty($filters['vendor']) || !empty($filters['tahun']) || !empty($filters['item_biaya']);
@endphp
<div class="filter-card py-2.5 px-3 mb-3">
    <form method="GET" action="{{ route('realisasi.index') }}" id="filterForm">
        {{-- Baris Utama (Hanya Filter Penting) --}}
        <div class="row g-2 align-items-center">
            {{-- 1. Pencarian Cepat Semua Kata Kunci --}}
            <div class="col-lg-4 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Cari project, vendor, PIC, item biaya..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>

            {{-- 2. Periode Bulan --}}
            <div class="col-lg-2 col-md-3 col-sm-6">
                <select name="periode" class="form-select form-select-sm">
                    <option value="">Semua Periode Bulan</option>
                    @foreach(['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $bln)
                        <option value="{{ $bln }}" {{ ($filters['periode'] ?? '') === $bln ? 'selected' : '' }}>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Status --}}
            <div class="col-lg-2 col-md-2 col-sm-6">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Service Manager --}}
            <div class="col-lg-2 col-md-2 col-sm-6">
                <select name="service_manager" class="form-select form-select-sm">
                    <option value="">Semua SM</option>
                    @foreach($smList as $sm)
                        <option value="{{ $sm }}" {{ ($filters['service_manager'] ?? '') === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 5. Tombol Aksi --}}
            <div class="col-lg-2 col-md-12 d-flex align-items-center gap-1.5">
                <button type="submit" class="btn btn-primary btn-sm fw-bold flex-fill rounded-2 d-flex align-items-center justify-content-center gap-1" style="height:34px;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <button type="button" class="btn btn-sm rounded-2 px-2.5 border {{ $hasAdvancedFilters ? 'btn-primary-subtle text-primary border-primary' : 'btn-light text-secondary' }}" 
                        data-bs-toggle="collapse" data-bs-target="#advancedFilterCollapse" 
                        aria-expanded="{{ $hasAdvancedFilters ? 'true' : 'false' }}" 
                        style="height:34px;"
                        title="Filter Lanjutan (Project ID, Vendor, PIC, Tahun)">
                    <i class="bi bi-sliders"></i>
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('realisasi.index') }}" class="btn btn-light border btn-sm rounded-2 text-danger px-2.5 d-flex align-items-center justify-content-center" 
                       style="height:34px;" title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Baris Tambahan (Tersembunyi, Hanya Terbuka Jika Dibutuhkan) --}}
        <div class="collapse {{ $hasAdvancedFilters ? 'show' : '' }} mt-2.5 pt-2.5 border-top" id="advancedFilterCollapse">
            <div class="row g-2 align-items-center">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">Semua Project ID</option>
                        @foreach($projectIds as $pid)
                            <option value="{{ $pid }}" {{ ($filters['project_id'] ?? '') === $pid ? 'selected' : '' }}>{{ $pid }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <select name="vendor" class="form-select form-select-sm">
                        <option value="">Semua Vendor</option>
                        @foreach($vendorList as $v)
                            <option value="{{ $v }}" {{ ($filters['vendor'] ?? '') === $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select name="pic" class="form-select form-select-sm">
                        <option value="">Semua PIC</option>
                        @foreach($picList as $pic)
                            <option value="{{ $pic }}" {{ ($filters['pic'] ?? '') === $pic ? 'selected' : '' }}>{{ $pic }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ ($filters['tahun'] ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select name="basto_status" class="form-select form-select-sm">
                        <option value="">Status Dokumen BASTO</option>
                        <option value="has_basto" {{ ($filters['basto_status'] ?? '') === 'has_basto' ? 'selected' : '' }}>Sudah Ada BASTO</option>
                        <option value="no_basto" {{ ($filters['basto_status'] ?? '') === 'no_basto' ? 'selected' : '' }}>Belum Ada BASTO</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 720px; overflow-y: auto;">
            <table class="table data-table table-sticky-header mb-0">
                <thead>
                    <tr>
                        <th style="width:38px;">#</th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Kode unik registrasi proyek (cth: PS-024-00)">
                                Project ID <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap" style="min-width:180px;">Project Name</th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Service Manager penanggung jawab portofolio proyek">
                                Service Manager <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Rincian pos pengeluaran (Upah, Sewa, Sparepart, dll.)">
                                Item Biaya <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Satuan Kerja pemilik anggaran (cth: DMO, OSM)">
                                Satuan Kerja <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">PIC</th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Bulan pencatatan transaksi realisasi biaya">
                                Periode <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">Tahun</th>
                        <th class="text-end text-nowrap">
                            <span class="th-content justify-content-end" data-bs-toggle="tooltip" title="Nilai pengeluaran aktual yang telah dikonversi ke Rupiah">
                                Realisasi Final (Rp) <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">
                            <span class="th-content" data-bs-toggle="tooltip" title="Status penagihan: PAID (Lunas), POPAY (PO Payment), PENDING, dll.">
                                Status <i class="bi bi-info-circle"></i>
                            </span>
                        </th>
                        <th class="text-nowrap">Vendor</th>
                        <th class="text-nowrap" style="width:95px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($realisasi as $idx => $r)
                    <tr>
                        <td class="text-muted" style="font-size:0.75rem;">{{ $realisasi->firstItem() + $idx }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <a href="{{ route('realisasi.show', $r->id) }}" class="project-id-link text-decoration-none fw-bold">
                                    {{ $r->project_id }}
                                </a>

                                {{-- Indikator Status BASTO Proyek --}}
                                @if(in_array($r->project_id, $projectsWithBasto ?? []))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.6rem;" title="Dokumen BASTO fisik telah terbit untuk proyek ini">
                                        <i class="bi bi-patch-check-fill me-0.5"></i> BASTO
                                    </span>
                                @else
                                    <a href="{{ route('basto.create', ['project_id' => $r->project_id]) }}" class="badge bg-warning-subtle text-warning border border-warning-subtle px-1.5 py-0.5 rounded-pill text-decoration-none" style="font-size:0.6rem;" title="Belum ada berkas BASTO. Klik untuk buat BASTO!">
                                        <i class="bi bi-plus-circle me-0.5"></i> + BASTO
                                    </a>
                                @endif

                                @php
                                    $pVal = $r->kontrak->project_value ?? 0;
                                    $pReal = $projectTotals[$r->project_id] ?? 0;
                                @endphp
                                @if($pVal > 0)
                                    @if($pReal > $pVal)
                                        <span class="badge bg-danger rounded-pill px-1.5 py-0.5" title="Overbudget: Total realisasi (Rp {{ number_format($pReal, 0, ',', '.') }}) melebihi nilai kontrak (Rp {{ number_format($pVal, 0, ',', '.') }})" style="font-size:0.6rem; cursor:help;">
                                            <i class="bi bi-exclamation-octagon-fill"></i> Over
                                        </span>
                                    @elseif($pReal >= 0.9 * $pVal)
                                        <span class="badge bg-warning text-dark rounded-pill px-1.5 py-0.5" title="Mendekati limit kontrak: {{ round($pReal / $pVal * 100) }}%" style="font-size:0.6rem; cursor:help;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> &ge;90%
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td style="max-width:200px;">
                            <span class="text-truncate-2" title="{{ $r->project_name }}" style="font-size:0.8rem;">
                                {{ $r->project_name }}
                            </span>
                        </td>
                        <td>
                            @if($r->kontrak && $r->kontrak->service_manager)
                                <span class="badge rounded-pill" style="background:#E0F2FE;color:#0369A1;font-size:0.68rem;font-weight:600;">
                                    {{ $r->kontrak->service_manager }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;">{{ $r->item_biaya ?? '—' }}</td>
                        <td style="font-size:0.78rem;">{{ $r->satuan_kerja ?? '—' }}</td>
                        <td>
                            @if($r->pic)
                                <span class="badge rounded-pill" style="background:#EEF2FF;color:#4338CA;font-size:0.68rem;font-weight:600;">
                                    {{ $r->pic }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#F1F5F9;color:#475569;font-size:0.68rem;font-weight:600;border:1px solid #E2E8F0;">
                                {{ $r->periode ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:0.78rem; font-weight:600;">{{ $r->tahun ?? '—' }}</td>
                        <td class="number-cell">
                            @if($r->realisasi_biaya_final)
                                <div class="fw-bold">{{ number_format($r->realisasi_biaya_final, 0, ',', '.') }}</div>
                                @if($r->currency && $r->currency !== 'IDR' && $r->realisasi_biaya_original)
                                    <div class="mt-0.5">
                                        <span class="badge" style="background:#FEF3C7; color:#B45309; font-size:0.65rem; font-weight:700; border:1px solid #FDE68A;">
                                            {{ $r->currency }} {{ number_format($r->realisasi_biaya_original, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted fw-normal">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $sc = match(strtoupper($r->status ?? '')) {
                                    'PAID'        => 'badge-paid',
                                    'UNPAID'      => 'badge-unpaid',
                                    'PENDING'     => 'badge-pending',
                                    'CANCEL'      => 'badge-cancel',
                                    'PROSES'      => 'badge-proses',
                                    'POPAY'       => 'badge-popay',
                                    'IN PROGRES'  => 'badge-in-progres',
                                    'WAIT INV'    => 'badge-wait-inv',
                                    default       => 'badge-default',
                                };
                            @endphp
                            <span class="badge rounded-pill px-2 {{ $sc }}" style="font-size:0.65rem;font-weight:700;">
                                {{ $r->status ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:0.78rem; max-width:120px;">
                            <span class="text-truncate d-block" title="{{ $r->vendor }}">{{ $r->vendor ?? '—' }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('realisasi.show', $r->id) }}" class="action-btn action-btn-view" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if(auth()->user()->hasRole(['dmo', 'admin']))
                                    <a href="{{ route('realisasi.edit', $r->id) }}" class="action-btn action-btn-edit" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form id="del-{{ $r->id }}" action="{{ route('realisasi.destroy', $r->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="button" class="action-btn action-btn-delete" title="Hapus"
                                                onclick="confirmDelete('del-{{ $r->id }}', '{{ addslashes($r->project_id) }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border p-3 mb-3 shadow-xs" style="width:68px; height:68px;">
                                <i class="bi bi-file-earmark-x text-secondary" style="font-size:2rem; opacity:0.6;"></i>
                            </div>
                            <div class="text-dark fw-bold fs-7 mb-1">Tidak Ada Transaksi yang Cocok</div>
                            <div class="text-muted small mb-3" style="max-width:320px; margin:0 auto; font-size:0.8rem;">
                                Tidak ditemukan data realisasi dengan kriteria filter saat ini. Silakan atur ulang filter pencarian Anda.
                            </div>
                            @if(array_filter($filters))
                                <a href="{{ route('realisasi.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua Filter
                                </a>
                            @else
                                @if(auth()->user()->hasRole(['dmo', 'admin']))
                                    <a href="{{ route('realisasi.create') }}" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Transaksi Pertama
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($realisasi->hasPages())
    <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between px-4 py-2" style="border-color:#F1F5F9 !important;">
        <div style="font-size:0.78rem; color:#64748B;">
            Halaman <strong class="text-dark">{{ $realisasi->currentPage() }}</strong> dari <strong class="text-dark">{{ $realisasi->lastPage() }}</strong>
        </div>
        {{ $realisasi->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
