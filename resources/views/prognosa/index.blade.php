@extends('layouts.main')

@section('title', 'Monitoring Prognosa & Budget Alert')
@section('page-title', 'Monitoring Prognosa & Budget')

@section('breadcrumb')
    <li class="breadcrumb-item active">Monitoring Prognosa</li>
@endsection

@section('content')

{{-- Budget Alerts --}}
@if(count($alertProjects) > 0)
<div class="alert alert-warning alert-dismissible fade show no-autodismiss border-0 rounded-4 mb-4 d-flex gap-3" role="alert">
    <div class="flex-shrink-0"><i class="bi bi-exclamation-triangle-fill fs-4 text-warning mt-1"></i></div>
    <div>
        <strong class="d-block mb-1">⚠️ Budget Alert — {{ count($alertProjects) }} project membutuhkan perhatian!</strong>
        <ul class="mb-0 ps-3 small">
            @foreach($alertProjects as $alert)
                <li>{{ $alert }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="bi bi-cash-stack fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold text-muted small text-uppercase">Total Budget</div>
                    <div class="fs-6 fw-bold lh-1 mt-1">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10">
                    <i class="bi bi-graph-up-arrow fs-4 text-info"></i>
                </div>
                <div>
                    <div class="fw-bold text-muted small text-uppercase">Total Prognosa</div>
                    <div class="fs-6 fw-bold lh-1 mt-1">Rp {{ number_format($totalPrognosa, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="bi bi-wallet2 fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fw-bold text-muted small text-uppercase">Total Realisasi</div>
                    <div class="fs-6 fw-bold lh-1 mt-1">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 {{ $totalVarianceRp > 0 ? 'bg-danger bg-opacity-10' : 'bg-primary bg-opacity-10' }}">
                    <i class="bi {{ $totalVarianceRp > 0 ? 'bi-arrow-up-right text-danger' : 'bi-arrow-down-left text-primary' }} fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-muted small text-uppercase">Deviasi Prognosa</div>
                    <div class="fs-6 fw-bold lh-1 mt-1 {{ $totalVarianceRp > 0 ? 'text-danger' : 'text-primary' }}">
                        {{ $totalVarianceRp > 0 ? '+' : '' }}Rp {{ number_format($totalVarianceRp, 0, ',', '.') }}
                        <span class="fs-8 fw-normal">({{ $totalVariancePct }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="bi bi-exclamation-octagon fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold text-muted small text-uppercase">Over Budget</div>
                    <div class="fs-4 fw-bold lh-1 mt-1 {{ $overBudgetCnt > 0 ? 'text-danger' : 'text-success' }}">{{ $overBudgetCnt }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('prognosa.index') }}" class="row g-2 align-items-center">
            <div class="col-sm-3">
                <select name="tahun" class="form-select form-select-sm">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">Semua Project</option>
                    @foreach($projectIds as $pid)
                        <option value="{{ $pid }}" {{ $project_id === $pid ? 'selected' : '' }}>{{ $pid }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary btn-sm w-100" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('prognosa.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Monthly Trend Chart --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Tren Bulanan: Prognosa vs Realisasi {{ $tahun }}</h5>
    </div>
    <div class="card-body px-4 pb-4">
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>

{{-- Comparison Table & Gap Analysis --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi bi-table text-success me-2"></i>Matriks Perbandingan &amp; Analisis Deviasi (Gap Analysis)</h5>
            <small class="text-muted">Perbandingan nilai pagu kontrak, estimasi prognosa, dan deviasi pengeluaran aktual per project</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
            <button class="btn btn-sm btn-primary rounded-3 fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#updatePrognosaModal">
                <i class="bi bi-pencil-square me-1"></i> Update Estimasi Prognosa
            </button>
            @endif
            <span class="badge bg-primary bg-opacity-10 text-primary border px-3 py-2">{{ count($comparisonData) }} project</span>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 720px; overflow-y: auto;">
        <table class="table table-hover table-sticky-header align-middle mb-0" style="font-size: 0.82rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold border-bottom" style="font-size: 0.74rem;">
                <tr>
                    <th class="py-3 ps-4">Project</th>
                    <th class="text-end">Budget Kontrak</th>
                    <th class="text-end">Prognosa</th>
                    <th class="text-end">Realisasi</th>
                    <th class="text-end">Deviasi (Gap)</th>
                    <th class="text-end">Sisa Budget</th>
                    <th class="text-center" style="min-width:130px;">Serapan</th>
                    <th class="text-center pe-4">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparisonData as $row)
                @php
                    $barColor = $row['is_over_budget'] ? 'danger' : ($row['is_near_budget'] ? 'warning' : 'success');
                    $pct = min($row['serapan_pct'], 100);
                    $varRp = $row['variance_rp'] ?? 0;
                    $varPct = $row['variance_pct'] ?? 0;
                    $varStatus = $row['variance_status'] ?? 'exact';
                @endphp
                <tr style="{{ $row['is_over_budget'] ? 'background-color: rgba(220, 38, 38, 0.05);' : '' }}">
                    <td class="ps-4">
                        <div class="fw-semibold text-dark">{{ $row['project_id'] }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $row['project_name'] }}">{{ $row['project_name'] }}</div>
                    </td>
                    <td class="text-end">
                        @if($row['budget'] > 0)
                            <span class="text-dark">Rp {{ number_format($row['budget'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($row['prognosa'] > 0)
                            <span class="text-info fw-semibold">Rp {{ number_format($row['prognosa'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end fw-semibold text-dark">
                        Rp {{ number_format($row['realisasi'], 0, ',', '.') }}
                    </td>
                    <td class="text-end">
                        @if($row['prognosa'] > 0)
                            <div class="fw-bold font-monospace {{ $varRp > 0 ? 'text-danger' : ($varRp < 0 ? 'text-success' : 'text-muted') }}" style="font-size:0.8rem;">
                                {{ $varRp > 0 ? '+' : '' }}Rp {{ number_format($varRp, 0, ',', '.') }}
                            </div>
                            <span class="badge {{ $varRp > 0 ? 'bg-danger-subtle text-danger' : ($varRp < 0 ? 'bg-success-subtle text-success' : 'bg-light text-dark') }}" style="font-size: 0.65rem;">
                                {{ $varStatus === 'over' ? '▲ Over Forecast' : ($varStatus === 'under' ? '▼ Under Forecast' : '• Akurat') }} ({{ $varPct }}%)
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($row['budget'] > 0)
                            <span class="{{ $row['sisa_budget'] < 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                {{ $row['sisa_budget'] < 0 ? '-' : '' }}Rp {{ number_format(abs($row['sisa_budget']), 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 99px;">
                                <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $pct }}%; border-radius: 99px;"></div>
                            </div>
                            <span class="small fw-bold text-{{ $barColor }}" style="min-width: 38px; text-align: right;">{{ $row['serapan_pct'] }}%</span>
                        </div>
                        @if($row['is_over_budget'])
                            <span class="badge border rounded-pill px-2" style="font-size:0.65rem; background-color: rgba(220, 38, 38, 0.1); color: #dc2626; border-color: rgba(220, 38, 38, 0.2) !important;">OVER BUDGET</span>
                        @elseif($row['is_near_budget'])
                            <span class="badge border rounded-pill px-2" style="font-size:0.65rem; background-color: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.2) !important;">MENDEKATI LIMIT</span>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('prognosa.show', $row['project_id']) }}?tahun={{ $tahun }}"
                               class="btn btn-outline-primary rounded-start-3 px-2" title="Lihat Detail & Tren">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
                            <button type="button" class="btn btn-outline-info rounded-end-3 px-2 btn-open-prognosa-modal"
                                    data-project-id="{{ $row['project_id'] }}"
                                    data-project-name="{{ $row['project_name'] }}"
                                    title="Update Estimasi Prognosa Bulanan">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart-line fs-2 d-block mb-2"></i>
                        Belum ada data realisasi untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthlyTrend);
const labels    = monthlyData.map(d => d.periode);
const realisasi = monthlyData.map(d => d.realisasi);
const prognosa  = monthlyData.map(d => d.prognosa);

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Prognosa',
                data: prognosa,
                backgroundColor: 'rgba(99, 179, 237, 0.5)',
                borderColor: 'rgba(99, 179, 237, 1)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Realisasi',
                data: realisasi,
                backgroundColor: 'rgba(72, 187, 120, 0.7)',
                borderColor: 'rgba(72, 187, 120, 1)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});

// Event listener for opening modal with prefilled project
document.querySelectorAll('.btn-open-prognosa-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const pid = this.dataset.projectId;
        const pname = this.dataset.projectName;
        const select = document.getElementById('modal_project_id');
        if (select) {
            select.value = pid;
        }
        const nameInput = document.getElementById('modal_project_name');
        if (nameInput) {
            nameInput.value = pname;
        }
        const modalEl = document.getElementById('updatePrognosaModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });
});
</script>
@endpush

@push('modals')
@if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
<div class="modal fade" id="updatePrognosaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('prognosa.update-monthly') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Update Estimasi Prognosa Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Project</label>
                        <select name="project_id" id="modal_project_id" class="form-select rounded-3" required>
                            @foreach($comparisonData as $row)
                                <option value="{{ $row['project_id'] }}" data-name="{{ $row['project_name'] }}">
                                    {{ $row['project_id'] }} - {{ Str::limit($row['project_name'], 35) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="project_name" id="modal_project_name" value="">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label text-secondary small fw-bold">Periode Bulan</label>
                            <select name="periode" class="form-select rounded-3" required>
                                @foreach(['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $m)
                                    <option value="{{ $m }}" {{ strtoupper(date('F')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label text-secondary small fw-bold">Tahun</label>
                            <input type="number" name="tahun" class="form-control rounded-3" value="{{ $tahun ?? date('Y') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Nominal Estimasi Prognosa (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                            <input type="number" name="prognosa_biaya" class="form-control rounded-end-3" placeholder="Contoh: 150000000" min="0" required>
                        </div>
                        <small class="text-muted">Masukkan nominal perkiraan biaya yang direncanakan untuk bulan tersebut.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-secondary small fw-bold">Aktivitas / Keterangan (Opsional)</label>
                        <input type="text" name="activity" class="form-control rounded-3" placeholder="Misal: Biaya bulanan operasional & maintenance">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 shadow-sm"><i class="bi bi-check2-circle me-1"></i>Simpan Prognosa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endpush
