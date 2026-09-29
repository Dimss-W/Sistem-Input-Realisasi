@extends('layouts.main')

@section('title', 'Detail BASTO — ' . $basto->basto_number)
@section('page-title', 'Detail BASTO')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('basto.index') }}" class="text-decoration-none">BASTO</a></li>
    <li class="breadcrumb-item active">{{ $basto->basto_number }}</li>
@endsection

@section('content')
@php
    $badgeMap = [
        'draft'     => ['color' => 'secondary', 'icon' => 'bi-file-earmark',       'label' => 'Draft'],
        'submitted' => ['color' => 'primary',   'icon' => 'bi-send-fill',          'label' => 'Menunggu Review'],
        'approved'  => ['color' => 'success',   'icon' => 'bi-check-circle-fill',  'label' => 'Disetujui'],
        'rejected'  => ['color' => 'danger',    'icon' => 'bi-x-circle-fill',      'label' => 'Ditolak'],
    ];
    $statusInfo = $badgeMap[$basto->status] ?? ['color' => 'dark', 'icon' => 'bi-question-circle', 'label' => 'Unknown'];
    $hasRealisasi = \App\Models\Realisasi::where('project_id', $basto->project_id)->exists();
    $inv = \App\Models\Invoice::where('project_id', $basto->project_id)->latest()->first();
    $isBastoApproved = in_array($basto->status, ['approved']);
    $isInvPaid = $inv && in_array(strtolower($inv->payment_status), ['paid', 'lunas']);
@endphp

