<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Portfolio Summary — {{ $smDisplayName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0A2540;
            --secondary: #005A9C;
            --accent: #FF5A00;
            --bg-light: #F8FAFC;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
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
            font-size: 12px;
            line-height: 1.5;
            padding: 24px;
        }

        .report-page {
            max-width: 1100px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 36px 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--border-color);
        }

        /* Top Bar Actions */
        .toolbar {
            max-width: 1100px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #005A9C;
            color: #FFFFFF;
        }
        .btn-primary:hover {
            background: #004578;
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
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0A2540;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .company-title {
            font-size: 16px;
            font-weight: 800;
            color: #0A2540;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .company-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .report-title-section {
            text-align: right;
        }

        .report-title {
            font-size: 18px;
            font-weight: 800;
            color: #005A9C;
            letter-spacing: -0.02em;
        }

        .report-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Info Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            background: #F8FAFC;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 14px;
        }

        .meta-item .lbl {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.04em;
        }

        .meta-item .val {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 2px;
        }

        /* KPI Row */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .kpi-box {
            padding: 10px 8px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #FFFFFF;
            text-align: center;
            border-top: 3px solid #005A9C;
        }

        .kpi-box.kpi-safe { border-top-color: #16A34A; }
        .kpi-box.kpi-warning { border-top-color: #D97706; }
        .kpi-box.kpi-danger { border-top-color: #DC2626; }

        .kpi-box .kpi-lbl {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .kpi-box .kpi-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
            font-variant-numeric: tabular-nums;
        }

        .kpi-box .kpi-sub {
            font-size: 9px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Table */
        .table-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #0A2540;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 24px;
        }

        th {
            background: #F1F5F9;
            color: #334155;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 9px 10px;
            border-bottom: 2px solid #CBD5E1;
            text-align: left;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Kolom nama proyek boleh wrap */
        td:nth-child(2) {
            white-space: normal;
            min-width: 180px;
            max-width: 260px;
        }

        tr:nth-child(even) td {
            background: #F8FAFC;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger  { background: #FEE2E2; color: #991B1B; }

        /* Signatures */
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        .sig-name {
            font-size: 13px;
            font-weight: 800;
            color: #0A2540;
            border-bottom: 1px solid #334155;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 200px;
        }

        .sig-role {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 12mm 8mm 12mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                font-size: 8pt !important;
                line-height: 1.25 !important;
            }

            .toolbar {
                display: none !important;
            }

            .report-page {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            /* Header kompak */
            .report-header {
                padding-bottom: 6pt !important;
                margin-bottom: 6pt !important;
                border-bottom-width: 1.5pt !important;
            }

            .brand-section { gap: 8pt !important; }

            .company-title {
                font-size: 10pt !important;
                line-height: 1.1 !important;
            }

            .company-subtitle { font-size: 7pt !important; }
            .report-title { font-size: 11pt !important; }
            .report-meta { font-size: 7pt !important; }

            /* Logo PGNCOM */
            .brand-section img {
                height: 30pt !important;
                width: auto !important;
            }

            /* Meta grid rapat */
            .meta-grid {
                padding: 5pt 8pt !important;
                gap: 6pt !important;
                margin-bottom: 6pt !important;
                border-radius: 4pt !important;
            }

            .meta-item .lbl { font-size: 6.5pt !important; }
            .meta-item .val { font-size: 8.5pt !important; margin-top: 1pt !important; }

            /* KPI cards rapat */
            .kpi-row {
                gap: 5pt !important;
                margin-bottom: 6pt !important;
            }

            .kpi-box {
                padding: 5pt 4pt !important;
                border-top-width: 2.5pt !important;
                border-radius: 4pt !important;
            }

            .kpi-box .kpi-lbl {
                font-size: 6pt !important;
                margin-bottom: 2pt !important;
                letter-spacing: 0.02em !important;
            }

            .kpi-box .kpi-val { font-size: 9pt !important; }
            .kpi-box .kpi-sub { font-size: 6pt !important; margin-top: 1pt !important; }

            /* Table title */
            .table-title {
                font-size: 8pt !important;
                margin-bottom: 4pt !important;
            }

            /* Table compact — A4 portrait lebih sempit */
            table {
                font-size: 6.5pt !important;
                margin-bottom: 8pt !important;
            }

            th {
                padding: 3.5pt 4pt !important;
                font-size: 6pt !important;
                letter-spacing: 0.01em !important;
            }

            td {
                padding: 3pt 4pt !important;
                vertical-align: middle !important;
            }

            .badge {
                font-size: 6pt !important;
                padding: 1pt 5pt !important;
            }

            /* Signature kompak */
            .signature-section {
                margin-top: 12pt !important;
                gap: 20pt !important;
            }

            .sig-title { margin-bottom: 30pt !important; font-size: 7.5pt !important; }
            .sig-name { font-size: 8pt !important; min-width: 120pt !important; }
            .sig-role { font-size: 7pt !important; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div>
            <a href="javascript:history.back()" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <form method="GET" action="{{ route('sm.executive-summary') }}" style="display: flex; align-items: center; gap: 8px;">
                @if(isset($availableSMs) && $availableSMs->count() > 0 && !auth()->user()->hasRole('osm_service_manager'))
                <label style="font-weight: 700; font-size: 11px; color: #64748B;">Pilih SM:</label>
                <select name="service_manager" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 12px; font-weight: 600; background: #fff; cursor: pointer; color: #0F172A;">
                    <option value="">-- Seluruh Service Manager (Konsolidasi) --</option>
                    @foreach($availableSMs as $sm)
                        <option value="{{ $sm }}" {{ request('service_manager') === $sm ? 'selected' : '' }}>{{ $sm }}</option>
                    @endforeach
                </select>
                @endif
                <label style="font-weight: 700; font-size: 11px; color: #64748B;">Tahun:</label>
                <select name="tahun" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 12px; font-weight: 600; background: #fff; cursor: pointer; color: #0F172A;">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList ?? [] as $th)
                        <option value="{{ $th }}" {{ ($filterTahun ?? '') == $th ? 'selected' : '' }}>Tahun {{ $th }}</option>
                    @endforeach
                </select>
            </form>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer-fill"></i> Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <div class="report-page">
        <!-- Header -->
        <div class="report-header">
            <div class="brand-section">
                <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM" style="height: 42px; width: auto; object-fit: contain; display: block;">
                <div>
                    <div class="company-title">PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)</div>
                    <div class="company-subtitle">Direktorat Infrastruktur &amp; Teknologi &mdash; Monitoring Realisasi Finansial</div>
                </div>
            </div>
            <div class="report-title-section">
                <div class="report-title">RESUME EKSEKUTIF PORTOFOLIO PROYEK</div>
                <div class="report-meta">Ref: PGN/PMO/SM-SUM/{{ date('Ym') }}/{{ str_pad(count($projectItems), 3, '0', STR_PAD_LEFT) }} &bull; Tanggal: {{ now()->locale('id')->isoFormat('D MMMM Y') }}</div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="lbl">Service Manager / PIC</div>
                <div class="val">{{ $smDisplayName }}</div>
            </div>
            <div class="meta-item">
                <div class="lbl">Tahun Anggaran</div>
                <div class="val">{{ $filterTahun ? "Tahun {$filterTahun}" : "Semua Tahun (Konsolidasi)" }}</div>
            </div>
            <div class="meta-item">
                <div class="lbl">Total Portofolio Proyek</div>
                <div class="val">{{ count($projectItems) }} Proyek Aktif</div>
            </div>
            <div class="meta-item">
                <div class="lbl">Status BASTO</div>
                <div class="val">{{ $bastoApprovedTotal }} Selesai / {{ $bastoPendingTotal }} Review</div>
            </div>
        </div>

        <!-- 5 KPI Cards -->
        <div class="kpi-row">
            <div class="kpi-box">
                <div class="kpi-lbl">Total Pagu Kontrak</div>
                <div class="kpi-val">Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ count($projectItems) }} Kontrak</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Total Realisasi</div>
                <div class="kpi-val">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
                <div class="kpi-sub">Biaya Aktual</div>
            </div>
            <div class="kpi-box {{ $persentaseRealisasi >= 90 ? 'kpi-danger' : ($persentaseRealisasi >= 75 ? 'kpi-warning' : 'kpi-safe') }}">
                <div class="kpi-lbl">Serapan Portofolio</div>
                <div class="kpi-val">{{ number_format($persentaseRealisasi, 1) }}%</div>
                <div class="kpi-sub">Rasio Pagu</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Sisa Saldo Pagu</div>
                <div class="kpi-val" style="color: {{ $sisaBudget < 0 ? '#DC2626' : '#16A34A' }};">
                    {{ $sisaBudget < 0 ? '-' : '' }}Rp {{ number_format(abs($sisaBudget), 0, ',', '.') }}
                </div>
                <div class="kpi-sub">Sisa Portofolio</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Estimasi Prognosa</div>
                <div class="kpi-val">Rp {{ number_format($totalPrognosa, 0, ',', '.') }}</div>
                <div class="kpi-sub">Forecast Sisa Tahun</div>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="table-title">
            <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Rincian Finansial & Kesehatan Anggaran per Proyek
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">No</th>
                    <th>Project ID & Nama Proyek</th>
                    <th>Client / Entitas</th>
                    <th class="text-end">Nilai Pagu (Rp)</th>
                    <th class="text-end">Realisasi (Rp)</th>
                    <th class="text-end">Sisa Saldo (Rp)</th>
                    <th class="text-center">Serapan</th>
                    <th class="text-center">BASTO</th>
                    <th class="text-center">Kesehatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projectItems as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $p['project_name'] }}</strong><br>
                        <span style="font-family: monospace; color: var(--text-muted); font-size: 10px;">{{ $p['project_id'] }}</span>
                    </td>
                    <td>{{ $p['client'] }}</td>
                    <td class="text-end font-monospace">Rp {{ number_format($p['pagu'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace" style="font-weight: 700; color: #005A9C;">Rp {{ number_format($p['realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace" style="color: {{ $p['sisa'] < 0 ? '#DC2626; font-weight:700;' : '#16A34A;' }}">
                        {{ $p['sisa'] < 0 ? '-' : '' }}Rp {{ number_format(abs($p['sisa']), 0, ',', '.') }}
                    </td>
                    <td class="text-center font-monospace" style="font-weight: 700;">{{ $p['serapan'] }}%</td>
                    <td class="text-center" style="font-size: 10px;">{{ $p['basto_info'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $p['badge_color'] }}">
                            {{ $p['status_label'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: var(--text-muted);">
                        Tidak ada data proyek aktif untuk Service Manager ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="sig-title">Disiapkan Oleh,</div>
                <div class="sig-name">{{ $smDisplayName }}</div>
                <div class="sig-role">OSM Service Manager</div>
            </div>
            <div class="signature-box">
                <div class="sig-title">Diketahui &amp; Disetujui Oleh,</div>
                <div class="sig-name">{{ \App\Models\AppSetting::getValue('signer_vp_name', 'Dedi Suherman, S.T., M.M.') }}</div>
                <div class="sig-role">{{ \App\Models\AppSetting::getValue('signer_vp_title', 'Vice President OSM / Direktur Infrastruktur') }} &bull; PGNCOM</div>
            </div>
        </div>

    </div>

</body>
</html>
