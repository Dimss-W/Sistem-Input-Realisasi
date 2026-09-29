@extends('layouts.main')

@section('title', 'Edit Work Order — ' . $workOrder->wo_number)
@section('page-title', 'Edit Work Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('work-order.index') }}" class="text-decoration-none">Work Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('work-order.show', $workOrder->id) }}" class="text-decoration-none">{{ $workOrder->wo_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Work Order</h5>
                <p class="text-muted small mb-0">Nomor: <strong>{{ $workOrder->wo_number }}</strong></p>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4"><ul class="mb-0 ps-3 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                <form action="{{ route('work-order.update', $workOrder->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Project ID <span class="text-danger">*</span></label>
                            <select name="project_id" id="woEditProjectId" class="form-select rounded-3" required>
                                @foreach($kontrakList as $k)
                                    <option value="{{ $k->project_id }}" data-name="{{ $k->project_name }}"
                                            {{ old('project_id', $workOrder->project_id) === $k->project_id ? 'selected' : '' }}>
                                        {{ $k->project_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Nama Project <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="woEditProjectName" class="form-control rounded-3"
                                   value="{{ old('project_name', $workOrder->project_name) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Judul Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3"
                                   value="{{ old('title', $workOrder->title) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi Detail</label>
                            <textarea name="description" class="form-control rounded-3" rows="4">{{ old('description', $workOrder->description) }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="category" class="form-select rounded-3">
                                <option value="">—</option>
                                @foreach(['Maintenance','Project','Service','Procurement','Support','Others'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $workOrder->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select rounded-3" required>
                                @foreach(['low','normal','high','critical'] as $p)
                                    <option value="{{ $p }}" {{ old('priority', $workOrder->priority) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3" required>
                                @foreach(['open','in_progress','on_hold','closed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $workOrder->status) === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">PIC</label>
                            <select name="assigned_to_user_id" class="form-select rounded-3">
                                <option value="">—</option>
                                @foreach($userList as $u)
                                    <option value="{{ $u->id }}" {{ old('assigned_to_user_id', $workOrder->assigned_to_user_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vendor</label>
                            <input type="text" name="vendor" class="form-control rounded-3" value="{{ old('vendor', $workOrder->vendor) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Mulai</label>
                            <input type="date" name="start_date" class="form-control rounded-3" value="{{ old('start_date', $workOrder->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Due Date</label>
                            <input type="date" name="due_date" class="form-control rounded-3" value="{{ old('due_date', $workOrder->due_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Selesai</label>
                            <input type="date" name="completed_date" class="form-control rounded-3" value="{{ old('completed_date', $workOrder->completed_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Estimasi Biaya (Rp)</label>
                            <input type="number" name="estimated_cost" class="form-control rounded-3" value="{{ old('estimated_cost', $workOrder->estimated_cost) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Biaya Aktual (Rp)</label>
                            <input type="number" name="actual_cost" class="form-control rounded-3" value="{{ old('actual_cost', $workOrder->actual_cost) }}" min="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Catatan</label>
                            <textarea name="notes" class="form-control rounded-3" rows="3">{{ old('notes', $workOrder->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-4">
                        <a href="{{ route('work-order.show', $workOrder->id) }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                        <button type="submit" class="btn btn-warning rounded-3 fw-bold px-4 text-dark">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('woEditProjectId').addEventListener('change', function() {
    document.getElementById('woEditProjectName').value = this.options[this.selectedIndex].dataset.name || '';
});
</script>
@endpush
