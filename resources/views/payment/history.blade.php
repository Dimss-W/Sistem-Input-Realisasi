@extends('layouts.main')

@section('title', 'Riwayat Pembayaran Kas Masuk — Finance')
@section('page-title', 'Riwayat Pembayaran Kas Masuk')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-5">
            <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-receipt text-success me-2"></i>Riwayat Pelunasan & Kas Masuk</h4>
            <p class="text-muted small mb-0">Pencatatan mutasi transaksi pelunasan invoice dan penerimaan pembayaran kas dari pelanggan</p>
        </div>
        <div class="col-md-7 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
            @if(auth()->user()->hasRole(['finance', 'admin']))
                <a href="{{ route('payment.upload') }}" class="btn btn-success btn-md rounded-3 fw-bold shadow-sm btn-action-animated">
                    <i class="bi bi-file-earmark-excel me-1"></i> Upload Excel Pembayaran
                </a>
            @endif
            <a href="{{ route('payment.export', request()->query()) }}" class="btn btn-outline-success btn-md rounded-3 fw-bold shadow-sm btn-action-animated">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel Kas Masuk
            </a>
            <div class="badge bg-success bg-opacity-10 text-success p-2.5 px-3 rounded-3 border border-success-subtle d-inline-flex align-items-center gap-2">
                <i class="bi bi-wallet2 fs-6"></i>
                <span>Total: <strong>Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 bg-white card-entrance">
        <form action="{{ route('payment.history') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-1">Cari Pembayaran</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="search" value="{{ $search }}" class="form-control border-start-0" placeholder="No. referensi, invoice, klien...">
                </div>
            </div>
            <div class="col-md-2">
                <label for="from_date" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="to_date" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="payment_method" class="form-label text-secondary fs-7 text-uppercase fw-bold mb-1">Metode Bayar</label>
                <select name="payment_method" id="payment_method" class="form-select">
                    <option value="">Semua Metode</option>
                    @php
                        $standardMethods = collect(['TRANSFER', 'CASH', 'GIRO', 'CEK']);
                        if(isset($availableMethods) && count($availableMethods) > 0) {
                            $allMethods = $standardMethods->merge($availableMethods)->unique();
                        } else {
                            $allMethods = $standardMethods;
                        }
                    @endphp
                    @foreach($allMethods as $m)
                        <option value="{{ $m }}" {{ ($paymentMethod == $m) ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold btn-action-animated">
                    <i class="bi bi-filter"></i> Filter
                </button>
                @if($search || $fromDate || $toDate || $paymentMethod)
                    <a href="{{ route('payment.history') }}" class="btn btn-outline-secondary btn-action-animated" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        @if($search || $fromDate || $toDate || $paymentMethod)
            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between text-muted small">
                <div>
                    <i class="bi bi-funnel-fill text-primary me-1"></i> Menampilkan hasil filter kas masuk. 
                    Total nominal tersaring: <strong class="text-success">Rp {{ number_format($totalFilteredAmount, 0, ',', '.') }}</strong>
                </div>
                <a href="{{ route('payment.history') }}" class="text-decoration-none text-danger fw-semibold">
                    <i class="bi bi-trash3 me-1"></i>Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-entrance">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-uppercase text-secondary fs-7 fw-bold border-bottom">
                        <tr>
                            <th class="py-3.5 ps-4">No</th>
                            <th class="py-3.5">Tgl Pembayaran</th>
                            <th class="py-3.5">Nomor Invoice</th>
                            <th class="py-3.5">Pelanggan / Proyek</th>
                            <th class="py-3.5 text-end">Nominal Pembayaran</th>
                            <th class="py-3.5 text-center">Metode</th>
                            <th class="py-3.5">Referensi Bank</th>
                            <th class="py-3.5">Diproses Oleh</th>
                            <th class="py-3.5 text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                            <tr class="stagger-row" style="--row-delay: {{ $index * 0.03 }}s;">
                                <td class="ps-4 text-secondary fw-semibold">{{ $payments->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark">
                                    {{ $pay->payment_date ? $pay->payment_date->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                        #{{ $pay->invoice->invoice_number ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pay->invoice->customer ?? '—' }}</div>
                                    <div class="text-secondary small">{{ $pay->invoice->project_name ?? '—' }}</div>
                                </td>
                                <td class="text-end fw-bold text-success" style="font-size: 0.95rem;">
                                    Rp {{ number_format($pay->payment_amount, 0, ',', '.') }}
                                    @if((float)($pay->pph23_amount ?? 0) > 0)
                                        <small class="text-muted d-block" style="font-size:0.7rem;" title="Potongan PPh 23 (2%)">
                                            + PPh 23: Rp {{ number_format($pay->pph23_amount, 0, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill fw-bold" style="font-size:0.7rem;">
                                        {{ strtoupper($pay->payment_method ?? 'BANK TRANSFER') }}
                                    </span>
                                </td>
                                <td>
                                    <code class="text-dark fw-semibold">{{ $pay->payment_reference ?? '—' }}</code>
                                    @if($pay->bupot_number)
                                        <div class="mt-1">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:0.65rem;" title="Nomor Bukti Potong PPh 23">
                                                <i class="bi bi-file-earmark-check me-0.5"></i>{{ $pay->bupot_number }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $pay->processedBy->name ?? 'System' }}</div>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('payment.receipt', $pay->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 fw-bold shadow-sm btn-action-animated" title="Cetak Kuitansi Resmi (PDF/Print)">
                                        <i class="bi bi-printer me-1"></i> Kuitansi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-cash-stack display-5 d-block mb-2 text-secondary opacity-50"></i>
                                    <div class="fw-bold">Belum Ada Transaksi Pembayaran</div>
                                    <div class="small">Pelunasan invoice akan tercatat secara otomatis setelah pembayaran dimasukkan</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($payments->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan {{ $payments->firstItem() }} - {{ $payments->lastItem() }} dari {{ $payments->total() }} pembayaran</span>
                <div>{{ $payments->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
