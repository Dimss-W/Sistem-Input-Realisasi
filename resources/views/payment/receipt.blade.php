<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran #{{ $payment->payment_reference ?: $payment->id }} — PGNCOM Finance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0F2A4A;
            --secondary: #005A9C;
            --accent: #FF5A00;
            --pgncom-navy: #0A2540;
            --pgncom-blue: #005A9C;
            --bg-light: #F8FAFC;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --border-color: #CBD5E1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #F1F5F9;
            color: var(--text-dark);
            font-size: 13px;
            line-height: 1.5;
            padding: 30px 15px;
        }

        .receipt-container {
            max-width: 860px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 44px 48px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--border-color);
            position: relative;
        }

        /* Top Bar Actions */
        .toolbar {
            max-width: 860px;
            margin: 0 auto 18px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(15, 42, 74, 0.2);
        }
        .btn-primary:hover {
            background: #0A1C32;
        }

        .btn-outline {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
        }
        .btn-outline:hover {
            background: #F8FAFC;
        }

        /* Header Document */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2.5px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 26px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo-badge {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0F2A4A 0%, #005A9C 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
            letter-spacing: -0.5px;
            box-shadow: 0 4px 10px rgba(0, 90, 156, 0.25);
        }

        .company-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .company-subtitle {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .company-address {
            font-size: 10.5px;
            color: #94A3B8;
            margin-top: 2px;
        }

        .receipt-title-box {
            text-align: right;
        }

        .receipt-badge-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .receipt-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--secondary);
            margin-top: 4px;
        }

        .receipt-date {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Content Rows */
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .receipt-table td {
            padding: 12px 14px;
            vertical-align: top;
            border-bottom: 1px dashed #E2E8F0;
        }

        .receipt-table td.label-col {
            width: 220px;
            font-weight: 600;
            color: #475569;
            font-size: 12.5px;
        }

        .receipt-table td.colon-col {
            width: 15px;
            text-align: center;
            font-weight: 700;
            color: #64748B;
        }

        .receipt-table td.value-col {
            color: var(--text-dark);
            font-size: 13px;
        }

        /* Terbilang Box */
        .terbilang-card {
            background: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-left: 4px solid var(--secondary);
            padding: 14px 18px;
            border-radius: 6px;
            margin: 16px 0 24px 0;
        }

        .terbilang-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .terbilang-text {
            font-size: 14px;
            font-weight: 700;
            color: #0F172A;
            font-style: italic;
            line-height: 1.4;
        }

        /* Big Amount Display */
        .amount-display-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border: 1.5px solid #86EFAC;
            border-radius: 10px;
            padding: 16px 24px;
            margin-bottom: 30px;
        }

        .amount-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #166534;
        }

        .amount-value {
            font-size: 24px;
            font-weight: 800;
            color: #14532D;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Signatures */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 36px;
            padding-top: 20px;
        }

        .sig-box {
            text-align: center;
        }

        .sig-location {
            font-size: 11.5px;
            color: #475569;
            margin-bottom: 60px;
        }

        .sig-name {
            font-weight: 800;
            font-size: 13.5px;
            color: var(--primary);
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .sig-title {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 600;
        }

        .stamp-mark {
            display: inline-block;
            border: 2px solid #DC2626;
            color: #DC2626;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 4px;
            transform: rotate(-6deg);
            margin-bottom: 8px;
            opacity: 0.85;
        }

        /* Footer Notes */
        .receipt-footer {
            margin-top: 36px;
            border-top: 1px solid #E2E8F0;
            padding-top: 14px;
            font-size: 10.5px;
            color: #94A3B8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .receipt-container {
                border: none !important;
                box-shadow: none !important;
                padding: 20px 24px !important;
                max-width: 100% !important;
            }
            .toolbar {
                display: none !important;
            }
            .amount-display-box {
                border: 1px solid #86EFAC !important;
            }
            .terbilang-card {
                border: 1px solid #CBD5E1 !important;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div>
            <a href="{{ route('payment.history') }}" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Pembayaran
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer-fill"></i> Cetak Kuitansi Resmi / Simpan PDF
            </button>
        </div>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="brand-section">
                <div class="brand-logo-badge">
                    PGN
                </div>
                <div>
                    <div class="company-title">PT TELEKOMUNIKASI INDONESIA INTERNASIONAL / PGNCOM</div>
                    <div class="company-subtitle">Divisi Service Management &amp; Operation (SMO) &bull; Keuangan &amp; Treasury</div>
                    <div class="company-address">Gedung Graha PGAS, Jl. K.H. Zainul Arifin No. 20, Jakarta Barat 11140</div>
                </div>
            </div>
            <div class="receipt-title-box">
                <div class="receipt-badge-title">Kuitansi Pembayaran</div>
                <div class="receipt-number">KWT/{{ $payment->payment_date ? $payment->payment_date->format('Ym') : date('Ym') }}/{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="receipt-date">
                    <i class="bi bi-calendar3"></i> Tanggal: {{ $payment->payment_date ? $payment->payment_date->translatedFormat('d F Y') : '-' }}
                </div>
            </div>
        </div>

        <!-- Receipt Table -->
        <table class="receipt-table">
            <tr>
                <td class="label-col">Telah Diterima Dari</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <strong style="font-size: 14px; color: var(--primary);">
                        {{ $payment->invoice ? ($payment->invoice->customer ?: 'Mitra / Customer Pelanggan') : 'Mitra Terkait' }}
                    </strong>
                </td>
            </tr>
            <tr>
                <td class="label-col">Untuk Pembayaran</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    Pelunasan / Angsuran Invoice <strong>#{{ $payment->invoice ? $payment->invoice->invoice_number : '-' }}</strong>
                    @if($payment->invoice && $payment->invoice->project_name)
                        <br><span style="color: var(--text-muted); font-size: 11.5px;">Proyek: {{ $payment->invoice->project_name }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label-col">Metode Pembayaran</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span style="display: inline-block; background: #EEF2F6; padding: 2px 10px; border-radius: 4px; font-weight: 700; color: #1E293B; font-size: 11.5px;">
                        {{ $payment->payment_method ?: 'BANK TRANSFER' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Nomor Bukti / Referensi Bank</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #005A9C;">
                        {{ $payment->payment_reference ?: '-' }}
                    </span>
                </td>
            </tr>
            @if((float)($payment->pph23_amount ?? 0) > 0)
            <tr>
                <td class="label-col">Pemotongan PPh 23 (2%)</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span style="font-weight: 700; color: #B45309;">
                        Rp {{ number_format($payment->pph23_amount, 0, ',', '.') }}
                    </span>
                    @if($payment->bupot_number)
                        <span style="font-size: 11.5px; color: #475569; margin-left: 8px;">
                            (No. Bukti Potong: <code style="color:#0369A1; font-weight:700;">{{ $payment->bupot_number }}</code>)
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label-col">Total Tagihan Diselesaikan</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <strong style="color: var(--primary);">
                        Rp {{ number_format($payment->payment_amount + $payment->pph23_amount, 0, ',', '.') }}
                    </strong>
                    <span style="font-size: 11px; color: #15803D; margin-left: 6px;">(Kas Masuk + Kredit Pajak PPh 23)</span>
                </td>
            </tr>
            @endif
            @if($payment->notes)
            <tr>
                <td class="label-col">Keterangan / Catatan</td>
                <td class="colon-col">:</td>
                <td class="value-col" style="color: #475569; font-style: italic;">
                    {{ $payment->notes }}
                </td>
            </tr>
            @endif
        </table>

        <!-- Terbilang Box -->
        <div class="terbilang-card">
            <div class="terbilang-title">Terbilang Kas Diterima (Amount in Words):</div>
            <div class="terbilang-text"># {{ $terbilangAmount }} #</div>
        </div>

        <!-- Big Amount Card -->
        <div class="amount-display-box">
            <div>
                <div class="amount-label">Jumlah Pembayaran Diterima</div>
                <div style="font-size: 11px; color: #15803D; margin-top: 2px;">Status Dokumen: Sah & Diverifikasi Sistem Treasury</div>
            </div>
            <div class="amount-value">
                Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-grid">
            <div class="sig-box">
                <div class="sig-location">Mengetahui,</div>
                <div class="stamp-mark">LUNAS / VERIFIED</div>
                <div class="sig-name">{{ $payment->invoice && $payment->invoice->sales ? $payment->invoice->sales->name : 'Account Manager / Sales' }}</div>
                <div class="sig-title">Key Account / Project Sales</div>
            </div>
            <div class="sig-box">
                <div class="sig-location">Jakarta, {{ $payment->payment_date ? $payment->payment_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
                <div style="height: 32px;"></div>
                <div class="sig-name">{{ $payment->processedBy ? $payment->processedBy->name : auth()->user()->name }}</div>
                <div class="sig-title">Perbendaharaan / Finance & Treasury Officer</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div>
                Dicetak secara elektronik melalui <strong>PGN Enterprise Realisasi & Treasury System</strong> pada {{ now()->translatedFormat('d F Y H:i:s') }}
            </div>
            <div>
                ID Transaksi: #{{ $payment->id }} &bull; Dokumen Sah Tanpa Tanda Tangan Basah
            </div>
        </div>
    </div>

</body>
</html>
