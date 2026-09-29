@extends('layouts.main')

@section('title', 'Edit Invoice Vendor #' . $invoice->invoice_number)
@section('page-title', 'Edit Invoice Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}" class="text-decoration-none">Invoice Vendor</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                <i class="bi bi-pencil-square text-primary me-2"></i>Edit Invoice Vendor: {{ $invoice->invoice_number }}
            </h4>
            <p class="text-muted small mb-0">Perbarui rincian faktur tagihan dan perpajakan vendor rekanan.</p>
        </div>
        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-card-text text-primary me-2"></i>Rincian Faktur Tagihan
                        </span>
                        <span class="badge {{ $invoice->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} font-monospace" style="font-size: 0.72rem;">
                            STATUS: {{ strtoupper($invoice->payment_status) }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label fw-semibold text-secondary small mb-1">
                                    Nomor Invoice Vendor <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" class="form-control" required>
                                @error('invoice_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label fw-semibold text-secondary small mb-1">
                                    Proyek Terkait (Project ID) <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id" class="form-select select2-enable" required>
                                    @foreach($projects as $pid => $pname)
                                        <option value="{{ $pid }}" {{ old('project_id', $invoice->project_id) === $pid ? 'selected' : '' }}>
                                            {{ $pid }} &bull; {{ Str::limit($pname, 45) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-7">
                                <label for="customer" class="form-label fw-semibold text-secondary small mb-1">
                                    Nama Penyedia / Vendor <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="customer" id="customer" list="vendorDatalist" value="{{ old('customer', $invoice->customer) }}" class="form-control" required>
                                <datalist id="vendorDatalist">
                                    @if(isset($vendorList))
                                        @foreach($vendorList as $vnd)
                                            <option value="{{ $vnd }}">
                                        @endforeach
                                    @endif
                                </datalist>
                                @error('customer')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-secondary small mb-1">Petugas Pencatat</label>
                                <input type="text" class="form-control bg-light text-muted" value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label fw-semibold text-secondary small mb-1">
                                    Tanggal Terbit Invoice <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" class="form-control" required>
                                @error('invoice_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-semibold text-secondary small mb-1">
                                    Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" class="form-control" required>
                                @error('due_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="subtotal" class="form-label fw-semibold text-secondary small mb-1">
                                    Nilai Pokok / DPP (Subtotal) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                    <input type="number" name="subtotal" id="subtotal" value="{{ old('subtotal', (int)($invoice->subtotal ?? $invoice->invoice_amount)) }}" class="form-control font-monospace fw-bold" required>
                                </div>
                                @error('subtotal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="tax_ppn_percent" class="form-label fw-semibold text-secondary small mb-1">Tarif PPN</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tax_ppn_percent" id="tax_ppn_percent" value="{{ old('tax_ppn_percent', $invoice->tax_ppn_percent ?? 11.00) }}" class="form-control text-center font-monospace">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="tax_pph_percent" class="form-label fw-semibold text-secondary small mb-1">Tarif PPh 23</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tax_pph_percent" id="tax_pph_percent" value="{{ old('tax_pph_percent', $invoice->tax_pph_percent ?? 2.00) }}" class="form-control text-center font-monospace">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold text-secondary small mb-1">Rincian Pengadaan / Catatan Tagihan</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $invoice->notes) }}</textarea>
                                @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom">
                        <span class="fw-bold text-dark small">
                            <i class="bi bi-calculator text-primary me-1"></i>Ringkasan Finansial Tagihan
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">Subtotal / DPP</span>
                            <span class="font-monospace fw-semibold text-dark" id="summarySubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">PPN (<span id="summaryPpnPct">11</span>%)</span>
                            <span class="font-monospace text-info fw-semibold" id="summaryPpnVal">+ Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 small">
                            <span class="text-muted">Potongan PPh 23 (<span id="summaryPphPct">2</span>%)</span>
                            <span class="font-monospace text-danger fw-semibold" id="summaryPphVal">- Rp 0</span>
                        </div>

                        <div class="border-top pt-2.5">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                                Total Tagihan Bersih (Payable)
                            </div>
                            <div class="h4 fw-bold text-primary font-monospace mb-0 mt-1" id="summaryTotal">
                                Rp 0
                            </div>
                            <input type="hidden" name="invoice_amount" id="invoice_amount" value="{{ old('invoice_amount', (int)$invoice->invoice_amount) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="bi bi-check2-circle fs-5"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-light border w-100 py-1.5 fw-semibold text-secondary small">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subtotalInput = document.getElementById('subtotal');
        const ppnInput = document.getElementById('tax_ppn_percent');
        const pphInput = document.getElementById('tax_pph_percent');
        const totalHidden = document.getElementById('invoice_amount');

        const summarySubtotal = document.getElementById('summarySubtotal');
        const summaryPpnPct = document.getElementById('summaryPpnPct');
        const summaryPpnVal = document.getElementById('summaryPpnVal');
        const summaryPphPct = document.getElementById('summaryPphPct');
        const summaryPphVal = document.getElementById('summaryPphVal');
        const summaryTotal = document.getElementById('summaryTotal');

        function updateCalculation() {
            const subtotal = parseFloat(subtotalInput.value) || 0;
            const ppn = parseFloat(ppnInput.value) || 0;
            const pph = parseFloat(pphInput.value) || 0;

            const ppnVal = Math.round(subtotal * (ppn / 100));
            const pphVal = Math.round(subtotal * (pph / 100));
            const total = Math.max(0, subtotal + ppnVal - pphVal);

            totalHidden.value = total > 0 ? total : subtotal;

            summarySubtotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            summaryPpnPct.textContent = ppn;
            summaryPpnVal.textContent = '+ Rp ' + ppnVal.toLocaleString('id-ID');
            summaryPphPct.textContent = pph;
            summaryPphVal.textContent = '- Rp ' + pphVal.toLocaleString('id-ID');
            summaryTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        if (subtotalInput) subtotalInput.addEventListener('input', updateCalculation);
        if (ppnInput) ppnInput.addEventListener('input', updateCalculation);
        if (pphInput) pphInput.addEventListener('input', updateCalculation);

        updateCalculation();
    });
</script>
@endpush
