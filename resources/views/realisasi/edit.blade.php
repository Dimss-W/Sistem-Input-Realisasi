@extends('layouts.main')

@section('title', 'Edit Realisasi #' . $realisasi->id)
@section('page-title', 'Edit Realisasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}" class="text-decoration-none">Data Realisasi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('realisasi.show', $realisasi->id) }}" class="text-decoration-none">#{{ $realisasi->id }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
    .form-section {
        background: #fff;
        border: 1px solid #F1F5F9;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .form-section-header {
        padding: 12px 18px;
        border-bottom: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        gap: 8px;
        background: #FAFBFC;
    }
    .form-section-header .section-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .form-section-header h6 {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .form-section-body { padding: 16px 18px; }

    .form-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: #64748B;
        margin-bottom: 4px;
    }
    .form-control, .form-select {
        font-size: 0.82rem;
        border-color: #E2E8F0;
        border-radius: 7px;
        color: #0F172A;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #F59E0B;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
    }
    .form-text { font-size: 0.7rem; color: #94A3B8; margin-top: 3px; }
    .required-star { color: #EF4444; font-size: 0.85em; }

    .sidebar-sticky { position: sticky; top: 80px; }

    .update-btn {
        background: linear-gradient(135deg, #B45309, #F59E0B);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 0.85rem;
        font-weight: 700;
        width: 100%;
        transition: opacity 0.2s;
    }
    .update-btn:hover { opacity: 0.9; color: #fff; }
    .update-btn:disabled { opacity: 0.65; }

    .money-input-wrap { position: relative; }
    .money-prefix {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%);
        font-size: 0.78rem; font-weight: 600; color: #94A3B8;
        pointer-events: none;
    }
    .money-input-wrap .form-control { padding-left: 30px; }

    .info-banner {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-left: 4px solid #3B82F6;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')

{{-- Info Banner --}}
<div class="info-banner d-flex align-items-center gap-3">
    <i class="bi bi-pencil-square text-primary fs-5 flex-shrink-0"></i>
    <div>
        <div class="fw-bold text-dark" style="font-size:0.85rem;">
            Mengedit Record ID #{{ $realisasi->id }}
            <code class="ms-2" style="font-size:0.75rem; background:#DBEAFE; color:#1D4ED8; padding:2px 6px; border-radius:4px;">{{ $realisasi->project_id }}</code>
        </div>
        <div style="font-size:0.78rem; color:#64748B; margin-top:2px;">
            {{ $realisasi->project_name }} —
            Terakhir diperbarui: {{ $realisasi->updated_at?->format('d M Y H:i') ?? $realisasi->created_at?->format('d M Y H:i') ?? '—' }}
        </div>
    </div>
</div>

<form action="{{ route('realisasi.update', $realisasi->id) }}" method="POST" id="formEdit">
    @csrf
    @method('PUT')

    @if($errors->any())
    <div class="alert alert-danger d-flex gap-3 mb-4" style="border-radius:10px; border-left:4px solid #EF4444;">
        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-bold mb-1" style="font-size:0.85rem;">Terdapat kesalahan input:</div>
            <ul class="mb-0 ps-3" style="font-size:0.8rem;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="row g-3">

        {{-- ═══ KOLOM KIRI ═══ --}}
        <div class="col-lg-8">

            {{-- Project Info --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="section-icon" style="background:#EFF6FF;">
                        <i class="bi bi-folder2-open" style="color:#2563EB;"></i>
                    </div>
                    <h6>Informasi Project</h6>
                </div>
                <div class="form-section-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Project ID <span class="required-star">*</span></label>
                            <input type="text" class="form-control @error('project_id') is-invalid @enderror"
                                   id="project_id" name="project_id"
                                   value="{{ old('project_id', $realisasi->project_id) }}"
                                   list="projectIdList"
                                   style="text-transform:uppercase;">
                            <datalist id="projectIdList">
                                @foreach($projectIds as $pid)<option value="{{ $pid }}">@endforeach
                            </datalist>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Service Manager</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold d-flex align-items-center gap-1" id="btnTambahSM" style="font-size:0.75rem; color:#2563EB;">
                                    <i class="bi bi-plus-circle-fill"></i>+ Tambah SM Baru
                                </button>
                            </div>
                            @php
                                $currentSM = old('service_manager', $realisasi->kontrak->service_manager ?? '');
                            @endphp
                            <select class="form-select @error('service_manager') is-invalid @enderror"
                                    id="service_manager" name="service_manager">
                                <option value="">— Pilih Service Manager —</option>
                                <option value="__NEW_SM__" style="color:#2563EB; font-weight:700;">➕ + Tambah SM Baru...</option>
                                @if($currentSM && !$smList->contains($currentSM))
                                    <option value="{{ $currentSM }}" selected>{{ $currentSM }}</option>
                                @endif
                                @foreach($smList as $sm)
                                    <option value="{{ $sm }}" {{ $currentSM === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                                @endforeach
                            </select>
                            @error('service_manager')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Project Name <span class="required-star">*</span></label>
                            <input type="text" class="form-control @error('project_name') is-invalid @enderror"
                                   id="project_name" name="project_name"
                                   value="{{ old('project_name', $realisasi->project_name) }}">
                            @error('project_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- SMART AUTO-FILL & LIVE BUDGET TELEMETRY CARD --}}
                    <div id="projectTelemetryCard" class="mt-3 p-3 rounded-3 border d-none" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); transition: all 0.3s ease;">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary px-2.5 py-1 rounded-pill" style="font-size:0.7rem; font-weight:700;">
                                    <i class="bi bi-shield-check me-1"></i> MASTER KONTRAK
                                </span>
                                <span class="small fw-bold text-dark" id="telemetryClientName">—</span>
                            </div>
                            <span class="badge" id="telemetryBurnBadge" style="font-size: 0.72rem; font-weight:700;">
                                Serapan 0%
                            </span>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 rounded bg-white border">
                                    <div class="text-muted text-uppercase" style="font-size:0.65rem; font-weight:600;">Nilai Kontrak</div>
                                    <div class="fw-bold text-dark font-monospace" style="font-size:0.8rem;" id="telemetryProjectVal">Rp 0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-white border">
                                    <div class="text-muted text-uppercase" style="font-size:0.65rem; font-weight:600;">Realisasi Berjalan</div>
                                    <div class="fw-bold text-primary font-monospace" style="font-size:0.8rem;" id="telemetrySpentVal">Rp 0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-white border">
                                    <div class="text-muted text-uppercase" style="font-size:0.65rem; font-weight:600;">Sisa Anggaran</div>
                                    <div class="fw-bold font-monospace" style="font-size:0.8rem;" id="telemetryRemainingVal">Rp 0</div>
                                </div>
                            </div>
                        </div>
                        {{-- Progress bar serapan --}}
                        <div class="progress mt-2" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                            <div class="progress-bar" id="telemetryProgressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        {{-- Warning jika over budget --}}
                        <div id="telemetryOverBudgetAlert" class="alert alert-danger py-1.5 px-3 mt-2 mb-0 d-none d-flex align-items-center gap-2" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="bi bi-exclamation-octagon-fill fs-6 flex-shrink-0"></i>
                            <div>
                                <span class="fw-bold">Peringatan Batas Anggaran:</span> Realisasi input saat ini melebihi sisa pagu kontrak (<span id="overBudgetDiff">Rp 0</span>).
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Transaksi --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="section-icon" style="background:#F0FDF4;">
                        <i class="bi bi-file-text" style="color:#16A34A;"></i>
                    </div>
                    <h6>Informasi Transaksi</h6>
                </div>
                <div class="form-section-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Item Biaya</label>
                            <input type="text" class="form-control @error('item_biaya') is-invalid @enderror"
                                   id="item_biaya" name="item_biaya"
                                   value="{{ old('item_biaya', $realisasi->item_biaya) }}"
                                   list="itemBiayaList" style="text-transform:uppercase;">
                            <datalist id="itemBiayaList">
                                @foreach($itemBiayaList as $ib)<option value="{{ $ib }}">@endforeach
                            </datalist>
                            @error('item_biaya')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan Kerja</label>
                            <input type="text" class="form-control @error('satuan_kerja') is-invalid @enderror"
                                   id="satuan_kerja" name="satuan_kerja"
                                   value="{{ old('satuan_kerja', $realisasi->satuan_kerja) }}"
                                   list="satuanList" style="text-transform:uppercase;">
                            <datalist id="satuanList">
                                @foreach($satuanList as $s)<option value="{{ $s }}">@endforeach
                            </datalist>
                            @error('satuan_kerja')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIC</label>
                            <input type="text" class="form-control @error('pic') is-invalid @enderror"
                                   id="pic" name="pic"
                                   value="{{ old('pic', $realisasi->pic) }}"
                                   list="picListData" style="text-transform:uppercase;">
                            <datalist id="picListData">
                                @foreach($picList as $p)<option value="{{ $p }}">@endforeach
                            </datalist>
                            @error('pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Periode <span class="required-star">*</span></label>
                            <select class="form-select @error('periode') is-invalid @enderror" id="periode" name="periode">
                                <option value="">— Pilih Bulan —</option>
                                @foreach($periodeList as $bln)
                                    <option value="{{ $bln }}" {{ old('periode', $realisasi->periode) === $bln ? 'selected' : '' }}>{{ $bln }}</option>
                                @endforeach
                            </select>
                            @error('periode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="required-star">*</span></label>
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror"
                                   id="tahun" name="tahun"
                                   value="{{ old('tahun', $realisasi->tahun) }}"
                                   min="2000" max="2099">
                            @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sifat</label>
                            <select class="form-select @error('sifat') is-invalid @enderror" id="sifat" name="sifat">
                                <option value="">— Pilih Sifat —</option>
                                @foreach($sifatList as $sf)
                                    <option value="{{ $sf }}" {{ old('sifat', $realisasi->sifat) === $sf ? 'selected' : '' }}>{{ $sf }}</option>
                                @endforeach
                            </select>
                            @error('sifat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Keuangan --}}
            <div class="form-section">
                <div class="form-section-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="section-icon" style="background:#FFFBEB;">
                            <i class="bi bi-cash-coin" style="color:#D97706;"></i>
                        </div>
                        <h6 class="mb-0">Informasi Keuangan</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2" id="fxLiveBadgeContainer">
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1.5 d-flex align-items-center gap-1.5" style="font-size:0.72rem; font-weight:700;">
                            <i class="bi bi-broadcast"></i> <span id="fxLiveText">Kurs Real-Time Internet (Live)</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2 rounded-pill btn-action-animated" id="btnRefreshFx" style="font-size:0.7rem;" title="Update Kurs Terbaru dari Internet">
                            <i class="bi bi-arrow-clockwise me-1"></i>Update Kurs
                        </button>
                    </div>
                </div>
                <div class="form-section-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Currency</label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                                @foreach($currencyList as $cur)
                                    <option value="{{ $cur }}" {{ old('currency', $realisasi->currency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                                @endforeach
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Realisasi Original</label>
                            <div class="money-input-wrap">
                                <span class="money-prefix" id="origCurrencyPrefix">
                                    {{ match($realisasi->currency ?? 'IDR') { 'USD' => '$', 'EUR' => '€', 'SGD' => 'S$', 'JPY' => '¥', default => 'Rp' } }}
                                </span>
                                <input type="number" step="0.01" class="form-control @error('realisasi_biaya_original') is-invalid @enderror"
                                       id="realisasi_biaya_original" name="realisasi_biaya_original"
                                       value="{{ old('realisasi_biaya_original', $realisasi->realisasi_biaya_original) }}">
                            </div>
                            <div class="form-text">Sekarang: {{ ($realisasi->currency ?? 'IDR') === 'IDR' ? number_format($realisasi->realisasi_biaya_original ?? 0, 0, ',', '.') : number_format($realisasi->realisasi_biaya_original ?? 0, 2, ',', '.') }}</div>
                            @error('realisasi_biaya_original')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Realisasi IDR</label>
                            <div class="money-input-wrap">
                                <span class="money-prefix">Rp</span>
                                <input type="number" step="0.01" class="form-control @error('realisasi_biaya_idr') is-invalid @enderror"
                                       id="realisasi_biaya_idr" name="realisasi_biaya_idr"
                                       value="{{ old('realisasi_biaya_idr', $realisasi->realisasi_biaya_idr) }}">
                            </div>
                            <div id="conversionHelper"></div>
                            @error('realisasi_biaya_idr')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Realisasi Final <span class="required-star">*</span>
                                <span class="badge ms-1" style="background:#FEF3C7;color:#92400E;font-size:0.58rem;font-weight:700;padding:2px 5px;border-radius:4px;">Power BI</span>
                            </label>
                            <div class="money-input-wrap">
                                <span class="money-prefix">Rp</span>
                                <input type="number" step="0.01" class="form-control @error('realisasi_biaya_final') is-invalid @enderror"
                                       id="realisasi_biaya_final" name="realisasi_biaya_final"
                                       value="{{ old('realisasi_biaya_final', $realisasi->realisasi_biaya_final) }}">
                            </div>
                            <div class="form-text">Sekarang: <strong class="text-primary">{{ number_format($realisasi->realisasi_biaya_final ?? 0, 0, ',', '.') }}</strong></div>
                            @error('realisasi_biaya_final')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM KANAN ═══ --}}
        <div class="col-lg-4">
            <div class="sidebar-sticky">

                {{-- Record Info --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon" style="background:#F0F9FF;">
                            <i class="bi bi-info-circle" style="color:#0284C7;"></i>
                        </div>
                        <h6>Info Record</h6>
                    </div>
                    <div class="form-section-body" style="padding:12px 16px;">
                        <div style="font-size:0.74rem; color:#64748B; line-height:1.8;">
                            <div><span class="fw-semibold text-dark">ID Database:</span> #{{ $realisasi->id }}</div>
                            <div class="mt-1"><span class="fw-semibold text-dark">Record Key:</span></div>
                            <code style="font-size:0.62rem; word-break:break-all; color:#64748B;">{{ $realisasi->record_key ?? '—' }}</code>
                        </div>
                        <div class="mt-2 p-2 rounded" style="background:#FFFBEB; border:1px solid #FDE68A; font-size:0.72rem; color:#92400E;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Perubahan akan UPDATE record yang sama, ID tidak berubah.
                        </div>
                    </div>
                </div>

                {{-- Status & Vendor --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon" style="background:#F0F9FF;">
                            <i class="bi bi-tag" style="color:#0284C7;"></i>
                        </div>
                        <h6>Status &amp; Vendor</h6>
                    </div>
                    <div class="form-section-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="">— Pilih Status —</option>
                                @foreach($statusList as $s)
                                    <option value="{{ $s }}" {{ old('status', $realisasi->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vendor</label>
                            <input type="text" class="form-control @error('vendor') is-invalid @enderror"
                                   id="vendor" name="vendor"
                                   value="{{ old('vendor', $realisasi->vendor) }}"
                                   list="vendorListData" style="text-transform:uppercase;">
                            <datalist id="vendorListData">
                                @foreach($vendorList as $v)<option value="{{ $v }}">@endforeach
                            </datalist>
                            @error('vendor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Data Flag</label>
                            <input type="text" class="form-control @error('data_flag') is-invalid @enderror"
                                   id="data_flag" name="data_flag"
                                   value="{{ old('data_flag', $realisasi->data_flag) }}">
                            @error('data_flag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Evidence --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon" style="background:#F5F3FF;">
                            <i class="bi bi-paperclip" style="color:#7C3AED;"></i>
                        </div>
                        <h6>Evidence</h6>
                    </div>
                    <div class="form-section-body">
                        <label class="form-label">Link / Nama Evidence</label>
                        <textarea class="form-control @error('link_evidence') is-invalid @enderror"
                                  id="link_evidence" name="link_evidence"
                                  rows="3">{{ old('link_evidence', $realisasi->link_evidence) }}</textarea>
                        @error('link_evidence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="form-section">
                    <div class="form-section-body">
                        <input type="hidden" name="source_row" value="{{ $realisasi->source_row }}">
                        <button type="submit" class="update-btn" id="btnUpdate">
                            <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('realisasi.show', $realisasi->id) }}" class="btn btn-sm w-100 mt-2 fw-semibold"
                           style="background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0; border-radius:8px; padding:8px;">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const kontrakMap = @json($kontrakMap);
    document.getElementById('project_id')?.addEventListener('change', function() {
        const val = this.value.trim().toUpperCase();
        if (kontrakMap[val]) {
            if (kontrakMap[val].project_name) {
                document.getElementById('project_name').value = kontrakMap[val].project_name;
            }
            if (kontrakMap[val].service_manager) {
                const smVal = kontrakMap[val].service_manager.toUpperCase();
                const selectSM = document.getElementById('service_manager');
                if (selectSM) {
                    let found = false;
                    for (let i = 0; i < selectSM.options.length; i++) {
                        if (selectSM.options[i].value.toUpperCase() === smVal) {
                            selectSM.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        const newOpt = document.createElement('option');
                        newOpt.value = smVal;
                        newOpt.textContent = smVal;
                        newOpt.selected = true;
                        selectSM.appendChild(newOpt);
                    }
                }
            }
        }
    });

    // Tambah Service Manager Baru (SweetAlert Prompt)
    function openTambahSMModal() {
        Swal.fire({
            title: 'Tambah Service Manager',
            input: 'text',
            inputLabel: 'Nama Service Manager Baru',
            inputPlaceholder: 'Contoh: HENDRA WIJAYA',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Tambah',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563EB',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Nama Service Manager tidak boleh kosong!';
                }
            }
        }).then((result) => {
            const selectSM = document.getElementById('service_manager');
            if (result.isConfirmed && result.value) {
                const newSM = result.value.trim().toUpperCase();
                if (selectSM) {
                    let exists = false;
                    for (let i = 0; i < selectSM.options.length; i++) {
                        if (selectSM.options[i].value.toUpperCase() === newSM) {
                            selectSM.selectedIndex = i;
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        const opt = document.createElement('option');
                        opt.value = newSM;
                        opt.textContent = newSM;
                        opt.selected = true;
                        selectSM.appendChild(opt);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Service Manager "' + newSM + '" ditambahkan ke pilihan.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            } else {
                if (selectSM && selectSM.value === '__NEW_SM__') {
                    selectSM.value = '';
                }
            }
        });
    }

    document.getElementById('btnTambahSM')?.addEventListener('click', openTambahSMModal);

    document.getElementById('service_manager')?.addEventListener('change', function() {
        if (this.value === '__NEW_SM__') {
            openTambahSMModal();
        }
    });

    let liveExchangeRates = {!! json_encode($fxData['rates'] ?? ['IDR' => 1, 'USD' => 17765, 'EUR' => 20597, 'SGD' => 13954, 'JPY' => 110.95]) !!};
    let fxMetaInfo = {!! json_encode(['last_updated' => $fxData['last_updated'] ?? '', 'is_live' => $fxData['is_live'] ?? true, 'source' => $fxData['source'] ?? 'Live FX Market']) !!};
    const kontrakMasterData = {!! json_encode($kontrakMap) !!};

    function formatRupiahDisplay(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
    }

    let activeProjectContract = null;

    function handleProjectAutoFill() {
        const pIdInput = document.getElementById('project_id');
        const pNameInput = document.getElementById('project_name');
        const smSelect = document.getElementById('service_manager');
        const telemetryCard = document.getElementById('projectTelemetryCard');
        if (!pIdInput) return;

        const val = pIdInput.value.trim().toUpperCase();
        if (kontrakMasterData && kontrakMasterData[val]) {
            const data = kontrakMasterData[val];
            activeProjectContract = data;

            // Auto-fill Project Name if empty
            if (pNameInput && !pNameInput.value.trim() && data.project_name) {
                pNameInput.value = data.project_name;
            }

            // Auto-select Service Manager if empty
            if (smSelect && (!smSelect.value || smSelect.value === '__NEW_SM__') && data.service_manager) {
                const targetSM = data.service_manager.toUpperCase();
                let matched = false;
                for (let i = 0; i < smSelect.options.length; i++) {
                    if (smSelect.options[i].value.toUpperCase() === targetSM) {
                        smSelect.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    const newOpt = new Option(data.service_manager, data.service_manager, true, true);
                    smSelect.add(newOpt);
                }
            }

            // Render Telemetry Card
            if (telemetryCard) {
                telemetryCard.classList.remove('d-none');
                document.getElementById('telemetryClientName').textContent = 'Klien: ' + (data.project_client || '—');
                document.getElementById('telemetryProjectVal').textContent = formatRupiahDisplay(data.project_value);
                document.getElementById('telemetrySpentVal').textContent = formatRupiahDisplay(data.total_realisasi);
                
                const remEl = document.getElementById('telemetryRemainingVal');
                remEl.textContent = formatRupiahDisplay(data.remaining_budget);
                if (data.remaining_budget <= 0) {
                    remEl.className = 'fw-bold text-danger font-monospace';
                } else {
                    remEl.className = 'fw-bold text-success font-monospace';
                }

                const burnBadge = document.getElementById('telemetryBurnBadge');
                const pBar = document.getElementById('telemetryProgressBar');
                const burn = data.burn_rate || 0;

                burnBadge.textContent = `Serapan ${burn}%`;
                pBar.style.width = Math.min(100, burn) + '%';

                if (burn >= 100) {
                    burnBadge.className = 'badge bg-danger text-white';
                    pBar.className = 'progress-bar bg-danger';
                } else if (burn >= 80) {
                    burnBadge.className = 'badge bg-warning text-dark';
                    pBar.className = 'progress-bar bg-warning';
                } else {
                    burnBadge.className = 'badge bg-success text-white';
                    pBar.className = 'progress-bar bg-success';
                }
            }
        } else {
            activeProjectContract = null;
            if (telemetryCard) telemetryCard.classList.add('d-none');
        }
        checkBudgetGuard();
    }

    function checkBudgetGuard() {
        const finalInput = document.getElementById('realisasi_biaya_final');
        const alertEl = document.getElementById('telemetryOverBudgetAlert');
        const diffEl = document.getElementById('overBudgetDiff');
        if (!alertEl || !diffEl || !finalInput) return;

        const currentInput = parseFloat(finalInput.value) || 0;
        if (activeProjectContract && activeProjectContract.project_value > 0) {
            const rem = activeProjectContract.remaining_budget || 0;
            if (currentInput > rem) {
                const diff = currentInput - rem;
                diffEl.textContent = 'Lebih ' + formatRupiahDisplay(diff);
                alertEl.classList.remove('d-none');
            } else {
                alertEl.classList.add('d-none');
            }
        } else {
            alertEl.classList.add('d-none');
        }
    }

    document.getElementById('project_id')?.addEventListener('input', handleProjectAutoFill);
    document.getElementById('project_id')?.addEventListener('change', handleProjectAutoFill);
    document.getElementById('realisasi_biaya_final')?.addEventListener('input', checkBudgetGuard);

    const currencyPrefixes = {
        'IDR': 'Rp',
        'USD': '$',
        'EUR': '€',
        'SGD': 'S$',
        'JPY': '¥'
    };

    function updateFxBadgeText() {
        const textEl = document.getElementById('fxLiveText');
        if (textEl && fxMetaInfo) {
            textEl.textContent = fxMetaInfo.is_live 
                ? `Kurs Real-Time (Live) • Updated ${fxMetaInfo.last_updated}`
                : `Kurs Baseline (Offline) • ${fxMetaInfo.last_updated}`;
        }
    }

    function fetchLatestRates(forceRefresh = false) {
        const btn = document.getElementById('btnRefreshFx');
        if (btn) btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memuat...';

        fetch(`/api/exchange-rates?refresh=${forceRefresh ? 1 : 0}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.rates) {
                    liveExchangeRates = data.rates;
                    fxMetaInfo = {
                        last_updated: data.last_updated,
                        is_live: data.is_live,
                        source: data.source
                    };
                    updateFxBadgeText();
                    syncAndCalculateValues();
                }
            })
            .catch(err => console.warn('FX rate fetch error:', err))
            .finally(() => {
                if (btn) btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Update Kurs';
            });
    }

    document.getElementById('btnRefreshFx')?.addEventListener('click', () => fetchLatestRates(true));

    function syncAndCalculateValues() {
        const curSelect  = document.getElementById('currency');
        const origInput  = document.getElementById('realisasi_biaya_original');
        const idrInput   = document.getElementById('realisasi_biaya_idr');
        const finalInput = document.getElementById('realisasi_biaya_final');
        const prefixSpan = document.getElementById('origCurrencyPrefix');
        const helperSpan = document.getElementById('conversionHelper');

        if (!curSelect || !origInput) return;

        const cur = curSelect.value || 'IDR';
        const rate = liveExchangeRates[cur] || 1;

        if (prefixSpan) {
            prefixSpan.textContent = currencyPrefixes[cur] || cur;
        }

        const val = parseFloat(origInput.value);
        if (!isNaN(val) && val > 0) {
            const idrVal = Math.round(val * rate * 100) / 100;
            if (idrInput)   idrInput.value   = idrVal.toFixed(2);
            if (finalInput) finalInput.value = idrVal.toFixed(2);

            if (helperSpan) {
                if (cur !== 'IDR') {
                    const formattedRp = new Intl.NumberFormat('id-ID').format(Math.round(idrVal));
                    const formattedRate = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(rate);
                    helperSpan.innerHTML = `<span class="badge bg-success-subtle text-success border border-success-subtle mt-1 px-2.5 py-1" style="font-size:0.72rem; font-weight:700;"><i class="bi bi-broadcast me-1"></i>Konversi Live: Rp ${formattedRp} (1 ${cur} = Rp ${formattedRate})</span>`;
                } else {
                    helperSpan.innerHTML = '';
                }
            }
        } else {
            if (helperSpan) helperSpan.innerHTML = '';
        }
    }

    document.getElementById('realisasi_biaya_original')?.addEventListener('input', () => {
        syncAndCalculateValues();
        checkBudgetGuard();
    });
    document.getElementById('currency')?.addEventListener('change', () => {
        syncAndCalculateValues();
        checkBudgetGuard();
    });
    updateFxBadgeText();
    handleProjectAutoFill();

    ['project_id','item_biaya','satuan_kerja','pic','vendor','data_flag'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.addEventListener('blur', () => el.value = el.value.toUpperCase().trim());
    });

    document.getElementById('formEdit')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnUpdate');
        if(btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            btn.disabled = true;
        }
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush
