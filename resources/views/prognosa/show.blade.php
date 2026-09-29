@extends('layouts.main')

@section('title', 'Detail Prognosa — ' . $projectId)
@section('page-title', 'Detail Monitoring Project')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('prognosa.index') }}" class="text-decoration-none">Monitoring Prognosa</a></li>
    <li class="breadcrumb-item active">{{ $projectId }}</li>
@endsection

@section('content')

{{-- Header Info --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="text-muted small mb-1">Project</div>
                <h4 class="fw-bold mb-0">{{ $kontrak ? $kontrak->project_name : $projectId }}</h4>
                @if($kontrak)
                <div class="text-muted small mt-2">
                    <i class="bi bi-person me-1"></i>SM: <strong>{{ $kontrak->service_manager ?? '—' }}</strong>
                    &bull; <i class="bi bi-briefcase me-1"></i>Client: <strong>{{ $kontrak->project_client ?? '—' }}</strong>
                    &bull; <i class="bi bi-calendar me-1"></i>{{ $kontrak->start_date ? date('d/m/Y', strtotime($kontrak->start_date)) : '—' }}
                    — {{ $kontrak->end_date ? date('d/m/Y', strtotime($kontrak->end_date)) : '—' }}
                </div>
                @endif
                @if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
                <button class="btn btn-sm btn-primary rounded-3 fw-bold px-3 mt-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#updatePrognosaModalShow">
                    <i class="bi bi-pencil-square me-1"></i> Update Estimasi Prognosa
                </button>
                @endif
            </div>
            <div class="col-md-6">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="text-muted small">Budget Kontrak</div>
                        <div class="fw-bold {{ $budget > 0 ? 'text-dark' : 'text-muted' }}">
                            {{ $budget > 0 ? 'Rp ' . number_format($budget, 0, ',', '.') : '—' }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Total Realisasi</div>
                        <div class="fw-bold text-success">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Serapan</div>
                        <div class="fw-bold {{ $serapanPct >= 100 ? 'text-danger' : ($serapanPct >= 85 ? 'text-warning' : 'text-success') }}">
                            {{ $serapanPct }}%
                        </div>
                    </div>
                </div>
                @if($budget > 0)
                <div class="progress mt-2" style="height: 10px; border-radius: 99px;">
                    <div class="progress-bar bg-{{ $serapanPct >= 100 ? 'danger' : ($serapanPct >= 85 ? 'warning' : 'success') }}"
                         style="width: {{ min($serapanPct, 100) }}%; border-radius: 99px;"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Monthly Comparison Chart --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Perbandingan Bulanan — Prognosa vs Realisasi {{ $tahun }}</h5>
    </div>
    <div class="card-body px-4 pb-4">
        <canvas id="monthlyChart" height="70"></canvas>
    </div>
</div>

<div class="row g-3">
    {{-- Realisasi Detail Table --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-check2-circle text-success me-2"></i>Detail Realisasi per Item</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                    <thead class="table-light text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">
                        <tr>
                            <th class="py-3 ps-4">Item Biaya</th>
                            <th>Periode</th>
                            <th class="text-end pe-4">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($realisasiItems as $item)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $item->item_biaya ?: '—' }}</td>
                            <td>{{ $item->periode }}</td>
                            <td class="text-end pe-4 text-success fw-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data realisasi.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="fw-bold bg-success bg-opacity-5">
                        <tr>
                            <td colspan="2" class="ps-4 py-2">Total Realisasi</td>
                            <td class="text-end pe-4 text-success">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Prognosa Detail Table --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow text-info me-2"></i>Detail Prognosa per Activity</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                    <thead class="table-light text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">
                        <tr>
                            <th class="py-3 ps-4">Activity</th>
                            <th>Periode</th>
                            <th class="text-end pe-4">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prognosaItems as $item)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $item->activity ?: '—' }}</td>
                            <td>{{ $item->periode }}</td>
                            <td class="text-end pe-4 text-info fw-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data prognosa.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="fw-bold bg-info bg-opacity-5">
                        <tr>
                            <td colspan="2" class="ps-4 py-2">Total Prognosa</td>
                            <td class="text-end pe-4 text-info">Rp {{ number_format($totalPrognosa, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
</div>

{{-- Prognosa Reconciliation & Conflict Handling Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-shield-check text-primary me-2"></i>Sumber Data & Rekonsiliasi Prognosa (Estimasi SM vs PO Pengadaan Reza)</h6>
            <p class="text-secondary small mb-0">Memantau asal proyeksi biaya (input manual SM vs realisasi PO pengadaan) serta opsi sinkronisasi otomatis agar tidak terjadi selisih.</p>
        </div>
        @if(isset($availablePOs) && $availablePOs->count() > 0)
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
            <i class="bi bi-cart-check me-1"></i>{{ $availablePOs->count() }} PO Terbit di {{ $tahun }}
        </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
            <thead class="table-light text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">
                <tr>
                    <th class="py-3 ps-4">Periode</th>
                    <th>Aktivitas / Rincian</th>
                    <th class="text-end">Nominal Proyeksi</th>
                    <th>Sumber Data</th>
                    <th>Info PO Pengadaan Terkait</th>
                    <th>Status Rekonsiliasi</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prognosaRecords as $rec)
                <tr>
                    <td class="ps-4 fw-bold text-dark">{{ $rec->periode }} {{ $rec->tahun_normalized ?? $rec->tahun_original ?? $tahun }}</td>
                    <td>
                        <div class="fw-semibold">{{ $rec->activity ?: 'Estimasi Biaya Operasional' }}</div>
                        @if($rec->keterangan)
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $rec->keterangan }}</small>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-primary">
                        Rp {{ number_format($rec->prognosa_biaya, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($rec->data_source === 'procurement_po' || $rec->po_id)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                <i class="bi bi-cart-check me-1"></i>Sumber: PO Pengadaan (Reza)
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                <i class="bi bi-person-badge me-1"></i>Sumber: Estimasi SM
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($rec->purchaseOrder)
                            <div class="fw-semibold text-dark">#{{ $rec->purchaseOrder->po_number }}</div>
                            <small class="text-muted">
                                {{ $rec->purchaseOrder->vendor_name ?: 'Vendor' }} &bull; TOP: {{ $rec->purchaseOrder->term_of_payment ? $rec->purchaseOrder->term_of_payment . ' Hari' : '—' }} (Rp {{ number_format($rec->purchaseOrder->po_amount, 0, ',', '.') }})
                            </small>
                        @elseif($rec->po_id)
                            <span class="text-muted">PO #{{ $rec->po_id }}</span>
                        @else
                            <span class="text-muted fst-italic">— Tidak ada PO tertaut —</span>
                        @endif
                    </td>
                    <td>
                        @if($rec->is_overridden)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i>Telah Direkonsiliasi (PO)
                            </span>
                        @elseif($rec->data_source === 'procurement_po')
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">
                                <i class="bi bi-link-45deg me-1"></i>Sinkron dari PO
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">
                                Estimasi Awal SM
                            </span>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        @if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
                            @if($rec->purchaseOrder)
                                @if(!$rec->is_overridden)
                                    <form action="{{ route('prognosa.reconcile', $rec->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="use_po">
                                        <input type="hidden" name="po_id" value="{{ $rec->purchaseOrder->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm" title="Terapkan nilai aktual PO Reza ke Prognosa">
                                            <i class="bi bi-arrow-repeat me-1"></i>Gunakan Nilai PO
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('prognosa.reconcile', $rec->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reset_sm">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold" title="Kembalikan status ke Estimasi SM">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke SM
                                        </button>
                                    </form>
                                @endif
                            @elseif(isset($availablePOs) && $availablePOs->where('prognosa_periode', $rec->periode)->count() > 0)
                                @php
                                    $matchingPo = $availablePOs->where('prognosa_periode', $rec->periode)->first();
                                @endphp
                                <form action="{{ route('prognosa.reconcile', $rec->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="use_po">
                                    <input type="hidden" name="po_id" value="{{ $matchingPo->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm" title="Tautkan dan terapkan PO #{{ $matchingPo->po_number }}">
                                        <i class="bi bi-link me-1"></i>Pakai PO #{{ $matchingPo->po_number }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-1"></i>Belum ada data rekaman rincian prognosa untuk tahun {{ $tahun }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('prognosa.index') }}?tahun={{ $tahun }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Monitoring
    </a>
    @if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
    <button class="btn btn-primary rounded-3 fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#updatePrognosaModalShow">
        <i class="bi bi-pencil-square me-1"></i> Update Estimasi Bulan Berjalan
    </button>
    @endif
</div>

@endsection

@push('modals')
@if(auth()->user()->hasRole(['osm_service_manager', 'admin']))
<div class="modal fade" id="updatePrognosaModalShow" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('prognosa.update-monthly') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <input type="hidden" name="project_name" value="{{ $kontrak ? $kontrak->project_name : $projectId }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Update Estimasi Prognosa — {{ $projectId }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
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
                        <small class="text-muted">Masukkan nominal rencana biaya yang diproyeksikan untuk bulan tersebut.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-secondary small fw-bold">Aktivitas / Keterangan (Opsional)</label>
                        <input type="text" name="activity" class="form-control rounded-3" placeholder="Misal: Operasional bulanan & maintenance site">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const data   = @json($monthlyComparison);
const labels = data.map(d => d.periode.substring(0,3));

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Prognosa',
                data: data.map(d => d.prognosa),
                borderColor: 'rgba(99, 179, 237, 1)',
                backgroundColor: 'rgba(99, 179, 237, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
            },
            {
                label: 'Realisasi',
                data: data.map(d => d.realisasi),
                borderColor: 'rgba(72, 187, 120, 1)',
                backgroundColor: 'rgba(72, 187, 120, 0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: { label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID') }
            }
        },
        scales: {
            y: {
                ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
