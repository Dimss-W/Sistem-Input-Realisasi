@extends('layouts.main')

@section('title', 'Kunci Periode & Tutup Buku')
@section('page-title', 'Kontrol Kunci Periode & Tutup Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active"><i class="bi bi-lock-fill me-1"></i>Kunci Periode</li>
@endsection

@push('styles')
<style>
    .lock-card {
        border-radius: 14px;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .lock-card:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
    }
    .lock-header-locked {
        background: #FEF2F2;
        border-bottom: 2px solid #F87171;
        color: #991B1B;
    }
    .lock-header-open {
        background: #F0FDF4;
        border-bottom: 2px solid #4ADE80;
        color: #166534;
    }
    .badge-locked {
        background: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FECACA;
    }
    .badge-open {
        background: #DCFCE7;
        color: #166534;
        border: 1px solid #BBF7D0;
    }
</style>
@endpush

@section('content')

{{-- Year Selector & Summary Bar --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0A192F 0%, #1E3A8A 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="bi bi-shield-lock-fill text-warning"></i> Kontrol Integritas Finansial
                </div>
                <h4 class="fw-bold text-white mb-1">Manajemen Kunci Periode (*Period Locking*)</h4>
                <p class="text-white text-opacity-80 mb-0 small" style="max-width: 600px;">
                    Kunci periode transaksi (Kuartal, Semester, atau Bulan) setelah proses tutup buku/audit selesai. Transaksi pada periode terkunci tidak dapat ditambah, diedit, atau dihapus oleh non-admin.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <form action="{{ route('admin.period-locks.index') }}" method="GET" class="d-inline-flex align-items-center gap-2">
                    <label class="text-white text-opacity-90 small fw-bold text-nowrap">Tahun Anggaran:</label>
                    <select name="tahun" class="form-select form-select-sm fw-bold rounded-3" style="width: 120px;" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="mt-2 text-white text-opacity-80 small">
                    Status: <strong>{{ $totalLocked }}</strong> Periode Terkunci di Tahun {{ $selectedYear }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 1: KUARTAL & SEMESTER (HIGH LEVEL) --}}
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold text-dark mb-0 text-uppercase letter-spacing-05 fs-7">
            <i class="bi bi-grid-fill text-primary me-2"></i>Kunci Tingkat Kuartal &amp; Semester (Tahun {{ $selectedYear }})
        </h6>
        <span class="text-muted small">Mengunci kuartal otomatis memblokir bulan-bulan di dalamnya</span>
    </div>

    <div class="row g-3">
        {{-- Quarters --}}
        @foreach($quarters as $q)
            @php
                $isLocked = isset($locks[$q]) && $locks[$q]->is_locked;
                $lockData = $locks[$q] ?? null;
            @endphp
            <div class="col-md-3 col-sm-6">
                <div class="lock-card h-100">
                    <div class="p-3 {{ $isLocked ? 'lock-header-locked' : 'lock-header-open' }} d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-bold fs-6">{{ $q }}</span>
                            <div style="font-size: 0.72rem; opacity: 0.85;">
                                @if($q === 'Q1') Jan - Mar
                                @elseif($q === 'Q2') Apr - Jun
                                @elseif($q === 'Q3') Jul - Sep
                                @else Okt - Des
                                @endif
                            </div>
                        </div>
                        <span class="badge {{ $isLocked ? 'badge-locked' : 'badge-open' }} px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                            <i class="bi {{ $isLocked ? 'bi-lock-fill' : 'bi-unlock-fill' }} me-1"></i>
                            {{ $isLocked ? 'TERKUNCI' : 'TERBUKA' }}
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column justify-content-between" style="min-height: 100px;">
                        <div class="text-muted mb-3" style="font-size: 0.75rem;">
                            @if($isLocked)
                                Dikunci oleh: <strong>{{ $lockData->user->name ?? 'Admin' }}</strong><br>
                                <span class="text-muted">{{ $lockData->locked_at ? $lockData->locked_at->format('d M Y H:i') : '-' }}</span>
                            @else
                                Periode aktif dan dapat menerima transaksi dari DMO &amp; Tim Keuangan.
                            @endif
                        </div>
                        <form action="{{ route('admin.period-locks.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                            <input type="hidden" name="periode" value="{{ $q }}">
                            <button type="submit" class="btn w-100 btn-sm fw-bold {{ $isLocked ? 'btn-outline-success' : 'btn-outline-danger' }} rounded-3 d-flex align-items-center justify-content-center gap-1.5"
                                    onclick="return confirm('Apakah Anda yakin ingin {{ $isLocked ? 'MEMBUKA KUNCI' : 'MENGUNCI' }} periode {{ $q }} {{ $selectedYear }}?')">
                                <i class="bi {{ $isLocked ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                                {{ $isLocked ? 'Buka Kunci' : 'Kunci Periode Ini' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Semesters --}}
        @foreach($semesters as $sem)
            @php
                $isLocked = isset($locks[$sem]) && $locks[$sem]->is_locked;
                $lockData = $locks[$sem] ?? null;
            @endphp
            <div class="col-md-6">
                <div class="lock-card h-100">
                    <div class="p-3 {{ $isLocked ? 'lock-header-locked' : 'lock-header-open' }} d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-bold fs-6">{{ $sem }}</span>
                            <span class="text-muted ms-2 small">({{ $sem === 'SEMESTER 1' ? 'Januari - Juni' : 'Juli - Desember' }})</span>
                        </div>
                        <span class="badge {{ $isLocked ? 'badge-locked' : 'badge-open' }} px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                            <i class="bi {{ $isLocked ? 'bi-lock-fill' : 'bi-unlock-fill' }} me-1"></i>
                            {{ $isLocked ? 'TERKUNCI' : 'TERBUKA' }}
                        </span>
                    </div>
                    <div class="p-3 d-flex align-items-center justify-content-between">
                        <div class="text-muted small">
                            @if($isLocked)
                                Dikunci: {{ $lockData->locked_at ? $lockData->locked_at->format('d M Y H:i') : '-' }} oleh {{ $lockData->user->name ?? 'Admin' }}
                            @else
                                Status terbuka untuk seluruh semester {{ $sem === 'SEMESTER 1' ? 'pertama' : 'kedua' }}.
                            @endif
                        </div>
                        <form action="{{ route('admin.period-locks.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                            <input type="hidden" name="periode" value="{{ $sem }}">
                            <button type="submit" class="btn btn-sm fw-bold {{ $isLocked ? 'btn-outline-success' : 'btn-outline-danger' }} rounded-3 px-3 d-flex align-items-center gap-1.5"
                                    onclick="return confirm('Apakah Anda yakin ingin {{ $isLocked ? 'MEMBUKA KUNCI' : 'MENGUNCI' }} {{ $sem }} {{ $selectedYear }}?')">
                                <i class="bi {{ $isLocked ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                                {{ $isLocked ? 'Buka Kunci' : 'Kunci Semester Ini' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- SECTION 2: BULAN SPESIFIK --}}
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold text-dark mb-0 text-uppercase letter-spacing-05 fs-7">
            <i class="bi bi-calendar3 text-primary me-2"></i>Kunci Tingkat Bulan Spesifik (Tahun {{ $selectedYear }})
        </h6>
    </div>

    <div class="row g-2.5">
        @foreach($months as $m)
            @php
                $isLocked = isset($locks[$m]) && $locks[$m]->is_locked;
                $lockData = $locks[$m] ?? null;
            @endphp
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="card border rounded-3 p-2.5 h-100 {{ $isLocked ? 'bg-danger-subtle border-danger-subtle' : 'bg-white border-light-subtle' }}">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="fw-bold fs-7 {{ $isLocked ? 'text-danger' : 'text-dark' }}">{{ $m }}</span>
                        <span class="badge {{ $isLocked ? 'bg-danger text-white' : 'bg-light text-secondary' }}" style="font-size: 0.65rem;">
                            {{ $isLocked ? 'LOCKED' : 'OPEN' }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.68rem; min-height: 18px;">
                        {{ $isLocked ? ($lockData->locked_at ? $lockData->locked_at->format('d/m/Y') : 'Locked') : 'Bebas input' }}
                    </div>
                    <form action="{{ route('admin.period-locks.toggle') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                        <input type="hidden" name="periode" value="{{ $m }}">
                        <button type="submit" class="btn btn-xs w-100 py-1 fw-bold {{ $isLocked ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size: 0.68rem; border-radius: 6px;">
                            <i class="bi {{ $isLocked ? 'bi-unlock' : 'bi-lock' }} me-1"></i>
                            {{ $isLocked ? 'Buka' : 'Kunci' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
