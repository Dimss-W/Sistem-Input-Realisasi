@extends('layouts.main')

@section('title', 'Kelola Akun')
@section('page-title', 'Kelola Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li>
    <li class="breadcrumb-item active"><i class="bi bi-people me-1"></i>Kelola Akun</li>
@endsection

@push('styles')
<style>
    /* Smooth Main Card Entrance Animation */
    .user-mgmt-card {
        animation: userCardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes userCardEntrance {
        0% {
            opacity: 0;
            transform: translateY(28px) scale(0.985);
            filter: blur(4px);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
    }

    /* Header & Filter Card Entrance */
    .user-filter-header {
        animation: headerSlideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    }

    @keyframes headerSlideDown {
        0% {
            opacity: 0;
            transform: translateY(-12px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Staggered Row Entrance Animation */
    .user-row-animated {
        animation: userRowSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) calc(0.12s + var(--row-delay, 0s)) both;
        transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    }

    @keyframes userRowSlideIn {
        0% {
            opacity: 0;
            transform: translateX(-16px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .user-row-animated:hover {
        background-color: #f8fafc !important;
        transform: scale(1.002) translateX(2px);
    }

    /* Interactive Avatar Ring Pulse */
    .user-avatar-glow {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .user-row-animated:hover .user-avatar-glow {
        transform: scale(1.08);
        box-shadow: 0 0 12px rgba(2, 132, 199, 0.4) !important;
    }

    /* Action Buttons Hover Effects */
    .btn-action-animated {
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
    }
    .btn-action-animated:hover {
        transform: scale(1.18) translateY(-1px);
    }

    /* Pulse Indicator for Active Users */
    .status-pulse {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: statusPulse 2s infinite;
    }
    @keyframes statusPulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 user-mgmt-card">
    <!-- Filter Card Header -->
    <div class="card-body border-bottom bg-light bg-opacity-50 p-4 user-filter-header">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-2">
                    <i class="bi bi-search me-1"></i>Pencarian Pengguna
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="search" id="search" value="{{ $search }}" class="form-control border-start-0" style="height:42px; font-size: 0.9rem;" placeholder="Cari nama, email, atau username...">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="role" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-2">
                    <i class="bi bi-funnel me-1"></i>Filter Role
                </label>
                <select name="role" id="role" class="form-select" style="height:42px; font-size: 0.9rem;">
                    <option value="">Semua Role Access</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin (Administrator)</option>
                    <option value="procurement" {{ $roleFilter === 'procurement' ? 'selected' : '' }}>Procurement (Pengadaan Barang & Jasa)</option>
                    <option value="osm_service_manager" {{ $roleFilter === 'osm_service_manager' ? 'selected' : '' }}>OSM - Service Manager</option>
                    <option value="osm_qc" {{ $roleFilter === 'osm_qc' ? 'selected' : '' }}>OSM - QC</option>
                    <option value="dmo" {{ $roleFilter === 'dmo' ? 'selected' : '' }}>DMO (Data Management Officer)</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5 rounded-3 btn-action-animated" style="height:42px;">
                    <i class="bi bi-filter"></i> Terapkan Filter
                </button>
                @if($search || $roleFilter)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center rounded-3 btn-action-animated" style="height:42px; width:46px;" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Actions & Table -->
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pengguna Sistem</h5>
            <p class="text-muted small mb-0">Total {{ $users->total() }} akun terdaftar dalam database</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-md rounded-3 py-2 px-3.5 fw-bold d-flex align-items-center gap-1.5 shadow-sm btn-action-animated">
            <i class="bi bi-person-plus-fill fs-6"></i> Tambah Akun Baru
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
                <thead class="table-light text-uppercase text-secondary fs-7 fw-bold border-bottom">
                    <tr>
                        <th class="py-3.5 ps-4" style="width: 60px;">No</th>
                        <th class="py-3.5">Pengguna</th>
                        <th class="py-3.5">Username & Email</th>
                        <th class="py-3.5">Role Akses</th>
                        <th class="py-3.5">Status Akun</th>
                        <th class="py-3.5">Login Terakhir</th>
                        <th class="py-3.5 pe-4 text-end" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr class="user-row-animated" style="--row-delay: {{ $index * 0.04 }}s;">
                            <td class="ps-4 text-secondary fw-semibold">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm user-avatar-glow" 
                                         style="width: 40px; height: 40px; font-size: 0.88rem; background: linear-gradient(135deg, #0284c7, #1e3a8a);">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-7.5">{{ $u->name }}</div>
                                        @if($u->phone)
                                            <div class="text-secondary small"><i class="bi bi-telephone me-1"></i>{{ $u->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><code class="text-primary fw-bold px-2 py-0.5 rounded bg-primary bg-opacity-10">{{ $u->username ?? '—' }}</code></div>
                                <div class="text-secondary small mt-0.5"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</div>
                            </td>
                            <td>
                                @php
                                    $roleMap = [
                                        'admin' => ['Admin', 'bg-dark text-white', 'bi-shield-lock-fill', ''],
                                        'procurement' => ['Procurement', 'text-white', 'bi-cart-check-fill', 'background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 2px 6px rgba(99, 102, 241, 0.35);'],
                                        'osm_service_manager' => ['OSM - Service Manager', 'bg-primary text-white', 'bi-person-badge-fill', ''],
                                        'osm_qc' => ['OSM - QC', 'bg-info text-dark', 'bi-check-square-fill', ''],
                                        'dmo' => ['DMO', 'bg-warning text-dark', 'bi-folder-fill', '']
                                    ];
                                    $roleData = $roleMap[$u->role] ?? ['User', 'bg-secondary text-white', 'bi-person', ''];
                                @endphp
                                <span class="badge {{ $roleData[1] }} px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="font-size:0.72rem; font-weight:700; {{ $roleData[3] }}">
                                    <i class="bi {{ $roleData[2] }}"></i> {{ $roleData[0] }}
                                </span>
                            </td>

                            <td>
                                @if(($u->status ?? 'active') === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold small d-inline-flex align-items-center gap-1">
                                        <span class="badge bg-success rounded-circle p-1 status-pulse" style="width:5px; height:5px;"></span> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-pause-circle"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($u->last_login_at)
                                    <div class="d-flex align-items-center gap-1.5 text-dark fw-medium">
                                        <span>{{ $u->last_login_at->format('d M Y') }}</span>
                                    </div>
                                    <span class="text-secondary small d-block ms-1">{{ $u->last_login_at->format('H:i') }} WIB</span>
                                @else
                                    <span class="text-muted small italic">Belum pernah login</span>
                                @endif
                            </td>

                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <button class="btn btn-sm btn-light text-info rounded-3 p-1.5 px-2 btn-action-animated" data-bs-toggle="modal" data-bs-target="#userModal{{ $u->id }}" title="Detail Pengguna">
                                        <i class="bi bi-eye-fill fs-6"></i>
                                    </button>

                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-light text-warning rounded-3 p-1.5 px-2 btn-action-animated" title="Edit Pengguna">
                                        <i class="bi bi-pencil-fill fs-6"></i>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        {{-- Impersonate / Login Sebagai --}}
                                        @if(($u->status ?? 'active') === 'active')
                                            <a href="{{ route('admin.impersonate', $u->id) }}" class="btn btn-sm btn-light text-purple rounded-3 p-1.5 px-2 btn-action-animated" style="color: #7C3AED;" title="Login Sementara Sebagai {{ $u->name }} (Uji Akses Role)" onclick="return confirm('Masuk sementara sebagai {{ addslashes($u->name) }} (Role: {{ $u->role }})?')">
                                                <i class="bi bi-box-arrow-in-right fs-6"></i>
                                            </a>
                                        @endif
                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light {{ $u->status === 'active' ? 'text-secondary' : 'text-success' }} rounded-3 p-1.5 px-2 btn-action-animated" title="{{ $u->status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i class="bi {{ $u->status === 'active' ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' }} fs-6"></i>
                                            </button>
                                        </form>

                                        {{-- Reset Password --}}
                                        <form action="{{ route('admin.users.reset-password', $u->id) }}" method="POST" class="d-inline" id="reset-pwd-{{ $u->id }}">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-light text-primary rounded-3 p-1.5 px-2 btn-action-animated" onclick="confirmResetPassword('reset-pwd-{{ $u->id }}', '{{ addslashes($u->name) }}')" title="Reset Password ke Default">
                                                <i class="bi bi-key-fill fs-6"></i>
                                            </button>
                                        </form>

                                        {{-- Delete User --}}
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" id="delete-user-{{ $u->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light text-danger rounded-3 p-1.5 px-2 btn-action-animated" onclick="confirmDeleteUser('delete-user-{{ $u->id }}', '{{ addslashes($u->name) }}')" title="Hapus Pengguna">
                                                <i class="bi bi-trash-fill fs-6"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail Pengguna -->
                        <div class="modal fade" id="userModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-2"></i>Detail Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                                 style="width: 55px; height: 55px; font-size: 1.2rem; background: linear-gradient(135deg, #0284c7, #1e3a8a);">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $u->name }}</h6>
                                                <span class="badge {{ $roleData[1] }} px-2.5 py-1 rounded-pill" style="font-size: 0.7rem; {{ $roleData[3] }}">
                                                    <i class="bi {{ $roleData[2] }} me-1"></i>{{ $roleData[0] }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Username</label>
                                                <div class="fw-bold text-dark">{{ $u->username ?? '—' }}</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Nomor Telepon</label>
                                                <div class="fw-semibold text-dark">{{ $u->phone ?? '—' }}</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Email Address</label>
                                                <div class="fw-semibold text-primary">{{ $u->email }}</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Login Terakhir</label>
                                                <div class="fw-semibold text-dark">
                                                    {{ $u->last_login_at ? $u->last_login_at->format('d M Y H:i') . ' WIB' : 'Belum Pernah' }}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Dibuat Pada</label>
                                                <div class="fw-semibold text-dark">{{ $u->created_at ? $u->created_at->format('d M Y') : '—' }}</div>
                                            </div>
                                            @if($u->notes)
                                                <div class="col-12">
                                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">Catatan</label>
                                                    <div class="p-2.5 bg-light rounded-3 text-secondary small">{{ $u->notes }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                        <button type="button" class="btn btn-light rounded-3 w-100 fw-bold" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x display-5 d-block mb-2 text-secondary opacity-50"></i>
                                <div class="fw-bold">Tidak ada akun pengguna ditemukan</div>
                                <div class="small">Coba ubah kata kunci pencarian atau filter role</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            <div>{{ $users->links() }}</div>
        </div>
    @endif
</div>

<script>
    function confirmResetPassword(formId, userName) {
        Swal.fire({
            title: 'Reset Password Akun?',
            text: `Apakah Anda yakin ingin mereset password akun "${userName}" menjadi default ("password")?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-key-fill me-1"></i>Ya, Reset Password',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4 border-0 shadow'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmDeleteUser(formId, userName) {
        Swal.fire({
            title: 'Hapus Akun Pengguna?',
            text: `Apakah Anda yakin ingin menghapus akun "${userName}"? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i>Ya, Hapus Akun',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4 border-0 shadow'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection
