@extends('layouts.main')

@section('title', 'Riwayat Import Excel — DMO Portal')
@section('page-title', 'Riwayat Import Excel')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Logs Import Excel</h4>
            <p class="text-muted small mb-0">Catatan aktivitas pengunggahan dan pemrosesan file Excel Realisasi & Pembayaran</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('realisasi.import.form') }}" class="btn btn-primary btn-md rounded-3 fw-bold shadow-sm btn-action-animated">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah File Excel Baru
            </a>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-entrance">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-uppercase text-secondary fs-7 fw-bold border-bottom">
                        <tr>
                            <th class="py-3.5 ps-4">No</th>
                            <th class="py-3.5">Waktu Import</th>
                            <th class="py-3.5">Nama File</th>
                            <th class="py-3.5">Pengunggah</th>
                            <th class="py-3.5 text-center">Tipe</th>
                            <th class="py-3.5 text-center">Baris Sukses</th>
                            <th class="py-3.5 text-center">Baris Gagal</th>
                            <th class="py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                            <tr class="stagger-row" style="--row-delay: {{ $index * 0.03 }}s;">
                                <td class="ps-4 text-secondary fw-semibold">{{ $logs->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark">
                                    {{ $log->imported_at ? $log->imported_at->format('d M Y H:i') : '—' }} WIB
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-excel-fill text-success fs-5"></i>
                                        <span class="fw-semibold text-dark">{{ $log->filename }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-secondary small">{{ $log->user->email ?? '—' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.72rem;">
                                        {{ strtoupper($log->type ?? 'REALISASI') }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-success">
                                    {{ number_format($log->success_count ?? 0) }}
                                </td>
                                <td class="text-center fw-bold {{ ($log->failed_count ?? 0) > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($log->failed_count ?? 0) }}
                                </td>
                                <td class="text-center">
                                    @if(($log->failed_count ?? 0) === 0)
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle me-1"></i> Sukses Total
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Ada Warning
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-5 d-block mb-2 text-secondary opacity-50"></i>
                                    <div class="fw-bold">Belum Ada Riwayat Import</div>
                                    <div class="small">Semua pengunggahan file Excel akan dicatat secara otomatis di sini</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} log</span>
                <div>{{ $logs->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
