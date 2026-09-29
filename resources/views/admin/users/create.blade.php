@extends('layouts.main')

@section('title', 'Tambah Akun Baru')
@section('page-title', 'Tambah Pengguna Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none"><i class="bi bi-people"></i> Kelola Akun</a></li>
    <li class="breadcrumb-item active"><i class="bi bi-person-plus me-1"></i>Tambah Akun</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Form Header Banner -->
            <div class="card-header border-0 pt-4 px-4 pb-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2.5 bg-white bg-opacity-15 text-white">
                        <i class="bi bi-person-plus-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-white">Form Registrasi Pengguna Baru</h5>
                        <p class="text-white text-opacity-80 small mb-0">Isi data akun berikut untuk memberikan akses masuk ke sistem</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
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
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control border-start-0 @error('name') is-invalid @enderror" placeholder="Contoh: Ahmad Subagyo" required style="height:44px;">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label text-secondary small fw-bold">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control border-start-0 @error('username') is-invalid @enderror" placeholder="ahmad.subagyo" required style="height:44px;">
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label text-secondary small fw-bold">Email PGN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control border-start-0 @error('email') is-invalid @enderror" placeholder="ahmad.subagyo@pgncom.co.id" required style="height:44px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label text-secondary small fw-bold">Nomor Telepon / WA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control border-start-0 @error('phone') is-invalid @enderror" placeholder="081234567890" style="height:44px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Role & Otorisasi -->
                    <div class="d-flex align-items-center gap-2 mb-3 pt-2">
                        <span class="badge bg-warning rounded-circle p-1.5" style="width:8px; height:8px;"></span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase fs-7 letter-spacing-05">2. Role & Hak Akses Akses Sistem</h6>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label text-secondary small fw-bold">Pilih Role Akses <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required style="height:46px; font-weight: 600;">
                            <option value="" disabled selected>Pilih salah satu role...</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Full User Management & Control Access)</option>
                            <option value="procurement" {{ old('role') === 'procurement' ? 'selected' : '' }}>Procurement (Pengadaan Barang & Jasa)</option>
                            <option value="osm_service_manager" {{ old('role') === 'osm_service_manager' ? 'selected' : '' }}>OSM - Service Manager (BASTO & Operations)</option>
                            <option value="osm_qc" {{ old('role') === 'osm_qc' ? 'selected' : '' }}>OSM - Quality Control (QC Monitoring)</option>
                            <option value="dmo" {{ old('role') === 'dmo' ? 'selected' : '' }}>DMO (Data Management Officer - Realisasi & BASTO)</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Section: Keamanan Password -->
                    <div class="d-flex align-items-center gap-2 mb-3 pt-2">
                        <span class="badge bg-danger rounded-circle p-1.5" style="width:8px; height:8px;"></span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase fs-7 letter-spacing-05">3. Keamanan Akun (Password)</h6>
                    </div>

                    <div class="row g-3 mb-4 p-3 bg-light rounded-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label text-secondary small fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter" required style="height:44px;">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label text-secondary small fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang password" required style="height:44px;">
                        </div>
                    </div>

                    <!-- Section: Catatan -->
                    <div class="mb-4">
                        <label for="notes" class="form-label text-secondary small fw-bold">Catatan / Keterangan Jabatan</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Contoh: Staff Service Manager Divisi OCS Jakarta...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-3 px-4 fw-bold btn-action-animated"><i class="bi bi-x-circle me-1"></i>Batal</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm btn-action-animated"><i class="bi bi-check-circle-fill me-1.5"></i>Simpan Akun Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
