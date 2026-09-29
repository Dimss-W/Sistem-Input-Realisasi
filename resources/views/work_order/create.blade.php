@extends('layouts.main')

@section('title', 'Buat Work Order Baru')
@section('page-title', 'Buat Work Order Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('work-order.index') }}" class="text-decoration-none">Work Order</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-plus text-primary me-2"></i>Form Work Order Baru</h5>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4"><ul class="mb-0 ps-3 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                <form action="{{ route('work-order.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Project ID <span class="text-danger">*</span></label>
                            <select name="project_id" id="woProjectId" class="form-select rounded-3" required>
                                <option value="">-- Pilih Project --</option>
                                @foreach($kontrakList as $k)
                                    <option value="{{ $k->project_id }}" data-name="{{ $k->project_name }}"
                                            {{ old('project_id') === $k->project_id ? 'selected' : '' }}>
                                        {{ $k->project_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Nama Project <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="woProjectName" class="form-control rounded-3"
                                   value="{{ old('project_name') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Judul Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3"
                                   value="{{ old('title') }}" placeholder="Deskripsi singkat pekerjaan..." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi Detail</label>
                            <textarea name="description" class="form-control rounded-3" rows="4"
                                      placeholder="Jelaskan detail pekerjaan, scope, dan requirement...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="category" class="form-select rounded-3">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach(['Maintenance','Project','Service','Procurement','Support','Others'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select rounded-3" required>
                                @foreach(['low' => 'Low','normal' => 'Normal','high' => 'High','critical' => 'Critical'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('priority', 'normal') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">PIC / Ditugaskan ke</label>
                            <select name="assigned_to_user_id" class="form-select rounded-3">
                                <option value="">-- Pilih User --</option>
                                @foreach($userList as $u)
                                    <option value="{{ $u->id }}" {{ old('assigned_to_user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vendor / Mitra</label>
                            <input type="text" name="vendor" class="form-control rounded-3" value="{{ old('vendor') }}" placeholder="Nama vendor...">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control rounded-3" value="{{ old('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Batas Waktu (Due Date)</label>
                            <input type="date" name="due_date" class="form-control rounded-3" value="{{ old('due_date') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Estimasi Biaya (Rp)</label>
                            <input type="number" name="estimated_cost" class="form-control rounded-3" value="{{ old('estimated_cost') }}" placeholder="0" min="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Catatan</label>
                            <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-4">
                        <a href="{{ route('work-order.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4">
                            <i class="bi bi-save me-1"></i> Simpan Work Order
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
document.getElementById('woProjectId').addEventListener('change', function() {
    document.getElementById('woProjectName').value = this.options[this.selectedIndex].dataset.name || '';
});
</script>
@endpush
