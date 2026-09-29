@extends('layouts.main')

@section('title', 'Monitoring Work Order')
@section('page-title', 'Monitoring Work Order')

@section('breadcrumb')
    <li class="breadcrumb-item active">Work Order</li>
@endsection

@section('content')

{{-- Status Summary Cards --}}
<div class="row g-3 mb-4">
    @php
        $woCards = [
            ['key' => 'open',        'label' => 'Open',         'icon' => 'bi-file-earmark-plus', 'color' => 'primary'],
            ['key' => 'in_progress', 'label' => 'In Progress',  'icon' => 'bi-arrow-repeat',       'color' => 'warning'],
            ['key' => 'on_hold',     'label' => 'On Hold',      'icon' => 'bi-pause-circle',       'color' => 'secondary'],
            ['key' => 'closed',      'label' => 'Closed',       'icon' => 'bi-check2-all',         'color' => 'success'],
        ];
    @endphp
    @foreach($woCards as $card)
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-{{ $card['color'] }} bg-opacity-10">
                    <i class="bi {{ $card['icon'] }} fs-4 text-{{ $card['color'] }}"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $statusCounts[$card['key']] }}</div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($overdueCount > 0)
<div class="alert alert-danger border-0 rounded-4 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-alarm-fill fs-5"></i>
    <strong>{{ $overdueCount }} Work Order melewati batas waktu (overdue)!</strong>
</div>
@endif

{{-- Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('work-order.index') }}" class="row g-2 align-items-center">
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Nomor WO / Judul / Project..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['open','in_progress','on_hold','closed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">Semua Prioritas</option>
                    @foreach(['low','normal','high','critical'] as $p)
                        <option value="{{ $p }}" {{ ($filters['priority'] ?? '') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">Semua Project</option>
                    @foreach($projectIds as $pid)
                        <option value="{{ $pid }}" {{ ($filters['project_id'] ?? '') === $pid ? 'selected' : '' }}>{{ $pid }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('work-order.index') }}" class="btn btn-outline-secondary btn-sm px-2"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4">
        <span class="fw-bold text-dark"><i class="bi bi-clipboard-check me-2 text-primary"></i>Daftar Work Order</span>
        @if(auth()->user()->hasRole(['dmo','admin']))
        <a href="{{ route('work-order.create') }}" class="btn btn-primary btn-sm fw-bold rounded-3 px-3">
            <i class="bi bi-plus-lg me-1"></i> Buat WO
        </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold border-bottom" style="font-size: 0.74rem;">
                <tr>
                    <th class="py-3 ps-4">Nomor WO</th>
                    <th>Project</th>
                    <th>Judul</th>
                    <th class="text-center">Prioritas</th>
                    <th class="text-center">Status</th>
                    <th>Due Date</th>
                    <th>PIC</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                @php
                    $isOverdue = $wo->isOverdue();
                    $colorMap = [
                        'primary'   => ['bg' => 'rgba(37, 99, 235, 0.1)',  'text' => '#2563eb', 'border' => 'rgba(37, 99, 235, 0.2)'],
                        'warning'   => ['bg' => 'rgba(245, 158, 11, 0.1)',  'text' => '#d97706', 'border' => 'rgba(245, 158, 11, 0.2)'],
                        'secondary' => ['bg' => 'rgba(100, 116, 139, 0.1)', 'text' => '#475569', 'border' => 'rgba(100, 116, 139, 0.2)'],
                        'success'   => ['bg' => 'rgba(22, 163, 74, 0.1)',  'text' => '#16a34a', 'border' => 'rgba(22, 163, 74, 0.2)'],
                        'danger'    => ['bg' => 'rgba(220, 38, 38, 0.1)',  'text' => '#dc2626', 'border' => 'rgba(220, 38, 38, 0.2)'],
                        'dark'      => ['bg' => 'rgba(15, 23, 42, 0.1)',    'text' => '#0f172a', 'border' => 'rgba(15, 23, 42, 0.2)'],
                    ];
                    $pCol = $colorMap[$wo->priority_badge_color] ?? $colorMap['dark'];
                    $sCol = $colorMap[$wo->status_badge_color] ?? $colorMap['dark'];
                @endphp
                <tr style="{{ $isOverdue ? 'background-color: rgba(220, 38, 38, 0.05);' : '' }}">
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $wo->wo_number }}</div>
                        <small class="text-muted">{{ $wo->category ?? '—' }}</small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $wo->project_id }}</div>
                        <small class="text-muted text-truncate d-block" style="max-width:140px;" title="{{ $wo->project_name }}">{{ $wo->project_name }}</small>
                    </td>
                    <td>
                        <span class="text-truncate d-block" style="max-width:200px;" title="{{ $wo->title }}">{{ $wo->title }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge border rounded-pill px-2" style="font-size:0.68rem; background-color: {{ $pCol['bg'] }}; color: {{ $pCol['text'] }}; border-color: {{ $pCol['border'] }} !important;">
                            {{ strtoupper($wo->priority) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge border rounded-pill px-2" style="font-size:0.68rem; background-color: {{ $sCol['bg'] }}; color: {{ $sCol['text'] }}; border-color: {{ $sCol['border'] }} !important;">
                            {{ strtoupper(str_replace('_', ' ', $wo->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($wo->due_date)
                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-dark' }}">
                                {{ $isOverdue ? '⚠️ ' : '' }}{{ $wo->due_date->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $wo->assignedTo ? $wo->assignedTo->name : '—' }}</td>
                    <td class="text-center pe-4">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('work-order.show', $wo->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2" title="Detail"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->hasRole(['dmo','admin']))
                            <a href="{{ route('work-order.edit', $wo->id) }}" class="btn btn-sm btn-outline-warning rounded-3 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('work-order.destroy', $wo->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-3 px-2" title="Hapus" onclick="return confirm('Hapus WO ini?')"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>
                        Belum ada Work Order.
                        @if(auth()->user()->hasRole(['dmo','admin']))
                            <a href="{{ route('work-order.create') }}" class="d-block mt-2 text-decoration-none">Buat Work Order pertama →</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($workOrders->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $workOrders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
