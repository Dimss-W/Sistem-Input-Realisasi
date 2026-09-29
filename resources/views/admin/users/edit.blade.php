@extends('layouts.main')

@section('title', 'Edit Profil Akun')
@section('page-title', 'Edit Akun Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none"><i class="bi bi-people"></i> Kelola Akun</a></li>
    <li class="breadcrumb-item active"><i class="bi bi-pencil me-1"></i>Edit Akun</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Banner -->
            <div class="card-header border-0 pt-4 px-4 pb-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                         style="width: 48px; height: 48px; font-size: 1.1rem; background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-white">Edit Profil: {{ $user->name }}</h5>
                        <p class="text-white text-opacity-80 small mb-0">Ubah informasi profil, role akses, atau reset password pengguna</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Section: Profil Pengguna -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-1.5" style="width:8px; height:8px;"></span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase fs-7 letter-spacing-05">1. Profil & Identitas Pengguna</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label text-secondary small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control border-start-0 @error('name') is-invalid @enderror" placeholder="Nama Lengkap" required style="height:44px;">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label text-secondary small fw-bold">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="form-control border-start-0 @error('username') is-invalid @enderror" placeholder="username_pengguna" required style="height:44px;">
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label text-secondary small fw-bold">Email PGN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control border-start-0 @error('email') is-invalid @enderror" placeholder="user@pgncom.co.id" required style="height:44px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label text-secondary small fw-bold">Nomor Telepon / WA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-control border-start-0 @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" style="height:44px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Role Akses -->
                    <div class="d-flex align-items-center gap-2 mb-3 pt-2">
                        <span class="badge bg-warning rounded-circle p-1.5" style="width:8px; height:8px;"></span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase fs-7 letter-spacing-05">2. Role & Otorisasi Sistem</h6>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label text-secondary small fw-bold">Role Akses <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required style="height:46px; font-weight: 600;">
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Full User Management & Control Access)</option>
                            <option value="procurement" {{ old('role', $user->role) === 'procurement' ? 'selected' : '' }}>Procurement (Pengadaan Barang & Jasa)</option>
                            <option value="osm_service_manager" {{ old('role', $user->role) === 'osm_service_manager' ? 'selected' : '' }}>OSM - Service Manager (BASTO & Operations)</option>
                            <option value="osm_qc" {{ old('role', $user->role) === 'osm_qc' ? 'selected' : '' }}>OSM - Quality Control (QC Monitoring)</option>
                            <option value="dmo" {{ old('role', $user->role) === 'dmo' ? 'selected' : '' }}>DMO (Data Management Officer - Realisasi & BASTO)</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Section: Reset Password -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pt-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-circle p-1.5" style="width:8px; height:8px;"></span>
                            <h6 class="fw-bold text-dark mb-0 text-uppercase fs-7 letter-spacing-05">3. Ganti Password</h6>
                        </div>
                        <span class="text-muted small" style="font-size:0.75rem;">*Kosongkan jika tidak ingin mengubah password</span>
                    </div>

                    <div class="row g-3 mb-4 p-3 bg-light rounded-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label text-secondary small fw-bold">Password Baru</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter" style="height:44px;">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label text-secondary small fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang password baru" style="height:44px;">
                        </div>
                    </div>

                    <!-- Section: Catatan -->
                    <div class="mb-4">
                        <label for="notes" class="form-label text-secondary small fw-bold">Catatan / Keterangan Jabatan</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Tambahkan catatan jika ada...">{{ old('notes', $user->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-3 px-4 fw-bold btn-action-animated"><i class="bi bi-x-circle me-1"></i>Batal</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm btn-action-animated"><i class="bi bi-check-circle-fill me-1.5"></i>Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
