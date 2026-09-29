@extends('layouts.main')

@section('title', 'Detail Work Order — ' . $workOrder->wo_number)
@section('page-title', 'Detail Work Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('work-order.index') }}" class="text-decoration-none">Work Order</a></li>
    <li class="breadcrumb-item active">{{ $workOrder->wo_number }}</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Main Detail --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            {{-- Status Banner --}}
            <div class="card-header bg-{{ $workOrder->status_badge_color }} bg-opacity-10 border-0 px-4 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-{{ $workOrder->status_badge_color }} px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                        {{ strtoupper(str_replace('_', ' ', $workOrder->status)) }}
                    </span>
                    <span class="badge bg-{{ $workOrder->priority_badge_color }} bg-opacity-15 text-{{ $workOrder->priority_badge_color }} border border-{{ $workOrder->priority_badge_color }} border-opacity-25 px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                        {{ strtoupper($workOrder->priority) }}
                    </span>
                    @if($workOrder->isOverdue())
                    <span class="badge bg-danger px-3 py-2 rounded-pill" style="font-size:0.8rem;">⚠️ OVERDUE</span>
                    @endif
                    <span class="ms-auto text-muted small">{{ $workOrder->wo_number }}</span>
                </div>
            </div>

            <div class="card-body p-4">
                <h4 class="fw-bold text-dark mb-1">{{ $workOrder->title }}</h4>
                <p class="text-muted small mb-4">{{ $workOrder->category ?? '—' }}</p>

                @if($workOrder->description)
                <div class="mb-4">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Deskripsi</p>
                    <div class="border rounded-3 p-3 bg-light" style="white-space: pre-line;">{{ $workOrder->description }}</div>
                </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Project</p>
                        <p class="fw-bold mb-0">{{ $workOrder->project_id }}</p>
                        <p class="text-muted small mb-0">{{ $workOrder->project_name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">PIC / Ditugaskan ke</p>
                        <p class="fw-semibold mb-0">{{ $workOrder->assignedTo ? $workOrder->assignedTo->name : '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Vendor / Mitra</p>
                        <p class="mb-0">{{ $workOrder->vendor ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Dibuat Oleh</p>
                        <p class="mb-0">{{ $workOrder->createdBy ? $workOrder->createdBy->name : '—' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Tanggal Mulai</p>
                        <p class="mb-0">{{ $workOrder->start_date ? $workOrder->start_date->format('d/m/Y') : '—' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Due Date</p>
                        <p class="mb-0 {{ $workOrder->isOverdue() ? 'text-danger fw-bold' : '' }}">
                            {{ $workOrder->due_date ? $workOrder->due_date->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Tanggal Selesai</p>
                        <p class="mb-0 text-success">{{ $workOrder->completed_date ? $workOrder->completed_date->format('d/m/Y') : '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Estimasi Biaya</p>
                        <p class="mb-0">{{ $workOrder->estimated_cost ? 'Rp ' . number_format($workOrder->estimated_cost, 0, ',', '.') : '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Biaya Aktual</p>
                        <p class="mb-0 fw-semibold {{ $workOrder->actual_cost > $workOrder->estimated_cost ? 'text-danger' : 'text-success' }}">
                            {{ $workOrder->actual_cost ? 'Rp ' . number_format($workOrder->actual_cost, 0, ',', '.') : '—' }}
                        </p>
                    </div>
                </div>

                @if($workOrder->notes)
                <div class="mb-4">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Catatan</p>
                    <div class="border rounded-3 p-3 bg-light" style="white-space: pre-line;">{{ $workOrder->notes }}</div>
                </div>
                @endif

                <div class="d-flex gap-2 flex-wrap border-top pt-4">
                    <a href="{{ route('work-order.index') }}" class="btn btn-outline-secondary rounded-3"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
                    @if(auth()->user()->hasRole(['dmo','admin']))
                    <a href="{{ route('work-order.edit', $workOrder->id) }}" class="btn btn-warning rounded-3 fw-bold text-dark">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi WO</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="text-muted small">Nomor WO</div>
                        <div class="fw-bold font-monospace">{{ $workOrder->wo_number }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Dibuat</div>
                        <div>{{ $workOrder->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Terakhir Diperbarui</div>
                        <div>{{ $workOrder->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    @if($workOrder->estimated_cost && $workOrder->actual_cost)
                    <div>
                        <div class="text-muted small mb-1">Efisiensi Biaya</div>
                        @php
                            $efisiensi = $workOrder->estimated_cost > 0
                                ? round((1 - $workOrder->actual_cost / $workOrder->estimated_cost) * 100, 1)
                                : 0;
                        @endphp
                        <div class="fw-bold {{ $efisiensi >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $efisiensi >= 0 ? '+' : '' }}{{ $efisiensi }}%
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
