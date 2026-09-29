@extends('layouts.main')

@section('title', 'Detail Realisasi #' . $realisasi->id)
@section('page-title', 'Detail Realisasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}" class="text-decoration-none">Data Realisasi</a></li>
    <li class="breadcrumb-item active">#{{ $realisasi->id }}</li>
@endsection

@section('content')

{{-- PROJECT WORKFLOW STEPPER --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: #FFFFFF;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Status Alur Kerja Proyek (Workflow Progress)
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1">Proyek: {{ $realisasi->project_id }}</span>
        </div>

        @php
            $step1 = true;
            $step2 = !empty($basto);
            $step3 = $basto && in_array($basto->status, ['approved', 'completed']);
            $step4 = !empty($invoice);
            $step5 = $invoice && in_array(strtolower($invoice->payment_status), ['paid', 'lunas']);
        @endphp

        <div class="row g-2 text-center position-relative">
            <!-- Step 1 -->
            <div class="col">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div class="fw-bold fs-8 text-success">1. Realisasi Diinput</div>
                    <div class="text-secondary fs-9 mt-1">Data tercatat di DB</div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col">
                <div class="p-3 rounded-3 {{ $step2 ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-light border' }} h-100">
                    <div class="rounded-circle {{ $step2 ? 'bg-success text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($step2) <i class="bi bi-check-lg fs-5"></i> @else <span class="fs-7 fw-bold">2</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $step2 ? 'text-success' : 'text-secondary' }}">2. Pengajuan BASTO</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">
                        {{ $basto ? $basto->basto_number : 'Belum diajukan' }}
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col">
                @php
                    $isQcPass = $basto && $basto->qc_status === 'verified';
                    $isSmApproved = $step3;
                    $qcLabel = 'Menunggu BASTO';
                    if ($basto) {
                        if ($isSmApproved) {
                            $qcLabel = 'Disetujui SM';
                        } elseif ($basto->qc_status === 'verified') {
                            $qcLabel = 'Lolos QC • Menunggu SM';
                        } elseif ($basto->qc_status === 'revision_needed') {
                            $qcLabel = 'Perlu Revisi QC';
                        } else {
                            $qcLabel = 'Menunggu Inspeksi QC';
                        }
                    }
                @endphp
                <div class="p-3 rounded-3 {{ $step3 ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : ($basto ? ($basto->qc_status === 'revision_needed' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-warning bg-opacity-10 border border-warning') : 'bg-light border') }} h-100">
                    <div class="rounded-circle {{ $step3 ? 'bg-success text-white' : ($basto ? ($basto->qc_status === 'revision_needed' ? 'bg-danger text-white' : 'bg-warning text-dark') : 'bg-secondary text-white') }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($step3) <i class="bi bi-check-lg fs-5"></i> @elseif($basto && $basto->qc_status === 'revision_needed') <i class="bi bi-exclamation-triangle-fill fs-6"></i> @else <span class="fs-7 fw-bold">3</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $step3 ? 'text-success' : ($basto ? ($basto->qc_status === 'revision_needed' ? 'text-danger' : 'text-warning') : 'text-secondary') }}">3. Verifikasi &amp; Approval</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate" title="{{ $qcLabel }}">
                        {{ $qcLabel }}
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col">
                <div class="p-3 rounded-3 {{ $step4 ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-light border' }} h-100">
                    <div class="rounded-circle {{ $step4 ? 'bg-success text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($step4) <i class="bi bi-check-lg fs-5"></i> @else <span class="fs-7 fw-bold">4</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $step4 ? 'text-success' : 'text-secondary' }}">4. Penerbitan Invoice</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">
                        {{ $invoice ? $invoice->invoice_number : 'Belum terbit' }}
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="col">
                <div class="p-3 rounded-3 {{ $step5 ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : ($invoice ? 'bg-info bg-opacity-10 border border-info' : 'bg-light border') }} h-100">
                    <div class="rounded-circle {{ $step5 ? 'bg-success text-white' : ($invoice ? 'bg-info text-white' : 'bg-secondary text-white') }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($step5) <i class="bi bi-check-lg fs-5"></i> @else <span class="fs-7 fw-bold">5</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $step5 ? 'text-success' : ($invoice ? 'text-info' : 'text-secondary') }}">5. Pembayaran Lunas</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">
                        {{ $invoice ? strtoupper($invoice->payment_status) : 'Menunggu' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Kolom Kiri --}}
    <div class="col-lg-8">

        {{-- Informasi Project --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title">
                    <i class="bi bi-folder2-open text-primary me-2"></i>
                    Informasi Project
                </h6>
                <span class="badge rounded-pill" style="background: rgba(30,58,95,0.12); color: #1E3A5F; font-size: 0.7rem;">
                    ID #{{ $realisasi->id }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="detail-label">Project ID</div>
                        <div class="detail-value">
                            <code class="fs-6 text-primary">{{ $realisasi->project_id }}</code>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Service Manager</div>
                        <div class="detail-value">
                            @if($realisasi->kontrak && $realisasi->kontrak->service_manager)
                                <span class="badge rounded-pill px-2.5 py-1.5" style="background:#E0F2FE;color:#0369A1;font-weight:600;">
                                    <i class="bi bi-person-badge me-1"></i>{{ $realisasi->kontrak->service_manager }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Project Name</div>
                        <div class="detail-value fw-semibold">{{ $realisasi->project_name }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Transaksi --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-file-text text-success me-2"></i>Informasi Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="detail-label">Item Biaya</div>
                        <div class="detail-value">
                            <span class="badge bg-light text-dark border px-3 py-2">{{ $realisasi->item_biaya ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Satuan Kerja</div>
                        <div class="detail-value">{{ $realisasi->satuan_kerja ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">PIC</div>
                        <div class="detail-value fw-semibold">{{ $realisasi->pic ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Periode</div>
                        <div class="detail-value">
                            <span class="badge bg-primary px-3 py-2">{{ $realisasi->periode ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Tahun</div>
                        <div class="detail-value fw-bold fs-5 text-primary">{{ $realisasi->tahun ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Sifat</div>
                        <div class="detail-value">
                            <span class="badge bg-light text-secondary border">{{ $realisasi->sifat ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Keuangan --}}
        <div class="card mb-3" style="border-left: 4px solid #F59E0B;">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-cash-stack text-warning me-2"></i>Informasi Keuangan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-sm-4">
                        <div class="detail-label">Currency</div>
                        <div class="detail-value">
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background: #DBEAFE; color: #1E40AF; font-size: 0.8rem;">
                                {{ $realisasi->currency ?? 'IDR' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Realisasi Biaya Original</div>
                        <div class="detail-value number-display">
                            {{ ($realisasi->currency ?? 'IDR') === 'IDR' ? number_format($realisasi->realisasi_biaya_original ?? 0, 0, ',', '.') : number_format($realisasi->realisasi_biaya_original ?? 0, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="detail-label">Realisasi Biaya IDR</div>
                        <div class="detail-value number-display">
                            {{ number_format($realisasi->realisasi_biaya_idr ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="background: linear-gradient(135deg, #FEF3C7, #FFFBEB); border-radius: 10px; padding: 16px 20px; border: 1px solid #FCD34D;">
                            <div class="detail-label" style="color: #92400E;">
                                <i class="bi bi-star-fill me-1"></i>
                                Realisasi Biaya Final
                                <span class="badge ms-2" style="background: #F59E0B; font-size: 0.6rem;">Sumber Power BI</span>
                            </div>
                            <div class="fw-bold" style="font-size: 1.5rem; color: #92400E; font-variant-numeric: tabular-nums;">
                                Rp {{ number_format($realisasi->realisasi_biaya_final ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Evidence --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-paperclip text-secondary me-2"></i>Evidence</h6>
            </div>
            <div class="card-body">
                @if($realisasi->link_evidence)
                    @php
                        $isUrl = filter_var($realisasi->link_evidence, FILTER_VALIDATE_URL);
                    @endphp
                    @if($isUrl)
                        <a href="{{ $realisasi->link_evidence }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>
                            Buka Link Evidence
                        </a>
                        <div class="text-muted mt-2" style="font-size: 0.75rem; word-break: break-all;">
                            {{ $realisasi->link_evidence }}
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark text-muted"></i>
                            <span>{{ $realisasi->link_evidence }}</span>
                        </div>
                    @endif
                @else
                    <span class="text-muted fst-italic">Tidak ada evidence.</span>
                @endif
            </div>
        </div>

        {{-- Audit Log --}}
        @if($logs->isNotEmpty() || (isset($activityLogs) && $activityLogs->isNotEmpty()))
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-clock-history text-muted me-2"></i>Riwayat Perubahan &amp; Audit Log</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aksi</th>
                                <th>Pengguna / IP</th>
                                <th>Keterangan / Perubahan Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td style="font-size: 0.75rem;" class="text-muted">
                                    {{ $log->created_at?->format('d M Y H:i:s') }}
                                </td>
                                <td>
                                    @php
                                        $ac = match($log->action) {
                                            'CREATE' => 'badge bg-success',
                                            'UPDATE' => 'badge bg-warning text-dark',
                                            'DELETE' => 'badge bg-danger',
                                            'IMPORT' => 'badge bg-info',
                                            default  => 'badge bg-secondary',
                                        };
                                    @endphp
                                    <span class="{{ $ac }} rounded-pill">{{ $log->action }}</span>
                                </td>
                                <td style="font-size: 0.75rem;">
                                    {{ $log->user?->name ?? ($log->ip_address ?? 'Sistem') }}
                                </td>
                                <td style="font-size: 0.75rem;">
                                    @if($log->action === 'UPDATE' && $log->old_data)
                                        @php
                                            $changes = [];
                                            $newData = $log->new_data ?? [];
                                            $oldData = $log->old_data ?? [];
                                            $watchFields = ['realisasi_biaya_final','realisasi_biaya_idr','currency','status','vendor','periode'];
                                            foreach($watchFields as $f) {
                                                if(isset($oldData[$f]) && isset($newData[$f]) && $oldData[$f] != $newData[$f]) {
                                                    $oldV = is_numeric($oldData[$f]) ? number_format($oldData[$f], 0, ',', '.') : $oldData[$f];
                                                    $newV = is_numeric($newData[$f]) ? number_format($newData[$f], 0, ',', '.') : $newData[$f];
                                                    $changes[] = "<span class='text-secondary'>{$f}:</span> <del class='text-danger'>{$oldV}</del> &rarr; <strong class='text-success'>{$newV}</strong>";
                                                }
                                            }
                                        @endphp
                                        @if($changes)
                                            {!! implode('<br>', $changes) !!}
                                        @else
                                            Data diperbarui
                                        @endif
                                    @else
                                        {{ $log->action }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                            @if(isset($activityLogs))
                                @foreach($activityLogs as $act)
                                    @if(!$logs->contains('created_at', $act->created_at))
                                        <tr>
                                            <td style="font-size: 0.75rem;" class="text-muted">
                                                {{ $act->created_at?->format('d M Y H:i:s') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary rounded-pill">{{ $act->action }}</span>
                                            </td>
                                            <td style="font-size: 0.75rem;">
                                                {{ $act->user?->name ?? 'User #' . $act->user_id }}
                                            </td>
                                            <td style="font-size: 0.75rem;">
                                                {{ $act->description ?? 'Aktivitas sistem' }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4">

        {{-- Status Anggaran Proyek --}}
        @php
            $contractVal = $kontrak->project_value ?? 0;
            $totReal = $projectTotalRealisasi ?? 0;
            $pctReal = $contractVal > 0 ? round(($totReal / $contractVal) * 100, 1) : 0;
            $isOver = $contractVal > 0 && $totReal > $contractVal;
            $isNear = $contractVal > 0 && $totReal >= (0.9 * $contractVal) && !$isOver;
        @endphp
        <div class="card mb-3 shadow-sm border-0" style="border-radius:12px; border-left: 4px solid {{ $isOver ? '#EF4444' : ($isNear ? '#F59E0B' : '#10B981') }} !important;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fs-7 fw-bold"><i class="bi bi-pie-chart-fill text-primary me-1.5"></i>Status Anggaran Proyek</h6>
                @if($isOver)
                    <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size:0.65rem;">Overbudget</span>
                @elseif($isNear)
                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size:0.65rem;">&ge; 90% Limit</span>
                @else
                    <span class="badge bg-success rounded-pill px-2 py-0.5" style="font-size:0.65rem;">Aman</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-2.5">
                    <div class="detail-label">Nilai Kontrak Proyek</div>
                    <div class="detail-value fw-bold text-dark fs-7">
                        {{ $contractVal > 0 ? 'Rp ' . number_format($contractVal, 0, ',', '.') : 'Belum diisi' }}
                    </div>
                </div>
                <div class="mb-2.5">
                    <div class="detail-label">Akumulasi Realisasi Proyek</div>
                    <div class="detail-value fw-bold {{ $isOver ? 'text-danger' : 'text-primary' }} fs-7">
                        Rp {{ number_format($totReal, 0, ',', '.') }}
                    </div>
                </div>
                @if($contractVal > 0)
                    <div class="mb-1">
                        <div class="d-flex justify-content-between text-secondary fs-8 mb-1">
                            <span>Penyerapan Anggaran</span>
                            <span class="fw-bold">{{ $pctReal }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $isOver ? 'bg-danger' : ($isNear ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $pctReal) }}%;"></div>
                        </div>
                    </div>
                    @if($isOver)
                        <div class="alert alert-danger p-2 mb-0 mt-2.5 fs-8">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Realisasi melebihi kontrak sebesar <strong>Rp {{ number_format($totReal - $contractVal, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Status & Vendor Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-info-circle text-info me-2"></i>Status & Vendor</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Status</div>
                    @php
                        $sc = match(strtoupper($realisasi->status ?? '')) {
                            'PAID'        => 'badge-status-paid',
                            'UNPAID'      => 'badge-status-unpaid',
                            'PENDING'     => 'badge-status-pending',
                            'CANCEL'      => 'badge-status-cancel',
                            'PROSES'      => 'badge-status-proses',
                            'POPAY'       => 'badge-status-popay',
                            'IN PROGRES'  => 'badge-status-in-progres',
                            'WAIT INV'    => 'badge-status-wait-inv',
                            default       => 'badge-status-default',
                        };
                    @endphp
                    <span class="badge rounded-pill px-3 py-2 {{ $sc }}" style="font-size: 0.85rem;">
                        {{ $realisasi->status ?? '—' }}
                    </span>
                </div>
                <div class="mb-3">
                    <div class="detail-label">Vendor</div>
                    <div class="detail-value fw-semibold">{{ $realisasi->vendor ?? '—' }}</div>
                </div>
                <div class="mb-0">
                    <div class="detail-label">Data Flag</div>
                    <div class="detail-value">
                        @if($realisasi->data_flag)
                            <span class="badge bg-light text-dark border">{{ $realisasi->data_flag }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-database text-muted me-2"></i>Metadata</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="detail-label">Source Row</div>
                    <div class="detail-value">{{ $realisasi->source_row ?? '—' }}</div>
                </div>
                <div class="mb-2">
                    <div class="detail-label">Record Key</div>
                    <div style="font-family: monospace; font-size: 0.65rem; color: #94A3B8; word-break: break-all;">
                        {{ $realisasi->record_key ?? '—' }}
                    </div>
                </div>
                <hr class="my-2">
                <div class="mb-2">
                    <div class="detail-label">Dibuat</div>
                    <div class="detail-value">{{ $realisasi->created_at?->format('d M Y H:i') ?? '—' }}</div>
                </div>
                <div class="mb-0">
                    <div class="detail-label">Terakhir Diperbarui</div>
                    <div class="detail-value">{{ $realisasi->updated_at?->format('d M Y H:i') ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(auth()->user()->hasRole(['dmo', 'admin']))
                        <a href="{{ route('realisasi.edit', $realisasi->id) }}" class="btn btn-warning text-dark">
                            <i class="bi bi-pencil-square me-2"></i>Edit Data Ini
                        </a>
                    @endif
                    <a href="{{ route('realisasi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                    @if(auth()->user()->hasRole(['dmo', 'admin']))
                        <hr class="my-1">
                        <form id="form-delete-show"
                              action="{{ route('realisasi.destroy', $realisasi->id) }}"
                              method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-outline-danger w-100"
                                    onclick="confirmDelete('form-delete-show', '{{ addslashes($realisasi->project_id) }}')">
                                <i class="bi bi-trash me-2"></i>Hapus Data Ini
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 4px;
}
.detail-value {
    font-size: 0.9rem;
    color: var(--text-primary);
}
</style>
@endpush
