@extends('layouts.main')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard User Management')

@section('breadcrumb')
    <li class="breadcrumb-item active"><i class="bi bi-speedometer2 me-1"></i>Dashboard Admin</li>
@endsection

@section('content')
<!-- Hero Executive Banner -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" 
     style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0284c7 100%);">
    <div class="card-body p-4 p-lg-5 text-white position-relative">
        <div class="position-absolute end-0 bottom-0 opacity-05 pe-5 pb-0 d-none d-xl-block pointer-events-none" style="opacity: 0.06;">
            <i class="bi bi-shield-lock-fill" style="font-size: 14rem; line-height: 0;"></i>
        </div>
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" 
                     style="background: rgba(255, 255, 255, 0.15); font-size: 0.78rem; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.25); color: #ffffff;">
                    <span class="badge bg-success rounded-circle p-1" style="width:7px; height:7px;"></span>
                    Enterprise Control Center &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)
                </div>
                <h2 class="fw-bold text-white mb-2" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h2>
                <p class="text-white text-opacity-90 mb-0 small" style="max-width: 600px; line-height: 1.6;">
                    Anda berada di Dashboard Administrator. Pantau statistik distribusi pengguna, kelola akun akses sistem, dan pantau log keamanan aktivitas secara terpusat.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2.5">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-md rounded-3 fw-bold px-3.5 py-2.5 shadow-sm" style="background: #0284c7; border: none; color: #ffffff;">
                        <i class="bi bi-person-plus-fill me-1.5"></i>Tambah Akun
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-md rounded-3 fw-bold px-3.5 py-2.5 text-dark shadow-sm">
                        <i class="bi bi-people-fill me-1 text-primary"></i>Kelola Akun
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Metric Cards (4 Cards Grid) -->
<div class="row g-3 mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: #ffffff; border-left: 4px solid #0284c7 !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary text-uppercase fw-bold fs-7 letter-spacing-05">Total Akun</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1" style="font-size: 2.1rem; font-variant-numeric: tabular-nums;">
                            {{ $stats['total_user'] }}
                        </h2>
                    </div>
                    <div class="rounded-3 p-2.5" style="background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1.5 text-secondary small">
                    <i class="bi bi-check-circle-fill text-success fs-7"></i>
                    <span>Semua akun terdaftar aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Users -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: #ffffff; border-left: 4px solid #0f172a !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary text-uppercase fw-bold fs-7 letter-spacing-05">Administrator</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1" style="font-size: 2.1rem; font-variant-numeric: tabular-nums;">
                            {{ $stats['admin'] }}
                        </h2>
                    </div>
                    <div class="rounded-3 p-2.5" style="background: #f1f5f9; color: #0f172a;">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1.5 text-secondary small">
                    <i class="bi bi-shield-fill-check text-primary fs-7"></i>
                    <span>Akses penuh sistem</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Roles -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary text-uppercase fw-bold fs-7 letter-spacing-05">Role Operasional</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1" style="font-size: 2.1rem; font-variant-numeric: tabular-nums;">
                            {{ $stats['total_user'] - $stats['admin'] }}
                        </h2>
                    </div>
                    <div class="rounded-3 p-2.5" style="background: #dcfce7; color: #10b981;">
                        <i class="bi bi-hdd-network-fill fs-3"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1.5 text-secondary small">
                    <i class="bi bi-diagram-3-fill text-info fs-7"></i>
                    <span>DMO, SM, QC, Procurement</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log Quick Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: #ffffff; border-left: 4px solid #e11d48 !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary text-uppercase fw-bold fs-7 letter-spacing-05">Security Log</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.25rem;">
                            Audit Log
                        </h2>
                    </div>
                    <div class="rounded-3 p-2.5" style="background: #ffe4e6; color: #e11d48;">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                </div>
                <a href="{{ route('audit.log') }}" class="btn btn-outline-danger btn-sm rounded-3 w-100 fw-bold mt-1">
                    <i class="bi bi-eye me-1"></i>Buka Audit Log
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: User Distribution & Quick Actions -->
<div class="row g-3">
    <!-- User Distribution by Role -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi Role Akses</h5>
                    <p class="text-muted small mb-0">Persentase dan jumlah akun berdasarkan role pekerjaan</p>
                </div>
                <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill small fw-semibold">
                    {{ $stats['total_user'] }} User Total
                </span>
            </div>
            <div class="card-body px-4 py-4">
                <div class="d-flex flex-column gap-3.5">
                    @php
                        $rolesList = [
                            ['key' => 'admin', 'label' => 'Administrator', 'color' => 'bg-dark', 'icon' => 'bi-shield-lock-fill', 'textColor' => '#0f172a'],
                            ['key' => 'procurement', 'label' => 'Procurement (Pengadaan)', 'color' => 'bg-indigo', 'icon' => 'bi-cart-check-fill', 'textColor' => '#4f46e5'],
                            ['key' => 'osm_service_manager', 'label' => 'OSM - Service Manager', 'color' => 'bg-primary', 'icon' => 'bi-person-badge-fill', 'textColor' => '#0284c7'],
                            ['key' => 'osm_qc', 'label' => 'OSM - Quality Control (QC)', 'color' => 'bg-info', 'icon' => 'bi-check-square-fill', 'textColor' => '#0284c7'],
                            ['key' => 'dmo', 'label' => 'DMO (Data Management Officer)', 'color' => 'bg-warning', 'icon' => 'bi-folder-fill', 'textColor' => '#d97706'],
                        ];
                    @endphp

                    @foreach($rolesList as $r)
                        @php
                            $count = $stats[$r['key']] ?? 0;
                            $pct = $stats['total_user'] > 0 ? round(($count / $stats['total_user']) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="fw-bold text-dark small d-flex align-items-center gap-2">
                                    <i class="bi {{ $r['icon'] }}" style="color: {{ $r['textColor'] }};"></i>
                                    {{ $r['label'] }}
                                </span>
                                <span class="small">
                                    <strong class="text-dark">{{ $count }}</strong> <span class="text-muted">({{ $pct }}%)</span>
                                </span>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 10px;">
                                <div class="progress-bar {{ $r['color'] }} rounded-pill" role="progressbar" 
                                     style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Hub -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="card-title mb-1 fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat Admin</h5>
                <p class="text-muted small mb-0">Jalan pintas ke fitur manajemen utama</p>
            </div>
            <div class="card-body px-4 py-4 d-flex flex-column gap-3">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-lg rounded-3 py-3 px-4 d-flex align-items-center justify-content-between shadow-sm text-start"
                   style="background: linear-gradient(135deg, #0284c7, #1e3a8a); border: none;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center text-white" 
                             style="width:42px; height:42px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-person-plus-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white">Tambah Akun Baru</div>
                            <div class="small text-white text-opacity-80">Registrasi akun pengguna sistem baru</div>
                        </div>
                    </div>
                    <i class="bi bi-arrow-right fs-4 text-white"></i>
                </a>

                <a href="{{ route('admin.users.index') }}" class="btn btn-light border btn-lg rounded-3 py-3 px-4 d-flex align-items-center justify-content-between text-start" style="background: #ffffff; color: #0f172a;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center" 
                             style="width:42px; height:42px; background: #f1f5f9; color: #0f172a;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Kelola Pengguna</div>
                            <div class="small text-secondary">Lihat, ubah role, & hapus daftar pengguna</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('audit.log') }}" class="btn btn-light border btn-lg rounded-3 py-3 px-4 d-flex align-items-center justify-content-between text-start" style="background: #ffffff; color: #0f172a;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center" 
                             style="width:42px; height:42px; background: #e0f2fe; color: #0284c7;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Audit Log Sistem</div>
                            <div class="small text-secondary">Riwayat log kegiatan operasional pengguna</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('realisasi.index') }}" class="btn btn-light border btn-lg rounded-3 py-3 px-4 d-flex align-items-center justify-content-between text-start" style="background: #ffffff; color: #0f172a;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center" 
                             style="width:42px; height:42px; background: #dcfce7; color: #10b981;">
                            <i class="bi bi-table fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Data Realisasi</div>
                            <div class="small text-secondary">Akses dashboard monitoring data realisasi</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
