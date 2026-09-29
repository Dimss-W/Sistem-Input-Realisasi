@extends('layouts.main')

@section('title', 'Audit Log Aktivitas Sistem')
@section('page-title', 'Audit Log')

@section('breadcrumb')
    <li class="breadcrumb-item active">Audit Log</li>
@endsection

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="bi bi-journal-text fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1">{{ number_format($totalCount) }}</div>
                    <div class="text-muted small">Total Log</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="bi bi-calendar-check fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1">{{ number_format($todayCount) }}</div>
                    <div class="text-muted small">Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                    <i class="bi bi-people fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1">{{ $users->count() }}</div>
                    <div class="text-muted small">User Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="bi bi-layers fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1">{{ $modules->count() }}</div>
                    <div class="text-muted small">Modul</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('audit.log') }}" id="auditFilterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-search me-1"></i>Pencarian Kata Kunci
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Deskripsi, record ID, modul..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                {{-- Modul --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-layers me-1"></i>Modul Sistem
                    </label>
                    <select name="module" class="form-select form-select-sm">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" {{ ($filters['module'] ?? '') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Aksi --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-lightning-charge me-1"></i>Tipe Aksi
                    </label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" {{ ($filters['action'] ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- User --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-person me-1"></i>Pengguna (User)
                    </label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ ($filters['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ strtoupper($u->role) }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range (Dari Tanggal s/d Hingga Tanggal) --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-calendar-event me-1"></i>Dari Tanggal
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-range"></i></span>
                        <input type="date" name="date_from" id="auditDateFrom" class="form-control" value="{{ $filters['date_from'] ?? '' }}" title="Dari tanggal">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-calendar-check me-1"></i>Hingga Tanggal
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-range"></i></span>
                        <input type="date" name="date_to" id="auditDateTo" class="form-control" value="{{ $filters['date_to'] ?? '' }}" title="Hingga tanggal">
                    </div>
                </div>

                {{-- Quick Presets & Filter Actions --}}
                <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-end">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        {{-- Quick Range Buttons --}}
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small me-1" style="font-size: 0.72rem;">Rentang Cepat:</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setQuickDate('today')">Hari Ini</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setQuickDate('7days')">7 Hari Terakhir</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setQuickDate('month')">Bulan Ini</button>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <a href="{{ route('audit.log') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-3" title="Reset semua filter">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </a>
                            <button class="btn btn-primary btn-sm px-4 fw-bold rounded-3 shadow-sm" type="submit">
                                <i class="bi bi-funnel-fill me-1"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Active Filter Indicator Strip --}}
        @if(!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['search']) || !empty($filters['module']) || !empty($filters['action']) || !empty($filters['user_id']))
            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 0.76rem;">
                    <span class="text-muted fw-bold"><i class="bi bi-funnel text-primary me-1"></i>Filter Aktif:</span>
                    @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1.5 rounded-pill">
                            <i class="bi bi-calendar-event me-1"></i>
                            Periode: {{ !empty($filters['date_from']) ? date('d/m/Y', strtotime($filters['date_from'])) : 'Awal' }}
                            s/d
                            {{ !empty($filters['date_to']) ? date('d/m/Y', strtotime($filters['date_to'])) : 'Sekarang' }}
                        </span>
                    @endif
                    @if(!empty($filters['search']))
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-pill">
                            <i class="bi bi-search me-1"></i>Kata Kunci: "{{ $filters['search'] }}"
                        </span>
                    @endif
                    @if(!empty($filters['module']))
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2.5 py-1.5 rounded-pill">
                            Modul: {{ ucfirst($filters['module']) }}
                        </span>
                    @endif
                    @if(!empty($filters['action']))
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill">
                            Aksi: {{ $filters['action'] }}
                        </span>
                    @endif
                    @if(!empty($filters['user_id']))
                        @php $uFilter = $users->firstWhere('id', $filters['user_id']); @endphp
                        @if($uFilter)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill">
                                User: {{ $uFilter->name }}
                            </span>
                        @endif
                    @endif
                </div>
                <a href="{{ route('audit.log') }}" class="text-danger small text-decoration-none fw-bold" style="font-size: 0.74rem;">
                    <i class="bi bi-x-circle me-1"></i>Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4">
        <span class="fw-bold text-dark"><i class="bi bi-shield-check me-2 text-success"></i>Log Aktivitas Sistem</span>
        <span class="text-muted small">{{ $logs->total() }} total entri</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold border-bottom" style="font-size: 0.72rem;">
                <tr>
                    <th class="py-3 ps-4">Waktu</th>
                    <th>User</th>
                    <th class="text-center">Aksi</th>
                    <th>Modul</th>
                    <th>Record ID</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $actionBadgeStyle = match(strtoupper($log->action)) {
                        'CREATE', 'SUBMIT', 'STORE' => 'background: #dcfce7; color: #15803d; border: 1px solid #86efac;',
                        'UPDATE', 'EDIT'            => 'background: #fef3c7; color: #b45309; border: 1px solid #fde047;',
                        'DELETE', 'REJECT', 'CANCEL'=> 'background: #ffe4e6; color: #be123c; border: 1px solid #fca5a5;',
                        'APPROVE'                   => 'background: #e0e7ff; color: #4338ca; border: 1px solid #a5b4fc;',
                        'LOGIN'                     => 'background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc;',
                        'LOGOUT'                    => 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;',
                        default                     => 'background: #f8fafc; color: #334155; border: 1px solid #cbd5e1;',
                    };
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold text-dark">{{ $log->created_at ? $log->created_at->format('d/m/Y') : '—' }}</div>
                        <small class="text-secondary">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }} WIB</small>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $log->user ? $log->user->name : 'System' }}</div>
                        <small class="text-secondary">{{ $log->user_role ?? '' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.68rem; {{ $actionBadgeStyle }}">
                            {{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 rounded-pill" style="font-size:0.68rem;">
                            {{ $log->module }}
                        </span>
                    </td>
                    <td>
                        <code class="text-muted" style="font-size: 0.75rem;">{{ $log->record_id ?? '—' }}</code>
                    </td>
                    <td>
                        <span class="text-truncate d-block" style="max-width: 220px;" title="{{ $log->description }}">{{ $log->description ?? '—' }}</span>
                    </td>
                    <td>
                        <code style="font-size: 0.72rem; color: #6c757d;">{{ $log->ip_address ?? '—' }}</code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                        Belum ada log aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function setQuickDate(range) {
    const fromInput = document.getElementById('auditDateFrom');
    const toInput = document.getElementById('auditDateTo');
    const today = new Date();
    
    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    toInput.value = formatDate(today);

    if (range === 'today') {
        fromInput.value = formatDate(today);
    } else if (range === '7days') {
        const past7 = new Date();
        past7.setDate(today.getDate() - 7);
        fromInput.value = formatDate(past7);
    } else if (range === 'month') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        fromInput.value = formatDate(firstDay);
    }

    document.getElementById('auditFilterForm').submit();
}
</script>
@endpush
