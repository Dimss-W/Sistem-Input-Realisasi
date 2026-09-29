@extends('layouts.main')

@section('title', 'Edit BASTO — ' . $basto->basto_number)
@section('page-title', 'Edit BASTO Draft')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('basto.index') }}" class="text-decoration-none">BASTO</a></li>
    <li class="breadcrumb-item"><a href="{{ route('basto.show', $basto->id) }}" class="text-decoration-none">{{ $basto->basto_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-warning me-2"></i>Edit BASTO Draft</h5>
                        <p class="text-muted small mb-0 mt-1">Nomor BASTO: <strong>{{ $basto->basto_number }}</strong> &bull; Hanya BASTO berstatus <em>Draft</em> yang dapat diedit.</p>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-dark px-3 py-2 rounded-pill fw-semibold fs-8">
                        <i class="bi bi-pencil me-1"></i>Draft Mode
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pengisian form:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('basto.update', $basto->id) }}" method="POST">
                    @csrf @method('PUT')

                    {{-- ── SECTION 1: IDENTITAS PROYEK ──────────────────────── --}}
                    <div class="bg-light rounded-4 p-3.5 p-md-4 mb-4 border">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary rounded-circle p-1.5"><i class="bi bi-folder2-open text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">1. Identitas Proyek &amp; Penanggung Jawab</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-dark small">Project ID <span class="text-danger">*</span></label>
                                <select name="project_id" id="projectIdSelect" class="form-select rounded-3 shadow-none" required>
                                    <option value="">-- Pilih Project --</option>
                                    @foreach($kontrakList as $k)
                                        <option value="{{ $k->project_id }}"
                                                data-name="{{ $k->project_name }}"
                                                data-sm="{{ $k->service_manager }}"
                                                data-cost-no="{{ $k->contract_number ?? '' }}"
                                                data-cost-value="{{ $k->project_value ?? '' }}"
                                                data-cost-based="{{ $k->costbased ?? '' }}"
                                                data-cost-start="{{ $k->start_date ? \Carbon\Carbon::parse($k->start_date)->format('Y-m-d') : '' }}"
                                                data-cost-end="{{ $k->end_date ? \Carbon\Carbon::parse($k->end_date)->format('Y-m-d') : '' }}"
                                                data-supply-chain="{{ $k->resource_management ?? '' }}"
                                                {{ old('project_id', $basto->project_id) === $k->project_id ? 'selected' : '' }}>
                                            {{ $k->project_id }} — {{ $k->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label fw-semibold text-dark small">Nama Project <span class="text-danger">*</span></label>
                                <input type="text" name="project_name" id="projectNameInput" class="form-control rounded-3 shadow-none"
                                       value="{{ old('project_name', $basto->project_name) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small">Service Manager Penerima (Approver) <span class="text-danger">*</span></label>
                                <select name="sm_user_id" id="smSelect" class="form-select rounded-3 shadow-none" required>
                                    <option value="">-- Pilih Service Manager --</option>
                                    @foreach($smList as $sm)
                                        <option value="{{ $sm->id }}" data-sm-name="{{ $sm->name }}" {{ old('sm_user_id', $basto->sm_user_id) == $sm->id ? 'selected' : '' }}>
                                            {{ $sm->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 2: INFORMASI FINANSIAL & KONTRAK (COST & SUPPLY CHAIN) ─ --}}
                    <div class="bg-light rounded-4 p-3.5 p-md-4 mb-4 border">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-circle p-1.5"><i class="bi bi-cash-stack text-white"></i></span>
                                <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">2. Informasi Finansial &amp; Kontrak Proyek</h6>
                            </div>
                            <span class="text-muted small"><i class="bi bi-magic me-1"></i>Otomatis sinkron dari master kontrak</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Cost. No (Nomor Kontrak / SPK)</label>
                                <input type="text" name="cost_no" id="costNoInput" class="form-control rounded-3 shadow-none"
                                       value="{{ old('cost_no', $basto->cost_no) }}" placeholder="Contoh: 012400.PK/04/PGN/2026">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Supply Chain (Vendor / Rekanan Pengadaan)</label>
                                <input type="text" name="supply_chain" id="supplyChainInput" class="form-control rounded-3 shadow-none"
                                       value="{{ old('supply_chain', $basto->supply_chain) }}" placeholder="Contoh: PT Telkom Akses / Subkontraktor">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Cost. Value (Nilai Kontrak / Realisasi Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                    <input type="number" step="0.01" name="cost_value" id="costValueInput" class="form-control border-start-0 rounded-end-3 shadow-none"
                                           value="{{ old('cost_value', $basto->cost_value) }}" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Cost Based (Pagu Anggaran Angka Dasar Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                    <input type="number" step="0.01" name="cost_based" id="costBasedInput" class="form-control border-start-0 rounded-end-3 shadow-none"
                                           value="{{ old('cost_based', $basto->cost_based) }}" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Cost. Date (Start) / Mulai Kontrak</label>
                                <input type="date" name="cost_date_start" id="costDateStartInput" class="form-control rounded-3 shadow-none"
                                       value="{{ old('cost_date_start', $basto->cost_date_start ? \Carbon\Carbon::parse($basto->cost_date_start)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Cost. Date (End) / Selesai Kontrak</label>
                                <input type="date" name="cost_date_end" id="costDateEndInput" class="form-control rounded-3 shadow-none"
                                       value="{{ old('cost_date_end', $basto->cost_date_end ? \Carbon\Carbon::parse($basto->cost_date_end)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 3: CATATAN ────────────────────────────────── --}}
                    <div class="bg-light rounded-4 p-3.5 p-md-4 mb-4 border">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-secondary rounded-circle p-1.5"><i class="bi bi-chat-left-text text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">3. Catatan / Keterangan</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <textarea name="notes" class="form-control rounded-3 shadow-none" rows="4"
                                          placeholder="Tambahkan keterangan lingkup pekerjaan atau catatan...">{{ old('notes', $basto->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2">
                        <a href="{{ route('basto.show', $basto->id) }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                        <button type="submit" class="btn btn-warning rounded-3 fw-bold px-4 text-dark shadow-sm">
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
    document.getElementById('projectIdSelect').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) return;

        document.getElementById('projectNameInput').value = selected.dataset.name || '';
        if (selected.dataset.costNo) document.getElementById('costNoInput').value = selected.dataset.costNo;
        if (selected.dataset.costValue) document.getElementById('costValueInput').value = selected.dataset.costValue;
        if (selected.dataset.costBased) document.getElementById('costBasedInput').value = selected.dataset.costBased;
        if (selected.dataset.costStart) document.getElementById('costDateStartInput').value = selected.dataset.costStart;
        if (selected.dataset.costEnd) document.getElementById('costDateEndInput').value = selected.dataset.costEnd;
        if (selected.dataset.supplyChain) document.getElementById('supplyChainInput').value = selected.dataset.supplyChain;
    });
</script>
@endpush
