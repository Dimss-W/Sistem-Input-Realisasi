@extends('layouts.main')

@section('title', 'Master Data Terpusat')
@section('page-title', 'Master Data Vendor & Client')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active"><i class="bi bi-database-fill me-1"></i>Master Data</li>
@endsection

@section('content')

{{-- Header Banner --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0A192F 0%, #1E3A8A 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="bi bi-diagram-3-fill text-warning"></i> Standardisasi Data Operasional
                </div>
                <h4 class="fw-bold text-white mb-1">Pusat Kelola Master Data</h4>
                <p class="text-white text-opacity-80 mb-0 small" style="max-width: 600px;">
                    Standarisasi nama rekanan vendor dan entitas client/pelanggan untuk menjaga keakuratan laporan finansial dan visualisasi analitik.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <form action="{{ route('admin.master-data.sync') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm fw-bold px-3 py-2 rounded-3 shadow-sm" onclick="return confirm('Mulai sinkronisasi otomatis nama Vendor dan Client unik dari riwayat transaksi Realisasi?')">
                        <i class="bi bi-arrow-repeat text-primary me-1"></i>
                        <span>Auto-Sync dari Realisasi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabs & Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'projects' ? 'active' : '' }} fw-bold d-flex align-items-center gap-2" 
                       href="{{ route('admin.master-data.index', ['tab' => 'projects']) }}">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Master Proyek &amp; Pagu</span>
                        <span class="badge {{ $tab === 'projects' ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill">
                            {{ $totalProjects }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'vendors' ? 'active' : '' }} fw-bold d-flex align-items-center gap-2" 
                       href="{{ route('admin.master-data.index', ['tab' => 'vendors']) }}">
                        <i class="bi bi-building"></i>
                        <span>Master Vendor</span>
                        <span class="badge {{ $tab === 'vendors' ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill">
                            {{ $totalVendors }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'clients' ? 'active' : '' }} fw-bold d-flex align-items-center gap-2" 
                       href="{{ route('admin.master-data.index', ['tab' => 'clients']) }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Master Client</span>
                        <span class="badge {{ $tab === 'clients' ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill">
                            {{ $totalClients }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'signers' ? 'active' : '' }} fw-bold d-flex align-items-center gap-2" 
                       href="{{ route('admin.master-data.index', ['tab' => 'signers']) }}">
                        <i class="bi bi-pen-fill text-warning"></i>
                        <span>Pejabat Penandatangan</span>
                        <span class="badge {{ $tab === 'signers' ? 'bg-warning text-dark' : 'bg-light text-dark' }} rounded-pill" style="font-size: 0.68rem;">
                            Laporan
                        </span>
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                @if($tab === 'projects')
                    @if(auth()->user()->hasRole('dmo'))
                        <button type="button" class="btn btn-primary btn-sm fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddProject">
                            <i class="bi bi-plus-lg"></i>
                            <span>Daftarkan Proyek Baru</span>
                        </button>
                    @else
                        <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small">
                            <i class="bi bi-shield-lock text-primary me-1"></i> Input Proyek Baru Dikelola Khusus oleh DMO
                        </span>
                    @endif
                @elseif($tab === 'vendors')
                    <button type="button" class="btn btn-primary btn-sm fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#modalAddVendor">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Vendor
                    </button>
                @elseif($tab === 'clients')
                    <button type="button" class="btn btn-primary btn-sm fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#modalAddClient">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Client
                    </button>
                @else
                    <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small">
                        <i class="bi bi-info-circle text-primary me-1"></i> Sinkron Otomatis ke Laporan Cetak Resmi
                    </span>
                @endif
            </div>
        </div>

        {{-- Search Input --}}
        @if($tab !== 'signers')
        <form action="{{ route('admin.master-data.index') }}" method="GET" class="mb-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="input-group input-group-sm" style="max-width: 420px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" 
                       placeholder="Cari {{ $tab === 'projects' ? 'Project ID, nama proyek, PIC/SM...' : ($tab === 'vendors' ? 'nama vendor, PIC...' : 'nama client...') }}" 
                       value="{{ $search }}">
                @if($search)
                    <a href="{{ route('admin.master-data.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary">Reset</a>
                @endif
                <button type="submit" class="btn btn-primary fw-bold">Cari</button>
            </div>
        </form>
        @endif

        {{-- TAB CONTENT: MASTER PROYEK & PAGU --}}
        @if($tab === 'projects')
            {{-- Telemetry Cards Summary --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                        <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Proyek Master</div>
                        <div class="fw-extrabold text-dark fs-4 mb-0">{{ number_format($totalProjects, 0, ',', '.') }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Proyek terdaftar di sistem</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                        <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #0284C7 !important;">Total Pagu Kontrak</div>
                        <div class="fw-extrabold text-primary fs-5 font-monospace mb-0">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Batas alokasi anggaran</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                        <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #059669 !important;">Realisasi Berjalan</div>
                        <div class="fw-extrabold text-success fs-5 font-monospace mb-0">Rp {{ number_format($totalRealisasiPagu, 0, ',', '.') }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Total serapan pengeluaran</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                        @php
                            $globalBurn = $totalPagu > 0 ? round(($totalRealisasiPagu / $totalPagu) * 100, 1) : 0;
                        @endphp
                        <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em;">Rata-rata Serapan Pagu</div>
                        <div class="fw-extrabold {{ $globalBurn >= 90 ? 'text-danger' : ($globalBurn >= 75 ? 'text-warning' : 'text-dark') }} fs-4 mb-0">
                            {{ $globalBurn }}%
                        </div>
                        <div class="progress mt-1.5" style="height: 5px;">
                            <div class="progress-bar {{ $globalBurn >= 90 ? 'bg-danger' : ($globalBurn >= 75 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $globalBurn) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th style="width: 130px;">Project ID</th>
                            <th>Nama Proyek</th>
                            <th>PIC / SM</th>
                            <th>Client</th>
                            <th class="text-end">Pagu Kontrak</th>
                            <th class="text-end">Realisasi</th>
                            <th class="text-end">Sisa Pagu</th>
                            <th class="text-center" style="width: 110px;">Serapan</th>
                            <th style="width: 100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $idx => $p)
                            @php
                                $spent = (float) ($realisasiSums[$p->project_id] ?? 0);
                                $pagu  = (float) ($p->project_value ?? 0);
                                $sisa  = $pagu - $spent;
                                $burn  = $pagu > 0 ? round(($spent / $pagu) * 100, 1) : 0;
                                $isOver = $pagu > 0 && $spent > $pagu;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $projects->firstItem() + $idx }}</td>
                                <td>
                                    <div class="fw-bold font-monospace text-primary" style="font-size: 0.88rem;">{{ $p->project_id }}</div>
                                    @if($p->tahun)
                                        <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;">Th. {{ $p->tahun }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="max-width: 320px; line-height: 1.35;">{{ $p->project_name }}</div>
                                    @if($p->contract_number)
                                        <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                            <i class="bi bi-file-earmark-text me-1"></i>No: {{ $p->contract_number }}
                                        </div>
                                    @endif
                                    @if($p->amandemen)
                                        <div class="text-info small mt-0.5" style="font-size: 0.7rem;">
                                            <i class="bi bi-pencil-square me-1"></i>{{ Str::limit($p->amandemen, 45) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill" style="font-size: 0.74rem;">
                                        <i class="bi bi-person-fill me-1"></i>{{ $p->service_manager ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium small">{{ $p->project_client ?: 'PT PGN Tbk' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-dark font-monospace" style="font-size: 0.86rem;">
                                        Rp {{ number_format($pagu, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-primary font-monospace" style="font-size: 0.84rem;">
                                        Rp {{ number_format($spent, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold font-monospace {{ $isOver ? 'text-danger' : 'text-success' }}" style="font-size: 0.84rem;">
                                        Rp {{ number_format($sisa, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($isOver)
                                        <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;">Overbudget ({{ $burn }}%)</span>
                                    @elseif($burn >= 90)
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;">Kritis {{ $burn }}%</span>
                                    @elseif($burn >= 75)
                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1" style="font-size: 0.7rem;">Waspada {{ $burn }}%</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">Aman {{ $burn }}%</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(auth()->user()->hasRole('dmo'))
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-sm px-2 py-1 btn-edit-project" 
                                                    title="Edit Data Proyek & Pagu"
                                                    data-id="{{ $p->id }}"
                                                    data-project_id="{{ $p->project_id }}"
                                                    data-project_name="{{ $p->project_name }}"
                                                    data-project_value="{{ $p->project_value }}"
                                                    data-service_manager="{{ $p->service_manager }}"
                                                    data-project_client="{{ $p->project_client }}"
                                                    data-tahun="{{ $p->tahun }}"
                                                    data-contract_number="{{ $p->contract_number }}"
                                                    data-amandemen="{{ $p->amandemen }}"
                                                    data-spent="{{ $spent }}"
                                                    data-action="{{ route('admin.master-data.projects.update', $p->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.master-data.projects.delete', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus master proyek {{ $p->project_id }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Hapus Proyek">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-eye text-primary me-1"></i> Terpantau
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bi bi-folder-x fs-3 d-block mb-1 text-secondary"></i>
                                    Tidak ada data proyek yang sesuai kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $projects->appends(['tab' => 'projects', 'search' => $search])->links() }}
            </div>
        @endif

        {{-- TAB CONTENT 1: VENDORS --}}
        @if($tab === 'vendors')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Vendor Rekanan</th>
                            <th>Kode</th>
                            <th>PIC / Narahubung</th>
                            <th>Kontak / Email</th>
                            <th>Status</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $idx => $v)
                            <tr>
                                <td class="text-muted">{{ $vendors->firstItem() + $idx }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $v->nama_vendor }}</div>
                                    @if($v->alamat)
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ Str::limit($v->alamat, 60) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($v->kode_vendor)
                                        <code class="px-2 py-0.5 rounded bg-light border text-dark">{{ $v->kode_vendor }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $v->pic_vendor ?? '-' }}</td>
                                <td>
                                    @if($v->kontak || $v->email)
                                        <div>{{ $v->kontak ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $v->email ?? '' }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $v->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        {{ $v->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditVendor{{ $v->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('admin.master-data.vendors.delete', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus master vendor {{ $v->nama_vendor }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Edit Vendor --}}
                            <div class="modal fade" id="modalEditVendor{{ $v->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="{{ route('admin.master-data.vendors.update', $v->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Master Vendor</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Vendor <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_vendor" class="form-control form-control-sm" required value="{{ $v->nama_vendor }}">
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Kode Vendor</label>
                                                        <input type="text" name="kode_vendor" class="form-control form-control-sm" value="{{ $v->kode_vendor }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">PIC</label>
                                                        <input type="text" name="pic_vendor" class="form-control form-control-sm" value="{{ $v->pic_vendor }}">
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Kontak / Telp</label>
                                                        <input type="text" name="kontak" class="form-control form-control-sm" value="{{ $v->kontak }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Email</label>
                                                        <input type="email" name="email" class="form-control form-control-sm" value="{{ $v->email }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Alamat</label>
                                                    <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ $v->alamat }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data master vendor. Klik "Auto-Sync dari Realisasi" untuk mengisi otomatis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $vendors->links() }}
            </div>
        @endif

        {{-- TAB CONTENT 2: CLIENTS --}}
        @if($tab === 'clients')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Client / Pelanggan</th>
                            <th>Kode Client</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $idx => $c)
                            <tr>
                                <td class="text-muted">{{ $clients->firstItem() + $idx }}</td>
                                <td class="fw-bold text-dark">{{ $c->nama_client }}</td>
                                <td>
                                    @if($c->kode_client)
                                        <code class="px-2 py-0.5 rounded bg-light border text-dark">{{ $c->kode_client }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $c->kategori ?? 'Umum' }}</td>
                                <td>
                                    <span class="badge {{ $c->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        {{ $c->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditClient{{ $c->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('admin.master-data.clients.delete', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus master client {{ $c->nama_client }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Edit Client --}}
                            <div class="modal fade" id="modalEditClient{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="{{ route('admin.master-data.clients.update', $c->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Master Client</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Client <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_client" class="form-control form-control-sm" required value="{{ $c->nama_client }}">
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Kode Client</label>
                                                        <input type="text" name="kode_client" class="form-control form-control-sm" value="{{ $c->kode_client }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Kategori</label>
                                                        <input type="text" name="kategori" class="form-control form-control-sm" value="{{ $c->kategori }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data master client. Klik "Auto-Sync dari Realisasi" untuk mengisi otomatis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $clients->links() }}
            </div>
        @endif

        {{-- TAB CONTENT 3: OFFICIAL SIGNERS & CORPORATE IDENTITY --}}
        @if($tab === 'signers')
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border rounded-4 shadow-none">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-person-lines-fill text-primary"></i>
                                Profil Pejabat Penandatangan Laporan Resmi
                            </h6>
                            <span class="small text-muted">Profil ini otomatis dicetak pada lembar pengesahan Laporan Mutu QC dan Ringkasan Eksekutif Service Manager.</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.master-data.signers.update') }}" method="POST">
                                @csrf

                                {{-- BLOK 1: VICE PRESIDENT / MANAGEMENT --}}
                                <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC;">
                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <span class="badge bg-primary px-2.5 py-1 rounded-pill fw-bold">1</span>
                                        <h6 class="fw-bold mb-0 text-dark">Pejabat Pengesah Akhir (Vice President / Management)</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                            <input type="text" name="signer_vp_name" class="form-control form-control-sm rounded-3" required value="{{ $signers['signer_vp_name'] ?? '' }}" placeholder="Contoh: Dedi Suherman, S.T., M.M.">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Jabatan Resmi <span class="text-danger">*</span></label>
                                            <input type="text" name="signer_vp_title" class="form-control form-control-sm rounded-3" required value="{{ $signers['signer_vp_title'] ?? '' }}" placeholder="Contoh: VP Information Technology & Project Management">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nomor Induk Pekerja / NIP</label>
                                            <input type="text" name="signer_vp_nip" class="form-control form-control-sm rounded-3" value="{{ $signers['signer_vp_nip'] ?? '' }}" placeholder="Contoh: Pekerja: 78912044">
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <div class="small text-muted ps-2">
                                                <i class="bi bi-shield-check text-success me-1"></i> Tampil di kolom pengesahan akhir laporan cetak.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- BLOK 2: QUALITY CONTROL LEAD --}}
                                <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC;">
                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <span class="badge bg-info text-white px-2.5 py-1 rounded-pill fw-bold">2</span>
                                        <h6 class="fw-bold mb-0 text-dark">Lead Inspector Quality Control (QC Lead)</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                            <input type="text" name="signer_qc_lead_name" class="form-control form-control-sm rounded-3" required value="{{ $signers['signer_qc_lead_name'] ?? '' }}" placeholder="Contoh: Ahmad Fauzi, S.T.">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Jabatan Resmi <span class="text-danger">*</span></label>
                                            <input type="text" name="signer_qc_lead_title" class="form-control form-control-sm rounded-3" required value="{{ $signers['signer_qc_lead_title'] ?? '' }}" placeholder="Contoh: Lead Quality Control & Technical Assurance">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nomor Induk Pekerja / NIP</label>
                                            <input type="text" name="signer_qc_lead_nip" class="form-control form-control-sm rounded-3" value="{{ $signers['signer_qc_lead_nip'] ?? '' }}" placeholder="Contoh: Pekerja: 89014522">
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <div class="small text-muted ps-2">
                                                <i class="bi bi-shield-check text-info me-1"></i> Tampil di lembar verifikasi mutu BASTO.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- BLOK 3: ENTITAS ORGANISASI --}}
                                <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC;">
                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <span class="badge bg-secondary px-2.5 py-1 rounded-pill fw-bold">3</span>
                                        <h6 class="fw-bold mb-0 text-dark">Identitas Entitas & Divisi</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nama Perusahaan / Entitas</label>
                                            <input type="text" name="company_name" class="form-control form-control-sm rounded-3" value="{{ $signers['company_name'] ?? 'PT PGAS Telekomunikasi Nusantara (PGNCOM)' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Nama Satuan Kerja / Divisi</label>
                                            <input type="text" name="division_name" class="form-control form-control-sm rounded-3" value="{{ $signers['division_name'] ?? 'Operation & Service Management (OSM)' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3 shadow-sm">
                                        <i class="bi bi-check2-circle me-1"></i> Simpan Profil Pejabat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR INFO PREVIEW --}}
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%); color: white;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-award-fill text-warning fs-4"></i>
                                <span class="fw-bold text-uppercase small" style="letter-spacing: 0.05em;">Pratinjau Lembar Sah</span>
                            </div>
                            <p class="small text-white text-opacity-80 mb-4">
                                Data pejabat di samping secara otomatis mengisi blok tanda tangan 3 pihak pada cetak laporan resmi PGNCOM:
                            </p>
                            
                            <div class="bg-white text-dark p-3 rounded-3 mb-3 shadow-sm" style="font-size: 0.75rem;">
                                <div class="text-muted mb-1" style="font-size: 0.68rem;">PENGESAHAN AKHIR (VP):</div>
                                <div class="fw-bold text-primary">{{ $signers['signer_vp_name'] ?? 'Dedi Suherman, S.T., M.M.' }}</div>
                                <div class="text-secondary" style="font-size: 0.7rem;">{{ $signers['signer_vp_title'] ?? 'VP IT & Project Management' }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $signers['signer_vp_nip'] ?? '-' }}</div>
                            </div>

                            <div class="bg-white text-dark p-3 rounded-3 mb-3 shadow-sm" style="font-size: 0.75rem;">
                                <div class="text-muted mb-1" style="font-size: 0.68rem;">PENGAWASAN MUTU (QC LEAD):</div>
                                <div class="fw-bold text-info">{{ $signers['signer_qc_lead_name'] ?? 'Ahmad Fauzi, S.T.' }}</div>
                                <div class="text-secondary" style="font-size: 0.7rem;">{{ $signers['signer_qc_lead_title'] ?? 'Lead QC & Assurance' }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $signers['signer_qc_lead_nip'] ?? '-' }}</div>
                            </div>

                            <div class="small text-white text-opacity-75 pt-2 border-top border-white border-opacity-25">
                                <i class="bi bi-printer me-1"></i> Berlaku langsung saat mencetak di:
                                <ul class="ps-3 mb-0 mt-1">
                                    <li>Laporan Mutu QC</li>
                                    <li>Ringkasan Eksekutif SM</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Add Vendor --}}
<div class="modal fade" id="modalAddVendor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.master-data.vendors.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Master Vendor Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Vendor <span class="text-danger">*</span></label>
                        <input type="text" name="nama_vendor" class="form-control form-control-sm" required placeholder="Contoh: PT ANUGERAH KARYA TEKNIK">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kode Vendor</label>
                            <input type="text" name="kode_vendor" class="form-control form-control-sm" placeholder="Contoh: VND-001">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">PIC</label>
                            <input type="text" name="pic_vendor" class="form-control form-control-sm" placeholder="Nama Contact Person">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kontak / Telp</label>
                            <input type="text" name="kontak" class="form-control form-control-sm" placeholder="0812...">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="vendor@domain.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat</label>
                        <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Alamat kantor/operasional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Add Client --}}
<div class="modal fade" id="modalAddClient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.master-data.clients.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Master Client Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Client / Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_client" class="form-control form-control-sm" required placeholder="Contoh: PT PGAS TELEKOMUNIKASI NUSANTARA">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kode Client</label>
                            <input type="text" name="kode_client" class="form-control form-control-sm" placeholder="Contoh: CLI-PGAS">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kategori</label>
                            <input type="text" name="kategori" class="form-control form-control-sm" placeholder="Subholding / Eksternal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(auth()->user()->hasRole('dmo'))
{{-- MODAL ADD PROJECT --}}
<div class="modal fade" id="modalAddProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form action="{{ route('admin.master-data.projects.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 32px; height: 32px;">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0">Daftarkan Master Proyek &amp; Pagu Baru</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Pagu ini menjadi batas plafon serapan realisasi dan perhitungan prognosa</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Project ID / Kode Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="project_id" class="form-control font-monospace fw-bold text-primary" required 
                                   placeholder="Contoh: PS-024-00" style="text-transform: uppercase;">
                            <small class="text-muted" style="font-size: 0.7rem;">Gunakan format baku (misal: PS-xxx-xx / MS-xxx)</small>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control" required 
                                   placeholder="Contoh: Operasi & Pemeliharaan Terintegrasi GMS">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nilai Pagu Anggaran (Kontrak) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="text" name="project_value" id="addProjectValue" class="form-control font-monospace fw-bold text-dark fs-6" required 
                                       placeholder="Contoh: 1.500.000.000">
                            </div>
                            <div class="text-primary fw-bold mt-1" id="addProjectValuePreview" style="font-size: 0.74rem;">—</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tahun Anggaran</label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" min="2020" max="2035">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Nomor Kontrak / PO</label>
                            <input type="text" name="contract_number" class="form-control" placeholder="Opsional">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Service Manager / PIC</label>
                            <select name="service_manager" id="addProjectSM" class="form-select">
                                <option value="">— Pilih Service Manager / PIC —</option>
                                @foreach($smList as $sm)
                                    <option value="{{ $sm }}">{{ $sm }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.68rem;">Pilih PIC resmi penanggung jawab proyek</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client / Pemilik Pekerjaan</label>
                            <input type="text" name="project_client" class="form-control" list="addClientOptions" placeholder="Pilih atau ketik nama client" value="PT PGN Tbk">
                            <datalist id="addClientOptions">
                                @foreach($clientList as $cl)
                                    <option value="{{ $cl }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan / Keterangan Amandemen</label>
                        <textarea name="amandemen" class="form-control" rows="2" placeholder="Catatan adendum kontrak, perubahan lingkup, atau detail pagu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Proyek Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT PROJECT & PAGU --}}
<div class="modal fade" id="modalEditProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form id="formEditProject" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning text-dark" style="width: 32px; height: 32px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0">Perbarui Master Proyek &amp; Pagu Anggaran</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Ubah plafon nilai kontrak saat ada Amandemen / Addendum Anggaran</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Status Realisasi Berjalan Telemetry --}}
                    <div class="p-3 rounded-3 border mb-3" style="background: #F8FAFC;">
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Realisasi Berjalan Saat Ini</small>
                                <div class="fw-bold font-monospace text-primary fs-6 mt-0.5" id="editProjectSpent">Rp 0</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Estimasi Sisa Pagu Baru</small>
                                <div class="fw-bold font-monospace text-success fs-6 mt-0.5" id="editProjectRemaining">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Project ID / Kode Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="project_id" id="editProjectId" class="form-control font-monospace fw-bold text-primary" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap Proyek <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="editProjectName" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nilai Pagu Anggaran (Kontrak) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="text" name="project_value" id="editProjectValue" class="form-control font-monospace fw-bold text-dark fs-6" required>
                            </div>
                            <div class="text-primary fw-bold mt-1" id="editProjectValuePreview" style="font-size: 0.74rem;">—</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tahun Anggaran</label>
                            <input type="number" name="tahun" id="editProjectTahun" class="form-control" min="2020" max="2035">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Nomor Kontrak / PO</label>
                            <input type="text" name="contract_number" id="editProjectContractNumber" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Service Manager / PIC</label>
                            <select name="service_manager" id="editProjectSM" class="form-select">
                                <option value="">— Pilih Service Manager / PIC —</option>
                                @foreach($smList as $sm)
                                    <option value="{{ $sm }}">{{ $sm }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.68rem;">Pilih PIC resmi penanggung jawab proyek</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client / Pemilik Pekerjaan</label>
                            <input type="text" name="project_client" id="editProjectClient" class="form-control" list="addClientOptions">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Amandemen / Riwayat Addendum</label>
                        <textarea name="amandemen" id="editProjectAmandemen" class="form-control" rows="2" placeholder="Contoh: Addendum I penambahan pagu sebesar Rp 200 Juta tertanggal ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Pembaruan Pagu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Currency formatter helper
    function formatRupiah(number) {
        if (!number && number !== 0) return 'Rp 0';
        const num = Math.round(Number(number));
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function cleanNumber(str) {
        if (!str) return 0;
        return parseFloat(String(str).replace(/[^\d]/g, '')) || 0;
    }

    // Live preview on Add Project Value
    const addValInput = document.getElementById('addProjectValue');
    const addValPreview = document.getElementById('addProjectValuePreview');
    if (addValInput && addValPreview) {
        addValInput.addEventListener('input', function() {
            const raw = cleanNumber(this.value);
            addValPreview.textContent = formatRupiah(raw);
        });
    }

    // Live preview & Remaining calculation on Edit Project Value
    const editValInput = document.getElementById('editProjectValue');
    const editValPreview = document.getElementById('editProjectValuePreview');
    const editRemainingDiv = document.getElementById('editProjectRemaining');
    let currentEditSpent = 0;

    if (editValInput && editValPreview) {
        editValInput.addEventListener('input', function() {
            const raw = cleanNumber(this.value);
            editValPreview.textContent = formatRupiah(raw);
            const remaining = raw - currentEditSpent;
            if (editRemainingDiv) {
                editRemainingDiv.textContent = formatRupiah(remaining);
                if (remaining < 0) {
                    editRemainingDiv.className = 'fw-bold font-monospace text-danger fs-6 mt-0.5';
                } else {
                    editRemainingDiv.className = 'fw-bold font-monospace text-success fs-6 mt-0.5';
                }
            }
        });
    }

    // Modal Edit Project Trigger
    const editButtons = document.querySelectorAll('.btn-edit-project');
    const formEdit = document.getElementById('formEditProject');
    const modalEditEl = document.getElementById('modalEditProject');
    const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;

    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const action = this.dataset.action;
            const projectId = this.dataset.project_id || '';
            const projectName = this.dataset.project_name || '';
            const projectValue = parseFloat(this.dataset.project_value || 0);
            const serviceManager = this.dataset.service_manager || '';
            const projectClient = this.dataset.project_client || '';
            const tahun = this.dataset.tahun || '';
            const contractNumber = this.dataset.contract_number || '';
            const amandemen = this.dataset.amandemen || '';
            const spent = parseFloat(this.dataset.spent || 0);

            currentEditSpent = spent;

            if (formEdit) formEdit.action = action;
            document.getElementById('editProjectId').value = projectId;
            document.getElementById('editProjectName').value = projectName;
            document.getElementById('editProjectValue').value = projectValue > 0 ? projectValue : '';
            if (editValPreview) editValPreview.textContent = formatRupiah(projectValue);
            const editSmEl = document.getElementById('editProjectSM');
            if (editSmEl) {
                editSmEl.value = serviceManager;
                if (serviceManager && editSmEl.value !== serviceManager) {
                    let matched = false;
                    for (let i = 0; i < editSmEl.options.length; i++) {
                        if (editSmEl.options[i].value.toUpperCase() === serviceManager.toUpperCase()) {
                            editSmEl.selectedIndex = i;
                            matched = true;
                            break;
                        }
                    }
                    if (!matched) {
                        const newOption = new Option(serviceManager, serviceManager, true, true);
                        editSmEl.add(newOption);
                    }
                }
            }
            document.getElementById('editProjectClient').value = projectClient;
            document.getElementById('editProjectTahun').value = tahun;
            document.getElementById('editProjectContractNumber').value = contractNumber;
            document.getElementById('editProjectAmandemen').value = amandemen;

            document.getElementById('editProjectSpent').textContent = formatRupiah(spent);
            const remaining = projectValue - spent;
            if (editRemainingDiv) {
                editRemainingDiv.textContent = formatRupiah(remaining);
                editRemainingDiv.className = 'fw-bold font-monospace fs-6 mt-0.5 ' + (remaining < 0 ? 'text-danger' : 'text-success');
            }

            if (modalEdit) modalEdit.show();
        });
    });
});
</script>
@endpush

@endsection