{{-- PROJECT WORKFLOW STEPPER --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: #FFFFFF;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase letter-spacing-05">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Status Alur Kerja Proyek (Workflow Progress)
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1">Proyek: {{ $basto->project_id }}</span>
        </div>

        <div class="row g-2 text-center position-relative">
            <div class="col">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div class="fw-bold fs-8 text-success">1. Input Realisasi</div>
                    <div class="text-secondary fs-9 mt-1">Data tercatat di DB</div>
                </div>
            </div>

            <div class="col">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div class="fw-bold fs-8 text-success">2. Pengajuan BASTO</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">{{ $basto->basto_number }}</div>
                </div>
            </div>

            <div class="col">
                <div class="p-3 rounded-3 {{ $isBastoApproved ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : ($basto->status == 'submitted' ? 'bg-warning bg-opacity-10 border border-warning' : ($basto->status == 'rejected' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light border')) }} h-100">
                    <div class="rounded-circle {{ $isBastoApproved ? 'bg-success text-white' : ($basto->status == 'submitted' ? 'bg-warning text-dark' : ($basto->status == 'rejected' ? 'bg-danger text-white' : 'bg-secondary text-white')) }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($isBastoApproved) <i class="bi bi-check-lg fs-5"></i> @elseif($basto->status == 'rejected') <i class="bi bi-x-lg fs-5"></i> @else <span class="fs-7 fw-bold">3</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $isBastoApproved ? 'text-success' : ($basto->status == 'submitted' ? 'text-warning' : ($basto->status == 'rejected' ? 'text-danger' : 'text-secondary')) }}">3. Approval SM</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">{{ ucfirst($basto->status) }}</div>
                </div>
            </div>

            <div class="col">
                <div class="p-3 rounded-3 {{ $inv ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-light border' }} h-100">
                    <div class="rounded-circle {{ $inv ? 'bg-success text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($inv) <i class="bi bi-check-lg fs-5"></i> @else <span class="fs-7 fw-bold">4</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $inv ? 'text-success' : 'text-secondary' }}">4. Penerbitan Invoice</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">{{ $inv ? $inv->invoice_number : 'Belum terbit' }}</div>
                </div>
            </div>

            <div class="col">
                <div class="p-3 rounded-3 {{ $isInvPaid ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : ($inv ? 'bg-info bg-opacity-10 border border-info' : 'bg-light border') }} h-100">
                    <div class="rounded-circle {{ $isInvPaid ? 'bg-success text-white' : ($inv ? 'bg-info text-white' : 'bg-secondary text-white') }} d-inline-flex align-items-center justify-content-center mb-2" style="width:32px; height:32px;">
                        @if($isInvPaid) <i class="bi bi-check-lg fs-5"></i> @else <span class="fs-7 fw-bold">5</span> @endif
                    </div>
                    <div class="fw-bold fs-8 {{ $isInvPaid ? 'text-success' : ($inv ? 'text-info' : 'text-secondary') }}">5. Pembayaran Lunas</div>
                    <div class="text-secondary fs-9 mt-1 text-truncate">{{ $inv ? strtoupper($inv->payment_status) : 'Menunggu' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Main Detail Card --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            {{-- Status Banner --}}
            <div class="card-header bg-{{ $statusInfo['color'] }} bg-opacity-10 border-0 px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi {{ $statusInfo['icon'] }} text-{{ $statusInfo['color'] }} fs-5"></i>
                    <span class="fw-bold text-{{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</span>
                    <span class="ms-auto text-muted small">{{ $basto->basto_number }}</span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Project ID</p>
                        <p class="fw-bold mb-0">{{ $basto->project_id }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Nama Project</p>
                        <p class="fw-bold mb-0">{{ $basto->project_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Pengaju (DMO)</p>
                        <p class="fw-semibold mb-0">{{ $basto->dmo ? $basto->dmo->name : '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Service Manager</p>
                        <p class="fw-semibold mb-0">{{ $basto->sm ? $basto->sm->name : '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Dibuat</p>
                        <p class="mb-0">{{ $basto->created_at ? $basto->created_at->format('d M Y, H:i') : '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Diajukan</p>
                        <p class="mb-0">{{ $basto->submitted_at ? $basto->submitted_at->format('d M Y, H:i') : '—' }}</p>
                    </div>
                    @if($basto->reviewed_at)
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Tanggal Review</p>
                        <p class="mb-0">{{ $basto->reviewed_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>

                {{-- ========================================================================= --}}
                {{-- INFORMASI KONTRAK, FINANSIAL & SUPPLY CHAIN --}}
                {{-- ========================================================================= --}}
                <div class="card border rounded-4 mb-4 overflow-hidden" style="background: #ffffff;">
                    <div class="card-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-file-earmark-ruled fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Informasi Finansial &amp; Kontrak (Cost &amp; Supply Chain)</h6>
                                <div class="text-muted" style="font-size: 0.75rem;">Parameter acuan anggaran, nomor kontrak, rentang tanggal dan mitra pengadaan</div>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                            <i class="bi bi-check-circle me-1"></i>Data Kontrak Terlampir
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Cost. No (Nomor Kontrak / SPK)</span>
                                    <span class="fw-bold text-dark fs-7">{{ $basto->cost_no ?: '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Supply Chain (Vendor / Rekanan Pengadaan)</span>
                                    <span class="fw-bold text-primary fs-7">{{ $basto->supply_chain ?: '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Cost. Value (Nilai Kontrak / Realisasi)</span>
                                    <span class="fw-bold text-success fs-6">
                                        {{ $basto->cost_value !== null ? 'Rp ' . number_format($basto->cost_value, 2, ',', '.') : '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Cost Based (Pagu Anggaran Acuan Dasar)</span>
                                    <span class="fw-bold text-info fs-6">
                                        {{ $basto->cost_based !== null ? 'Rp ' . number_format($basto->cost_based, 2, ',', '.') : '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Cost. Date (Start) / Mulai</span>
                                    <span class="fw-semibold text-dark">
                                        {{ $basto->cost_date_start ? \Carbon\Carbon::parse($basto->cost_date_start)->format('d F Y') : '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <span class="text-muted small d-block mb-1">Cost. Date (End) / Selesai</span>
                                    <span class="fw-semibold text-dark">
                                        {{ $basto->cost_date_end ? \Carbon\Carbon::parse($basto->cost_date_end)->format('d F Y') : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($basto->attachment_file)
                <div class="mb-4">
                    <p class="text-muted small mb-1">Berkas Fisik BASTO (Bertanda Tangan Basah)</p>
                    <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">{{ $basto->basto_number }}.pdf</div>
                                <div class="text-muted small">Format PDF Dokumen Fisik</div>
                            </div>
                        </div>
                        <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-sm btn-outline-danger fw-bold rounded-3 px-3 py-1.5">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka / Unduh PDF
                        </a>
                    </div>
                </div>
                @endif

                @if($basto->notes)
                <div class="mb-4">
                    <p class="text-muted small mb-1">Catatan / Keterangan</p>
                    <div class="border rounded-3 p-3 bg-light" style="white-space: pre-line;">{{ $basto->notes }}</div>
                </div>
                @endif

                @if($basto->status === 'rejected' && $basto->rejection_reason)
                <div class="alert alert-danger rounded-3">
                    <strong><i class="bi bi-x-circle-fill me-2"></i>Alasan Penolakan:</strong><br>
                    {{ $basto->rejection_reason }}
                </div>
                @endif

                {{-- ========================================================================= --}}
                {{-- STATUS & SERTIFIKASI PENGAWASAN MUTU (QUALITY CONTROL / QC) --}}
                {{-- ========================================================================= --}}
                <div class="card border rounded-4 mb-4 overflow-hidden" style="background: #ffffff;">
                    <div class="card-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Status Pengawasan Mutu (Quality Control)</h6>
                                <div class="text-muted" style="font-size: 0.75rem;">Verifikasi kelayakan teknis, kesesuaian SPK, dan bukti fisik operasional</div>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole(['osm_qc', 'admin']))
                            <button class="btn btn-primary btn-sm rounded-pill fw-bold px-3.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#qcModal">
                                <i class="bi bi-patch-check me-1"></i> {{ $basto->qc_status ? 'Perbarui Inspeksi QC' : 'Lakukan Inspeksi QC' }}
                            </button>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        @if($basto->qc_status === 'verified')
                            <div class="row align-items-center g-4">
                                {{-- FITUR 4: STEMPEL RESMI DIGITAL "QC PASSED" --}}
                                <div class="col-md-4 text-center">
                                    <div class="d-inline-flex flex-column align-items-center justify-content-center p-3 text-center position-relative shadow-sm"
                                         style="width: 190px; height: 190px; border-radius: 50%; border: 4px double #16a34a; background: radial-gradient(circle, #f0fdf4 20%, #dcfce7 100%); transform: rotate(-3deg); margin: 0 auto;">
                                        <div class="fw-bold text-success text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.12em;">★ PGNCOM — SMO ★</div>
                                        <i class="bi bi-patch-check-fill text-success my-1" style="font-size: 2.2rem; filter: drop-shadow(0 2px 4px rgba(22,163,74,0.3));"></i>
                                        <div class="text-success text-uppercase" style="font-size: 1.15rem; font-weight: 900; letter-spacing: 0.08em; line-height: 1;">QC PASSED</div>
                                        <div class="badge bg-success text-white my-1 px-2.5 py-0.5 rounded-pill" style="font-size: 0.68rem; font-family: monospace; letter-spacing: 0.05em;">
                                            {{ $basto->qc_verification_code }}
                                        </div>
                                        <div class="text-success text-opacity-90" style="font-size: 0.62rem; font-weight: 700;">
                                            VERIFIED: {{ $basto->qc_verified_at ? $basto->qc_verified_at->format('d/m/Y') : date('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- DETAIL VERIFIKASI & CHECKLIST CRITERIA --}}
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                        <span class="badge bg-success px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i> LOLOS VERIFIKASI MUTU & TEKNIS
                                        </span>
                                        <span class="text-muted small">Diinspeksi oleh: <b>{{ $basto->qcUser ? $basto->qcUser->name : 'Tim Quality Control' }}</b></span>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        @php
                                            $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                                        @endphp
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['admin_doc']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Administrasi & Tanda Tangan</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['admin_doc']) ? 'Lengkap & Sah' : 'Belum Memenuhi' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['spk_compliance']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Kesesuaian SPK & Kontrak</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['spk_compliance']) ? 'Sesuai SPK' : 'Deviasi SPK' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['baut_teknis']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">BAUT & Uji Fungsi Teknis</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['baut_teknis']) ? 'Lolos Uji Terima' : 'Belum Lolos' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['physical_evidence']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Bukti Fisik & Dokumentasi</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['physical_evidence']) ? 'Dokumentasi Valid' : 'Kurang Lengkap' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($basto->qc_notes)
                                        <div class="p-3 rounded-3 bg-light border">
                                            <div class="small fw-bold text-secondary mb-1"><i class="bi bi-chat-left-quote me-1 text-info"></i>Catatan Pengawasan Mutu QC:</div>
                                            <div class="small text-dark" style="white-space: pre-line;">{{ $basto->qc_notes }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        @elseif($basto->qc_status === 'revision_needed')
                            <div class="row align-items-center g-4">
                                {{-- STEMPEL REVISI QC --}}
                                <div class="col-md-4 text-center">
                                    <div class="d-inline-flex flex-column align-items-center justify-content-center p-3 text-center position-relative shadow-sm"
                                         style="width: 190px; height: 190px; border-radius: 50%; border: 4px double #dc2626; background: radial-gradient(circle, #fef2f2 20%, #fee2e2 100%); transform: rotate(-3deg); margin: 0 auto;">
                                        <div class="fw-bold text-danger text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.12em;">★ PGNCOM — SMO ★</div>
                                        <i class="bi bi-shield-x text-danger my-1" style="font-size: 2.2rem; filter: drop-shadow(0 2px 4px rgba(220,38,38,0.3));"></i>
                                        <div class="text-danger text-uppercase" style="font-size: 1.05rem; font-weight: 900; letter-spacing: 0.05em; line-height: 1;">REVISION NEEDED</div>
                                        <div class="badge bg-danger text-white my-1 px-2.5 py-0.5 rounded-pill" style="font-size: 0.68rem; font-family: monospace;">
                                            QC REJECTED
                                        </div>
                                        <div class="text-danger text-opacity-90" style="font-size: 0.62rem; font-weight: 700;">
                                            TGL: {{ $basto->qc_verified_at ? $basto->qc_verified_at->format('d/m/Y') : date('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- DETAIL REVISI & GATEKEEPER LOCK --}}
                                <div class="col-md-8">
                                    <div class="alert alert-danger rounded-3 mb-3 d-flex align-items-start gap-2">
                                        <i class="bi bi-lock-fill fs-4 text-danger flex-shrink-0"></i>
                                        <div>
                                            <strong class="d-block text-danger">Persetujuan Service Manager Diblokir (QC Gatekeeper)</strong>
                                            <span class="small">Dokumen BASTO ini belum memenuhi kriteria mutu teknis. DMO diwajibkan melengkapi atau memperbaiki dokumen fisik sebelum Service Manager dapat menyetujui.</span>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        @php
                                            $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                                        @endphp
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['admin_doc']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Administrasi & Tanda Tangan</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['admin_doc']) ? 'Lengkap & Sah' : 'Perlu Perbaikan' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['spk_compliance']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Kesesuaian SPK & Kontrak</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['spk_compliance']) ? 'Sesuai SPK' : 'Perlu Perbaikan' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['baut_teknis']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">BAUT & Uji Fungsi Teknis</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['baut_teknis']) ? 'Lolos Uji Terima' : 'Perlu Perbaikan' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                                                <i class="bi {{ !empty($chk['physical_evidence']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                                <div class="small">
                                                    <div class="fw-bold text-dark">Bukti Fisik & Dokumentasi</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ !empty($chk['physical_evidence']) ? 'Dokumentasi Valid' : 'Perlu Perbaikan' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($basto->qc_notes)
                                        <div class="p-3 rounded-3 bg-danger-subtle border border-danger-subtle text-danger">
                                            <div class="small fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Catatan Temuan / Revisi QC:</div>
                                            <div class="small text-dark" style="white-space: pre-line;">{{ $basto->qc_notes }}</div>
                                        </div>
                                    @endif

                                    @if(auth()->user()->hasRole(['dmo', 'admin']) && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                                    <div class="mt-3 text-end">
                                        <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#reuploadModal">
                                            <i class="bi bi-arrow-repeat me-1"></i> Tindak Lanjuti &amp; Unggah Berkas Revisi
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        @else
                            {{-- PENDING QC --}}
                            <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle p-2.5 bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Menunggu Pemeriksaan Mutu (QC Pending)</div>
                                        <div class="text-muted small">Dokumen BASTO ini belum diinspeksi oleh tim Quality Control.</div>
                                    </div>
                                </div>
                                @if(auth()->user()->hasRole(['osm_qc', 'admin']))
                                    <button class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#qcModal">
                                        <i class="bi bi-shield-check me-1"></i> Periksa Dokumen Sekarang
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 flex-wrap border-top pt-4">
                    <a href="{{ route('basto.index') }}" class="btn btn-outline-secondary rounded-3"><i class="bi bi-arrow-left me-1"></i>Kembali</a>

                    {{-- QC Inspection Button --}}
                    @if(auth()->user()->hasRole(['osm_qc', 'admin']))
                        <button class="btn btn-info text-white rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#qcModal">
                            <i class="bi bi-patch-check-fill me-1"></i> Inspeksi QC Mutu
                        </button>
                    @endif

                    {{-- DMO Re-upload Revision Button --}}
                    @if(auth()->user()->hasRole(['dmo', 'admin']) && $basto->qc_status === 'revision_needed' && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                        <button type="button" class="btn btn-warning text-dark fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#reuploadModal">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas Revisi ke QC
                        </button>
                    @endif

                    @if(auth()->user()->hasRole('dmo') && $basto->status === 'draft')
                        <a href="{{ route('basto.edit', $basto->id) }}" class="btn btn-outline-warning rounded-3"><i class="bi bi-pencil me-1"></i>Edit Draft</a>
                        <form action="{{ route('basto.submit', $basto->id) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-primary rounded-3 fw-bold" onclick="return confirm('Ajukan BASTO ini ke Service Manager?')">
                                <i class="bi bi-send me-1"></i>Ajukan ke SM
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->hasRole(['osm_service_manager', 'admin']) && $basto->status === 'submitted' && ($basto->sm_user_id === auth()->id() || auth()->user()->hasRole('admin')))
                        @php
                            $canSmApprove = auth()->user()->hasRole('admin') || $basto->qc_status === 'verified';
                        @endphp
                        @if($canSmApprove)
                            <button class="btn btn-success rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check2-circle me-1"></i>Setujui
                            </button>
                        @else
                            <button class="btn btn-outline-secondary rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal" title="Terkunci: Harus Lolos Verifikasi QC Mutu Terlebih Dahulu">
                                <i class="bi bi-lock-fill text-warning me-1"></i>Setujui (Terkunci QC)
                            </button>
                        @endif
                        <button class="btn btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i>Tolak
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2 text-primary"></i>Alur Status BASTO</h6>
            </div>
            <div class="card-body px-4">
                @php
                    $steps = [
                        ['status' => 'draft',     'label' => 'Draft',                'desc' => 'BASTO dibuat oleh DMO'],
                        ['status' => 'submitted', 'label' => 'Diajukan',             'desc' => 'Menunggu review Service Manager'],
                        ['status' => 'approved',  'label' => 'Disetujui',            'desc' => 'SM menyetujui BASTO'],
                        ['status' => 'rejected',  'label' => 'Ditolak',              'desc' => 'SM menolak BASTO'],
                    ];
                    $statusOrder = ['draft' => 0, 'submitted' => 1, 'approved' => 2, 'rejected' => 2];
                    $currentOrder = $statusOrder[$basto->status] ?? 0;
                @endphp
                <div class="position-relative ps-4 border-start border-2">
                    @foreach($steps as $i => $step)
                    @php
                        $stepOrder = $statusOrder[$step['status']] ?? $i;
                        $isActive = $basto->status === $step['status'];
                        $isPassed = $stepOrder < $currentOrder;
                        $dotColor = $isActive ? $statusInfo['color'] : ($isPassed ? 'success' : 'light');
                        $textColor = $isActive ? "text-{$statusInfo['color']}" : ($isPassed ? 'text-success' : 'text-muted');
                    @endphp
                    <div class="position-relative mb-4">
                        <div class="position-absolute" style="left: -1.45rem; top: 2px;">
                            <div class="rounded-circle border-2 d-flex align-items-center justify-content-center"
                                 style="width: 20px; height: 20px; background: {{ $isActive || $isPassed ? 'var(--bs-'.$dotColor.')' : '#e9ecef' }}; border: 3px solid white; box-shadow: 0 0 0 2px {{ $isActive ? 'var(--bs-'.$dotColor.')' : '#dee2e6' }}">
                            </div>
                        </div>
                        <div class="{{ $textColor }}">
                            <div class="fw-semibold" style="font-size: 0.82rem;">{{ $step['label'] }}</div>
                            <div class="small opacity-75">{{ $step['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal with QC Gatekeeper Protection --}}
@if(auth()->user()->hasRole(['osm_service_manager', 'admin']) && $basto->status === 'submitted' && ($basto->sm_user_id === auth()->id() || auth()->user()->hasRole('admin')))
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Setujui BASTO</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                {{-- FITUR 2: QC Gatekeeper Warning --}}
                @if($basto->qc_status === 'revision_needed')
                    <div class="alert alert-danger rounded-3 d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-shield-x fs-4 text-danger flex-shrink-0"></i>
                        <div>
                            <strong class="d-block text-danger">Persetujuan Diblokir (QC Gatekeeper)</strong>
                            <span class="small">Dokumen BASTO ini ditandai <b>Perlu Revisi Mutu</b> oleh tim QC. DMO harus menyelesaikan revisi berkas fisik sebelum SM dapat menyetujui.</span>
                            @if($basto->qc_notes)
                                <div class="mt-2 p-2 bg-white rounded border border-danger border-opacity-25 small text-dark">
                                    <b>Temuan QC:</b> {{ $basto->qc_notes }}
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($basto->qc_status === 'verified')
                    <div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-patch-check-fill fs-4 text-success flex-shrink-0"></i>
                        <div>
                            <strong class="d-block text-success">Lolos Verifikasi QC Mutu</strong>
                            <span class="small">Sertifikat: <code>{{ $basto->qc_verification_code }}</code> • Diverifikasi oleh {{ $basto->qcUser ? $basto->qcUser->name : 'Tim QC' }}</span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning rounded-3 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-shield-exclamation fs-4 text-warning flex-shrink-0"></i>
                        <div>
                            <strong class="d-block text-warning">Persetujuan Terkunci (QC Belum Verifikasi)</strong>
                            <span class="small">Dokumen BASTO ini belum diperiksa &amp; dinyatakan lolos oleh tim Quality Control. Harap tunggu verifikasi QC terlebih dahulu.</span>
                        </div>
                    </div>
                @endif

                <p>Anda akan menyetujui <strong>{{ $basto->basto_number }}</strong>. Aksi ini tidak dapat dibatalkan.</p>

                @if($basto->qc_status !== 'verified' && !auth()->user()->hasRole('admin'))
                    <div class="p-2.5 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 text-danger small fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> Tombol setujui dinonaktifkan karena BASTO belum lolos verifikasi QC.
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('basto.approve', $basto->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success rounded-3 fw-bold px-4" {{ ($basto->qc_status !== 'verified' && !auth()->user()->hasRole('admin')) ? 'disabled' : '' }}>
                        <i class="bi bi-check2-circle me-1"></i> Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak BASTO</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('basto.reject', $basto->id) }}" method="POST">@csrf @method('PATCH')
                <div class="modal-body"><p>Berikan alasan penolakan untuk <strong>{{ $basto->basto_number }}</strong>:</p>
                    <textarea name="rejection_reason" class="form-control rounded-3" rows="4" placeholder="Alasan penolakan..." required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal" type="button">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold px-4">Tolak BASTO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- FITUR 1: QC LIVE SPLIT-SCREEN DOCUMENT PREVIEWER & INSPECTION MODAL --}}
@if(auth()->user()->hasRole(['osm_qc', 'admin']))
<div class="modal fade" id="qcModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog {{ $basto->attachment_file ? 'modal-xl modal-fullscreen-xl-down' : 'modal-lg' }} modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <form action="{{ route('basto.qc-verify', $basto->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                    <div class="text-white">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-white text-info fw-bold px-2.5 py-1 rounded-pill">QC INSPECTION PREVIEWER</span>
                            <span class="fw-bold fs-6">{{ $basto->basto_number }}</span>
                        </div>
                        <div class="small text-white text-opacity-85">{{ $basto->project_name }} ({{ $basto->project_id }})</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-3 p-lg-4">
                    @if($basto->attachment_file)
                        <div class="row g-4">
                            {{-- KOLOM KIRI: LIVE IN-APP PREVIEW DOKUMEN BASTO --}}
                            <div class="col-lg-6 col-xl-7">
                                <div class="card border rounded-3 overflow-hidden shadow-sm h-100 d-flex flex-column" style="min-height: 580px; background: #0f172a;">
                                    <div class="card-header py-2 px-3 bg-dark text-white d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                            <span class="small fw-semibold text-truncate text-white" style="max-width: 250px;">
                                                {{ basename($basto->attachment_file) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-xs btn-outline-light py-1 px-2 rounded-2 fw-semibold" style="font-size: 0.75rem;" title="Buka di Tab Baru">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Fullscreen
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 flex-grow-1 position-relative" style="background: #1e293b; min-height: 520px;">
                                        @php
                                            $ext = strtolower(pathinfo($basto->attachment_file, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                            <div class="d-flex align-items-center justify-content-center h-100 p-3" style="min-height: 530px; max-height: 650px; overflow: auto;">
                                                <img src="{{ route('basto.attachment', $basto->id) }}" alt="Bukti Fisik BASTO" class="img-fluid rounded shadow" style="max-height: 600px; object-fit: contain;">
                                            </div>
                                        @else
                                            <iframe src="{{ route('basto.attachment', $basto->id) }}#toolbar=1&navpanes=0" class="w-100 h-100 border-0 rounded-bottom" style="min-height: 550px;" title="Preview Berkas BASTO"></iframe>
                                        @endif
                                    </div>
                                    <div class="card-footer py-1.5 px-3 bg-dark text-muted d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                                        <span class="text-white-50"><i class="bi bi-eye-fill text-info me-1"></i> Live In-App Document Viewer</span>
                                        <span class="text-white-50">Pengaju: <b>{{ $basto->dmo ? $basto->dmo->name : 'DMO' }}</b> &bull; SM: <b>{{ $basto->sm ? $basto->sm->name : 'SM' }}</b></span>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: QC CHECKLIST & VERIFIKASI MUTU --}}
                            <div class="col-lg-6 col-xl-5 d-flex flex-column">
                                <div class="h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        {{-- Standarisasi Checklist 4 Kriteria --}}
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label text-dark fw-bold small mb-0">
                                                    <i class="bi bi-check2-square text-primary me-1"></i> 1. Standarisasi Mutu Teknis
                                                </label>
                                                <button type="button" class="btn btn-link text-primary p-0 small text-decoration-none fw-bold" onclick="checkAllQcItems('show_{{ $basto->id }}')">
                                                    <i class="bi bi-check-all me-1"></i> Centang Semua (100%)
                                                </button>
                                            </div>
                                            
                                            @php
                                                $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                                            @endphp
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                        <div class="form-check">
                                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_show" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_admin_show">
                                                                1. Kelengkapan Administrasi &amp; Ttd
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem;">Kop surat, nomor BASTO, ttd basah/digital DMO, vendor &amp; tanggal sah.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                        <div class="form-check">
                                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_show" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_spk_show">
                                                                2. Kesesuaian SPK &amp; Kontrak
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem;">Nomor project, scope pekerjaan, dan jangka waktu sinkron dengan SPK.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                        <div class="form-check">
                                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_show" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_baut_show">
                                                                3. BAUT &amp; Uji Fungsi Teknis
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem;">Berita Acara Uji Terima teknis terpenuhi tanpa kendala operasional.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                                        <div class="form-check">
                                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_show" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold text-dark small cursor-pointer" for="chk_phys_show">
                                                                4. Bukti Fisik Lapangan
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem;">Foto instalasi fisik / aktivitas operasional terlampir jelas dan valid.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Status Keputusan Mutu --}}
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold small mb-1">
                                                <i class="bi bi-shield-check text-success me-1"></i> 2. Keputusan Verifikasi Mutu
                                            </label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                                        <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_show" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                                        <label class="form-check-label fw-bold text-success small cursor-pointer mb-0" for="status_verified_show">
                                                            <i class="bi bi-patch-check-fill me-1"></i> VERIFIED (Lolos)
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                                        <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_show" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                                        <label class="form-check-label fw-bold text-danger small cursor-pointer mb-0" for="status_revision_show">
                                                            <i class="bi bi-exclamation-octagon-fill me-1"></i> REVISI (Kunci)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Template Catatan Temuan Cepat --}}
                                        <div class="mb-2">
                                            <label class="form-label text-dark fw-bold small mb-1">
                                                <i class="bi bi-pencil-square text-secondary me-1"></i> 3. Catatan Inspeksi &amp; Temuan
                                            </label>
                                            <div class="mb-2 p-2 rounded-3 border bg-light" style="font-size: 0.72rem;">
                                                <span class="text-secondary fw-bold"><i class="bi bi-lightning-charge-fill text-warning"></i> Template Cepat:</span>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('show_{{ $basto->id }}', 'Berkas BASTO fisik belum ditandatangani basah oleh pihak berwenang.')">+ Ttd Basah</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('show_{{ $basto->id }}', 'Lampiran Berita Acara Uji Terima (BAUT) teknis belum disertakan.')">+ BAUT Kurang</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('show_{{ $basto->id }}', 'Foto dokumentasi bukti fisik lapangan buram / kurang representatif.')">+ Foto Buram</button>
                                                    <button type="button" class="btn btn-outline-success btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="appendQcNote('show_{{ $basto->id }}', 'Pemeriksaan fisik dan dokumen lengkap, pengujian teknis operasional lolos 100% tanpa deviasi.')">+ Lolos 100%</button>
                                                </div>
                                            </div>
                                            <textarea id="qcNotesArea_show_{{ $basto->id }}" name="qc_notes" rows="2" class="form-control form-control-sm rounded-3" placeholder="Tuliskan catatan teknis pemeriksaan fisik/berkas...">{{ $basto->qc_notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- NO ATTACHMENT FALLBACK --}}
                        <div class="p-4 rounded-3 text-center bg-light border mb-4">
                            <i class="bi bi-file-earmark-x text-muted display-4 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Berkas Fisik Diunggah</h6>
                            <span class="small text-muted">DMO belum mengunggah berkas fisik bertanda tangan untuk dokumen ini. Tim QC dapat memberikan evaluasi atau meminta perbaikan berkas.</span>
                        </div>

                        {{-- Standarisasi Checklist 4 Kriteria --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label text-dark fw-bold small mb-0">
                                    <i class="bi bi-check2-square text-primary me-1"></i> Standarisasi Mutu Teknis
                                </label>
                                <button type="button" class="btn btn-link text-primary p-0 small text-decoration-none fw-bold" onclick="checkAllQcItems('show_{{ $basto->id }}')">
                                    Centang Semua (100%)
                                </button>
                            </div>
                            @php
                                $chk = is_array($basto->qc_checklist) ? $basto->qc_checklist : [];
                            @endphp
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                        <div class="form-check">
                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[admin_doc]" value="1" id="chk_admin_show" {{ !empty($chk['admin_doc']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark small" for="chk_admin_show">1. Administrasi &amp; Ttd</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                        <div class="form-check">
                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[spk_compliance]" value="1" id="chk_spk_show" {{ !empty($chk['spk_compliance']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark small" for="chk_spk_show">2. Kesesuaian SPK &amp; Kontrak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                        <div class="form-check">
                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[baut_teknis]" value="1" id="chk_baut_show" {{ !empty($chk['baut_teknis']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark small" for="chk_baut_show">3. BAUT &amp; Uji Fungsi</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                                        <div class="form-check">
                                            <input class="form-check-input qc-chk-show_{{ $basto->id }}" type="checkbox" name="qc_checklist[physical_evidence]" value="1" id="chk_phys_show" {{ !empty($chk['physical_evidence']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark small" for="chk_phys_show">4. Bukti Fisik Lapangan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold small mb-1">Keputusan Verifikasi Mutu</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                        <input class="form-check-input m-0" type="radio" name="qc_status" id="status_verified_show" value="verified" {{ $basto->qc_status === 'verified' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-success small cursor-pointer" for="status_verified_show">VERIFIED (Lolos)</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-2">
                                        <input class="form-check-input m-0" type="radio" name="qc_status" id="status_revision_show" value="revision_needed" {{ $basto->qc_status === 'revision_needed' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-danger small cursor-pointer" for="status_revision_show">REVISI (Kunci)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark fw-bold small mb-1">Catatan Inspeksi</label>
                            <textarea id="qcNotesArea_show_{{ $basto->id }}" name="qc_notes" rows="2" class="form-control rounded-3" placeholder="Masukkan catatan teknis...">{{ $basto->qc_notes }}</textarea>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Hasil Verifikasi QC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL UNGGAH BERKAS REVISI (DMO SHOW VIEW) --}}
@if(auth()->user()->hasRole(['dmo', 'admin']) && $basto->qc_status === 'revision_needed' && ($basto->dmo_user_id === auth()->id() || auth()->user()->hasRole('admin')))
<div class="modal fade" id="reuploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <form action="{{ route('basto.reupload-revision', $basto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                    <div class="text-white">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-white text-warning fw-bold px-2.5 py-1 rounded-pill">TINDAK LANJUT REVISI DMO</span>
                            <span class="fw-bold fs-6">{{ $basto->basto_number }}</span>
                        </div>
                        <div class="small text-white text-opacity-85">{{ $basto->project_name }} ({{ $basto->project_id }})</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- QC Finding Alert --}}
                    <div class="alert alert-danger rounded-3 mb-4 border border-danger border-opacity-25" style="background: #fef2f2;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-0.5"></i>
                            <div class="w-100">
                                <div class="fw-bold text-danger mb-1">Catatan Hasil Pemeriksaan Mutu (QC):</div>
                                <div class="text-dark small mb-2" style="white-space: pre-line;">{{ $basto->qc_notes ?: 'Dokumen fisik BASTO belum memenuhi standar atau terdapat ketidaksesuaian administrasi/teknis.' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    Diperiksa oleh: <b>{{ $basto->qcUser ? $basto->qcUser->name : 'Tim Quality Control' }}</b> &bull; {{ $basto->qc_verified_at ? $basto->qc_verified_at->format('d/m/Y H:i') : 'Tanggal tidak tersedia' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Berkas Saat Ini --}}
                    @if($basto->attachment_file)
                    <div class="mb-3 p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.7rem;">Berkas Terunggah Saat Ini (Akan digantikan):</span>
                                <span class="fw-bold text-dark font-monospace">{{ basename($basto->attachment_file) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('basto.attachment', $basto->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-pill" style="font-size:0.75rem;">
                            <i class="bi bi-eye me-1"></i>Lihat Berkas Lama
                        </a>
                    </div>
                    @endif

                    {{-- Form Unggah Revisi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="bi bi-cloud-arrow-up-fill text-primary me-1"></i> Unggah Berkas Fisik Pengganti / Revisi <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="attachment_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                        <div class="form-text text-muted" style="font-size:0.75rem;">Format diperbolehkan: PDF, JPG, PNG, WebP. Ukuran maks: 10MB. Pastikan tanda tangan & cap basah terbaca jelas.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="bi bi-pencil-square text-secondary me-1"></i> Catatan Tindak Lanjut dari DMO (Opsional)
                        </label>
                        <textarea name="revision_notes" class="form-control" rows="3" placeholder="Jelaskan perbaikan yang telah dilakukan berdasarkan temuan QC..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 py-3 px-4 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3 px-4 shadow-sm">
                        <i class="bi bi-send-check-fill me-1"></i> Simpan &amp; Kirim Ulang ke QC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// FITUR 1: Centang semua checklist QC
function checkAllQcItems(id) {
    document.querySelectorAll(`.qc-chk-${id}`).forEach(cb => cb.checked = true);
    const verifiedRadio = document.getElementById(`status_verified_show`);
    if (verifiedRadio) verifiedRadio.checked = true;
}

// FITUR 4: Tambahkan template temuan cepat ke textarea catatan QC
function appendQcNote(id, text) {
    const textarea = document.getElementById(`qcNotesArea_${id}`);
    if (textarea) {
        if (textarea.value.trim().length > 0) {
            textarea.value += '\n• ' + text;
        } else {
            textarea.value = '• ' + text;
        }
    }
}
</script>
@endpush

@endsection
