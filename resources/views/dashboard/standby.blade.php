<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standby Executive Dashboard — PGN Command Center</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-pgncom.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            /* Harmonious Corporate Dark Slate Palette (Comfortable, Zero Glare, Zero Color Clash) */
            --bg-page: #0b1324;
            --bg-panel: #131d33;
            --bg-panel-hover: #182540;
            --bg-subtle: #1c2a47;
            --border-panel: rgba(148, 163, 184, 0.14);
            --border-subtle: rgba(148, 163, 184, 0.22);
            
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            
            /* Curated Corporate Harmony Accents */
            --brand-primary: #38bdf8;
            --brand-blue: #0284c7;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --accent-slate: #64748b;
        }

        * {
            box-sizing: border-box;
            user-select: none;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            max-height: 100vh;
            max-width: 100vw;
            overflow: hidden !important;
            background: linear-gradient(145deg, #090e1a 0%, #0d1629 50%, #111c34 100%);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Fullscreen Container: Strict 100vh Fit */
        .standby-container {
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 6px 12px 6px 12px;
            gap: 6px;
            overflow: hidden;
        }

        /* 1. Sleek Header Bar */
        .header-bar {
            height: 48px;
            min-height: 48px;
            background: var(--bg-panel);
            border: 1px solid var(--border-panel);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-img {
            height: 28px;
            width: auto;
            object-fit: contain;
        }

        .brand-text-title {
            font-size: 0.96rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #f8fafc;
            line-height: 1.1;
        }

        .brand-text-sub {
            font-size: 0.63rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--brand-primary);
        }

        .badge-live-pulse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 700;
            color: #34d399;
            letter-spacing: 0.04em;
        }

        .pulse-core {
            width: 6px;
            height: 6px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
            animation: pulseWave 1.8s infinite ease-in-out;
        }

        @keyframes pulseWave {
            0%, 100% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.4); opacity: 1; box-shadow: 0 0 8px #10b981; }
        }

        .header-clock-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(11, 19, 36, 0.6);
            border: 1px solid var(--border-panel);
            border-radius: 8px;
            padding: 3px 12px;
        }

        .clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.05rem;
            font-weight: 800;
            color: #38bdf8;
            letter-spacing: 0.02em;
        }

        .clock-date {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-secondary);
            border-left: 1px solid var(--border-panel);
            padding-left: 10px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* 10s Circular Countdown */
        .sync-ring-wrapper {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(56, 189, 248, 0.08);
            border: 1px solid rgba(56, 189, 248, 0.25);
            padding: 3px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sync-ring-wrapper:hover {
            background: rgba(56, 189, 248, 0.15);
            border-color: var(--brand-primary);
        }

        .sync-svg-ring {
            width: 18px;
            height: 18px;
            transform: rotate(-90deg);
        }

        .sync-circle-bg {
            fill: none;
            stroke: rgba(255, 255, 255, 0.1);
            stroke-width: 3;
        }

        .sync-circle-bar {
            fill: none;
            stroke: var(--brand-primary);
            stroke-width: 3;
            stroke-dasharray: 44;
            stroke-dashoffset: 0;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.8s linear;
        }

        .sync-counter-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--brand-primary);
        }

        .btn-kiosk {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-panel);
            color: var(--text-secondary);
            font-size: 0.74rem;
            font-weight: 600;
            padding: 5px 11px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-kiosk:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-color: var(--border-subtle);
        }

        .btn-kiosk-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 1px solid #0284c7;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);
        }

        .btn-kiosk-primary:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: #ffffff;
        }

        /* 2. Executive KPI Row (Dark Slate, Harmonious High Contrast) */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            height: 84px;
            min-height: 84px;
        }

        .kpi-tile {
            background: var(--bg-panel);
            border: 1px solid var(--border-panel);
            border-radius: 10px;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .kpi-tile:hover {
            transform: translateY(-2px);
            border-color: var(--border-subtle);
        }

        .kpi-tile-blue { border-left: 4px solid #38bdf8; }
        .kpi-tile-emerald { border-left: 4px solid #10b981; }
        .kpi-tile-slate { border-left: 4px solid #818cf8; }
        .kpi-tile-amber { border-left: 4px solid #38bdf8; }

        .kpi-tile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kpi-tile-label {
            font-size: 0.67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
        }

        .kpi-tile-icon {
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .kpi-tile-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.25rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .kpi-tile-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.67rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* 3. Main 2x2 Analytical Charts Grid */
        .charts-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 6px;
            min-height: 0;
            overflow: hidden;
        }

        .glass-box {
            background: var(--bg-panel);
            border: 1px solid var(--border-panel);
            border-radius: 10px;
            padding: 8px 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            transition: border-color 0.2s ease;
        }

        .glass-box:hover {
            border-color: rgba(148, 163, 184, 0.28);
        }

        .box-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }

        .box-title {
            font-size: 0.77rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
        }

        .chart-wrapper {
            position: relative;
            flex: 1;
            width: 100%;
            height: 100%;
            min-height: 0;
            overflow: hidden;
            padding: 2px 4px;
        }

        /* Toggle Buttons */
        .chart-toggle-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-panel);
            color: var(--text-secondary);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-toggle-btn.active {
            background: rgba(56, 189, 248, 0.18);
            border-color: var(--brand-primary);
            color: #38bdf8;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.25);
        }

        .badge-kpi-sub {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.02em;
        }

        /* Status Soft Badges */
        .badge-status-soft {
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .status-safe { background: rgba(16, 185, 129, 0.14); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-warning { background: rgba(245, 158, 11, 0.14); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-critical { background: rgba(244, 63, 94, 0.14); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.3); }

        /* Donut Center Overlay */
        .donut-center-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .donut-center-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.25rem;
            font-weight: 800;
            color: #34d399;
            line-height: 1;
        }

        .donut-center-label {
            font-size: 0.62rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Classification & Risk Mini Cards */
        .side-info-grid {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            gap: 4px;
        }

        .risk-bar-container {
            background: rgba(11, 19, 36, 0.5);
            border: 1px solid var(--border-panel);
            border-radius: 7px;
            padding: 5px 8px;
        }

        .risk-bar-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.63rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 3px;
        }

        .progress-stacked-bar {
            height: 6px;
            display: flex;
            border-radius: 3px;
            overflow: hidden;
            background: #1e293b;
        }

        .class-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }

        .class-card {
            background: rgba(11, 19, 36, 0.4);
            border: 1px solid var(--border-panel);
            border-radius: 6px;
            padding: 4px 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .class-card-title {
            font-size: 0.61rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .class-card-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            font-weight: 800;
            color: #f8fafc;
        }

        .class-card-sub {
            font-size: 0.59rem;
            color: #38bdf8;
            font-weight: 600;
        }

        /* 4. Live Marquee Ticker Strip */
        .ticker-strip {
            height: 25px;
            min-height: 25px;
            background: #090e1a;
            border: 1px solid var(--border-panel);
            border-radius: 6px;
            display: flex;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .ticker-label {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0 10px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 2;
            flex-shrink: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.4);
        }

        .ticker-content {
            white-space: nowrap;
            display: inline-block;
            animation: marqueeScroll 34s linear infinite;
            font-size: 0.68rem;
            font-weight: 600;
            color: #94a3b8;
            padding-left: 20px;
        }

        .ticker-content strong {
            color: #38bdf8;
        }

        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .ticker-strip:hover .ticker-content {
            animation-play-state: paused;
        }

        /* Pulse Highlight on Data Sync */
        .sync-wave-active {
            animation: syncFlash 0.9s ease;
        }

        @keyframes syncFlash {
            0% { box-shadow: 0 0 0 2px #38bdf8; }
            100% { box-shadow: none; }
        }

        /* Fullscreen Toast */
        .fs-toast {
            position: fixed;
            bottom: 34px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #0f172a;
            border: 1px solid #38bdf8;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .fs-toast.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

<div class="standby-container" id="kioskContainer">
    <!-- 1. HEADER BAR -->
    <header class="header-bar">
        <div class="header-left">
            <img src="{{ asset('assets/images/logo-pgncom-white.png') }}" alt="PGNCOM Logo" class="brand-logo-img" style="height: 36px; width: auto; object-fit: contain;">
            <div>
                <div class="brand-text-title">PGNCOM EXECUTIVE REALISASI MONITORING</div>
                <div class="brand-text-sub">Command Center Standby Dashboard • PT PGAS Telekomunikasi Nusantara</div>
            </div>
            <div class="badge-live-pulse ms-2">
                <span class="pulse-core"></span>
                <span>LIVE FEED</span>
            </div>
        </div>

        <div class="header-clock-pill">
            <i class="bi bi-clock-fill text-info" style="font-size: 0.88rem;"></i>
            <span class="clock-time" id="clockTime">--:--:-- WIB</span>
            <span class="clock-date" id="clockDate">Memuat tanggal...</span>
        </div>

        <div class="header-actions">
            <!-- 10-Second Countdown with Radial Progress Bar -->
            <div class="sync-ring-wrapper" id="btnSyncTrigger" title="Siklus refresh otomatis 10 detik. Klik untuk sinkronisasi instan.">
                <svg class="sync-svg-ring" viewBox="0 0 18 18">
                    <circle class="sync-circle-bg" cx="9" cy="9" r="7"></circle>
                    <circle class="sync-circle-bar" id="syncProgressCircle" cx="9" cy="9" r="7"></circle>
                </svg>
                <span class="sync-counter-text" id="countdownDisplay">10s</span>
            </div>

            <!-- FULLSCREEN TOGGLE BUTTON -->
            <button class="btn-kiosk btn-kiosk-primary" id="btnTrueFullscreen" title="Layar Penuh Sejati (Tekan F11 pada Keyboard)">
                <i class="bi bi-arrows-fullscreen" id="fsIcon"></i>
                <span id="fsLabel">Layar Penuh [F11]</span>
            </button>

            <a href="{{ route('login') }}" class="btn-kiosk" title="Menuju Halaman Login Portal">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        </div>
    </header>

    <!-- 2. EXECUTIVE 4 KPI CARDS (Harmonious Dark Slate Theme) -->
    <section class="kpi-row" id="kpiRowContainer">
        <!-- KPI 1: Pagu Anggaran Total -->
        <div class="kpi-tile kpi-tile-blue" title="Total pagu anggaran seluruh portofolio kontrak aktif">
            <div class="kpi-tile-header">
                <span class="kpi-tile-label">Pagu Anggaran Total</span>
                <i class="bi bi-briefcase-fill text-info kpi-tile-icon"></i>
            </div>
            <div class="kpi-tile-value" id="valPagu">Rp {{ number_format($data['summary']['total_pagu'], 0, ',', '.') }}</div>
            <div class="kpi-tile-footer">
                <span>{{ $data['summary']['total_proyek'] }} Proyek Aktif</span>
                <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25" style="font-size: 0.65rem;">Tahun {{ $data['summary']['current_year'] }}</span>
            </div>
        </div>

        <!-- KPI 2: Realisasi Biaya Kumulatif -->
        <div class="kpi-tile kpi-tile-emerald" title="Total biaya aktual yang telah terserap">
            <div class="kpi-tile-header">
                <span class="kpi-tile-label">Realisasi Biaya Kumulatif</span>
                <i class="bi bi-speedometer2 text-success kpi-tile-icon"></i>
            </div>
            <div class="kpi-tile-value text-success" id="valRealisasi">Rp {{ number_format($data['summary']['total_realisasi'], 0, ',', '.') }}</div>
            <div class="kpi-tile-footer">
                <span>Serapan: <strong id="valSerapanPct" class="text-success">{{ $data['summary']['serapan_pct'] }}%</strong></span>
                <span>{{ number_format($data['summary']['total_transaksi']) }} Transaksi</span>
            </div>
        </div>

        <!-- KPI 3: Sisa Pagu Anggaran -->
        <div class="kpi-tile kpi-tile-slate" title="Sisa kapasitas anggaran pagu yang belum terserap">
            <div class="kpi-tile-header">
                <span class="kpi-tile-label">Sisa Anggaran Tersedia</span>
                <i class="bi bi-shield-check text-primary kpi-tile-icon"></i>
            </div>
            <div class="kpi-tile-value text-info" id="valSisa">Rp {{ number_format($data['summary']['sisa_anggaran'], 0, ',', '.') }}</div>
            <div class="kpi-tile-footer">
                <span>Kapasitas Belum Terserap</span>
                <span class="text-info fw-bold" id="valSisaPct">
                    {{ $data['summary']['total_pagu'] > 0 ? round(($data['summary']['sisa_anggaran'] / $data['summary']['total_pagu']) * 100, 1) : 0 }}% Sisa
                </span>
            </div>
        </div>

        <!-- KPI 4: Kesehatan Portofolio Proyek -->
        <div class="kpi-tile kpi-tile-amber" title="Distribusi status kepatuhan anggaran proyek">
            <div class="kpi-tile-header">
                <span class="kpi-tile-label">Kesehatan Portofolio Proyek</span>
                <i class="bi bi-pie-chart-fill text-warning kpi-tile-icon"></i>
            </div>
            <div class="kpi-tile-value d-flex align-items-center gap-1" id="valHealthSummary">
                <span class="badge-status-soft status-safe">{{ $data['health_summary']['safe'] ?? 0 }} Sehat</span>
                <span class="badge-status-soft status-warning">{{ $data['health_summary']['warning'] ?? 0 }} Waspada</span>
                <span class="badge-status-soft status-critical">{{ $data['health_summary']['critical'] ?? 0 }} Kritis</span>
            </div>
            <div class="kpi-tile-footer">
                <span>Evaluasi Realisasi vs Pagu</span>
                <span class="text-muted">{{ $data['summary']['total_proyek'] }} Proyek Terpantau</span>
            </div>
        </div>
    </section>

    <!-- 3. MAIN SECTION: 4 HIGH-VALUE EXECUTIVE ANALYTICAL CHARTS (2x2 GRID) -->
    <section class="charts-grid">
        <!-- CHART 1 (Top Left): Tren Penyerapan Bulanan (Realisasi vs Prognosa Target) -->
        <div class="glass-box">
            <div class="box-title-bar">
                <h3 class="box-title">
                    <i class="bi bi-graph-up-arrow text-info"></i>
                    <span>Tren Penyerapan Bulanan ({{ $data['summary']['current_year'] }})</span>
                </h3>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-status-soft status-safe" style="font-size: 0.65rem;" title="Bulan dengan penyerapan belanja terbesar">
                        <i class="bi bi-arrow-up-circle"></i> Puncak: Mar (Rp 56,2 M)
                    </span>
                    <div class="d-flex align-items-center gap-1">
                        <button class="chart-toggle-btn active" id="btnToggleAll" onclick="setTrendMode('all')">Semua</button>
                        <button class="chart-toggle-btn" id="btnToggleReal" onclick="setTrendMode('realisasi')">Realisasi</button>
                        <button class="chart-toggle-btn" id="btnToggleProg" onclick="setTrendMode('prognosa')">Prognosa</button>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between px-1 mb-1" style="font-size: 0.65rem; color: #cbd5e1;">
                <span><span style="color: #38bdf8; font-weight:700;">● Realisasi Aktual</span>: Jan - Jul &bull; <span style="color: #f59e0b; font-weight:700;">-- Prognosa Estimasi</span>: Jul - Des</span>
                <span class="text-warning fw-bold">Target Belanja S2: Rp {{ number_format($data['summary']['total_prognosa'] / 1e9, 1, ',', '.') }} M</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="chartMonthlyTrend"></canvas>
            </div>
        </div>

        <!-- CHART 2 (Top Right): Rasio Penyerapan & Matriks Risiko Portofolio -->
        <div class="glass-box">
            <div class="box-title-bar">
                <h3 class="box-title">
                    <i class="bi bi-pie-chart-fill text-info"></i>
                    <span>Rasio Penyerapan &amp; Kategori Kontrak</span>
                </h3>
                <span class="badge-kpi-sub bg-success bg-opacity-10 text-success border border-success border-opacity-25" id="badgeSerapanOverview">
                    Serapan: {{ $data['summary']['serapan_pct'] }}%
                </span>
            </div>
            <div class="chart-wrapper d-flex align-items-center" style="gap: 14px;">
                <!-- Left Donut Chart -->
                <div style="position: relative; width: 44%; height: 100%;">
                    <canvas id="chartBudgetDonut"></canvas>
                    <div class="donut-center-overlay">
                        <div class="donut-center-number" id="centerPctText">{{ $data['summary']['serapan_pct'] }}%</div>
                        <div class="donut-center-label">Terserap</div>
                        <div style="font-size: 0.6rem; color: #94a3b8; font-weight: 600;">Sisa: {{ round(100 - $data['summary']['serapan_pct'], 1) }}%</div>
                    </div>
                </div>
                <!-- Right Side: Portfolio Health Bar + Classification -->
                <div style="flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <!-- Health Progress Bar Stack -->
                    <div class="risk-bar-container">
                        <div class="risk-bar-label-row">
                            <span>Status Kesehatan Portofolio</span>
                            <span class="text-light">{{ $data['summary']['total_proyek'] }} Proyek Terpantau</span>
                        </div>
                        @php
                            $totK = max(1, $data['summary']['total_proyek']);
                            $pctSafe = round((($data['health_summary']['safe'] ?? 0) / $totK) * 100);
                            $pctWarn = round((($data['health_summary']['warning'] ?? 0) / $totK) * 100);
                            $pctCrit = 100 - $pctSafe - $pctWarn;
                            $classMeta = [
                                'MRC'    => ['full' => 'Rutin Bulanan', 'desc' => 'Monthly Recurring'],
                                'MRC+WO' => ['full' => 'Rutin & SPK', 'desc' => 'Bulanan + Work Order'],
                                'WO'     => ['full' => 'Pekerjaan Khusus', 'desc' => 'Work Order'],
                                'OTC'    => ['full' => 'Sekali Bayar', 'desc' => 'One-Time Charge'],
                            ];
                        @endphp
                        <div class="progress-stacked-bar mb-1">
                            <div style="width: {{ $pctSafe }}%; background: #10b981;" title="Sehat (<75%): {{ $data['health_summary']['safe'] ?? 0 }}"></div>
                            <div style="width: {{ $pctWarn }}%; background: #f59e0b;" title="Waspada (75-90%): {{ $data['health_summary']['warning'] ?? 0 }}"></div>
                            <div style="width: {{ $pctCrit }}%; background: #f43f5e;" title="Kritis (≥90%): {{ $data['health_summary']['critical'] ?? 0 }}"></div>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size: 0.58rem;">
                            <span class="text-success">● {{ $data['health_summary']['safe'] ?? 0 }} Sehat (&lt;75%)</span>
                            <span class="text-warning">● {{ $data['health_summary']['warning'] ?? 0 }} Waspada (75-90%)</span>
                            <span class="text-danger">● {{ $data['health_summary']['critical'] ?? 0 }} Kritis (&ge;90%)</span>
                        </div>
                    </div>

                    <!-- Classification Mini Cards with Meaningful Labels -->
                    <div class="class-grid" id="classPillsGrid">
                        @foreach($data['charts']['chart4_komparasi']['classifications']['labels'] ?? ['MRC', 'MRC+WO', 'WO', 'OTC'] as $idx => $cLabel)
                            <div class="class-card" title="{{ $classMeta[$cLabel]['desc'] ?? $cLabel }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="class-card-title fw-bold text-light">{{ $cLabel }}</div>
                                    <span class="badge bg-secondary bg-opacity-25" style="font-size: 0.58rem; color: #38bdf8;">{{ $data['charts']['chart4_komparasi']['classifications']['pcts'][$idx] ?? 0 }}% Pagu</span>
                                </div>
                                <div style="font-size: 0.58rem; color: #94a3b8;">{{ $classMeta[$cLabel]['full'] ?? 'Kategori' }}</div>
                                <div class="class-card-val text-success">Rp {{ number_format(($data['charts']['chart4_komparasi']['classifications']['values'][$idx] ?? 0) / 1e9, 1) }} M</div>
                                <div class="class-card-sub">{{ $data['charts']['chart4_komparasi']['classifications']['counts'][$idx] ?? 0 }} Kontrak</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART 3 (Bottom Left): Early Warning: Top Proyek Rasio Serapan Tertinggi (Kritis) / Beban SM -->
        <div class="glass-box">
            <div class="box-title-bar">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="box-title">
                        <i class="bi bi-shield-exclamation text-info"></i>
                        <span id="chart3TitleText">Early Warning: Proyek Serapan Tertinggi</span>
                    </h3>
                    <span class="badge-status-soft status-critical" id="badgeCriticalCount" title="Proyek dengan serapan melebihi 100% nilai kontrak">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $data['charts']['chart4_komparasi']['critical_projects']['over_count'] ?? 6 }} Over-Budget (>100%)
                    </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="chart-toggle-btn active" id="btnToggleCritical" onclick="setChart3Mode('critical')">Proyek Kritis</button>
                    <button class="chart-toggle-btn" id="btnToggleTopPagu" onclick="setChart3Mode('top_pagu')">Pagu Terbesar</button>
                    <button class="chart-toggle-btn" id="btnToggleSM" onclick="setChart3Mode('sm')">Distribusi SM</button>
                </div>
            </div>
            <div class="d-flex justify-content-between px-1 mb-1" style="font-size: 0.65rem; color: #cbd5e1;">
                <span id="chart3Subtitle"><span class="text-danger fw-bold">■ Realisasi Aktual</span> melebihi <span class="text-info fw-bold">■ Pagu Kontrak</span> &bull; Memerlukan addendum/evaluasi</span>
                <span class="text-secondary">Sorted by % Serapan</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="chartOrangKontrak"></canvas>
            </div>
        </div>

        <!-- CHART 4 (Bottom Right): Top Vendor: Nilai Kontrak vs Realisasi Belanja -->
        <div class="glass-box">
            <div class="box-title-bar">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="box-title">
                        <i class="bi bi-building-fill text-info"></i>
                        <span>Top Rekanan: Realisasi Belanja Vendor</span>
                    </h3>
                    <span class="badge-kpi-sub bg-info bg-opacity-10 text-info border border-info border-opacity-25" id="badgeVendorShare">
                        Top 2 Vendor = {{ $data['charts']['chart3_vendor_realisasi']['top2_share'] ?? 54.2 }}% Belanja
                    </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="chart-toggle-btn active" id="btnToggleVendorReal" onclick="setVendorMode('realisasi')">Realisasi Belanja</button>
                    <button class="chart-toggle-btn" id="btnToggleVendorPagu" onclick="setVendorMode('pagu')">Total Pagu</button>
                </div>
            </div>
            <div class="d-flex justify-content-between px-1 mb-1" style="font-size: 0.65rem; color: #cbd5e1;">
                <span>Distribusi Pangsa Belanja Rekanan Vendor Terbesar</span>
                <span class="text-success fw-bold">Total Belanja: Rp {{ number_format($data['summary']['total_realisasi'] / 1e9, 1, ',', '.') }} M</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="chartTopVendorCompare"></canvas>
            </div>
        </div>
    </section>

    <!-- 4. LIVE OPERATIONAL MARQUEE TICKER (Dark Slate Strip) -->
    <div class="ticker-strip">
        <div class="ticker-label">
            <i class="bi bi-broadcast"></i>
            <span>LIVE MONITORING</span>
        </div>
        <div class="ticker-content" id="liveTickerText">
            <span>⚡ <strong>SERAPAN ANGGARAN:</strong> Rp {{ number_format($data['summary']['total_realisasi'], 0, ',', '.') }} ({{ $data['summary']['serapan_pct'] }}% dari Pagu Rp {{ number_format($data['summary']['total_pagu'], 0, ',', '.') }})</span>
            <span class="mx-3 text-secondary">•</span>
            <span>⚠️ <strong>PERHATIAN EKSEKUTIF:</strong> {{ $data['charts']['chart4_komparasi']['critical_projects']['over_count'] ?? 6 }} Proyek mengalami serapan >100% pagu (tertinggi PS-049-00 di 175,6%)</span>
            <span class="mx-3 text-secondary">•</span>
            <span>🏢 <strong>KONSENTRASI REKANAN:</strong> Top 2 Vendor (PT Packet Systems & Persada) menyerap {{ $data['charts']['chart3_vendor_realisasi']['top2_share'] ?? 54.2 }}% total belanja portofolio</span>
            <span class="mx-3 text-secondary">•</span>
            <span>📈 <strong>OUTLOOK SEMESTER 2:</strong> Estimasi kebutuhan kas belanja prognosa sisa tahun adalah Rp {{ number_format($data['summary']['total_prognosa'], 0, ',', '.') }}</span>
            <span class="mx-3 text-secondary">•</span>
            <span>🛡️ <strong>SISA ANGGARAN:</strong> Rp {{ number_format($data['summary']['sisa_anggaran'], 0, ',', '.') }} ({{ round(100 - $data['summary']['serapan_pct'], 1) }}% Kapasitas Tersedia)</span>
            <span class="mx-3 text-secondary">•</span>
            <span>👥 <strong>DISTRIBUSI SM:</strong> {{ $data['charts']['chart1_orang']['total_sm'] }} Service Manager mengelola {{ $data['summary']['total_proyek'] }} Kontrak Aktif</span>
            <span class="mx-3 text-secondary">•</span>
            <span>🔄 <strong>SINKRONISASI WAKTU-NYATA:</strong> Siklus pembaruan 10 detik • PGN Enterprise Monitoring</span>
        </div>
    </div>
</div>

<!-- Floating Toast for Fullscreen Guidance -->
<div class="fs-toast" id="fsToast">
    <i class="bi bi-info-circle-fill text-info"></i>
    <span id="fsToastMsg">Tekan tombol <strong>F11</strong> pada keyboard untuk layar penuh tanpa tab browser!</span>
</div>

<!-- Scripts -->
<script>
    // 1. LIVE DIGITAL CLOCK & DATE (WIB)
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('clockTime').textContent = `${hours}:${minutes}:${seconds} WIB`;

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('clockDate').textContent = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. FULLSCREEN CONTROLLER
    const btnFullscreen = document.getElementById('btnTrueFullscreen');
    const fsIcon = document.getElementById('fsIcon');
    const fsLabel = document.getElementById('fsLabel');
    const fsToast = document.getElementById('fsToast');

    function showToast(msg) {
        if (msg) document.getElementById('fsToastMsg').innerHTML = msg;
        fsToast.classList.add('show');
        setTimeout(() => fsToast.classList.remove('show'), 4000);
    }

    function toggleTrueFullscreen() {
        const docEl = document.documentElement;
        const isFS = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;

        if (!isFS) {
            const requestFS = docEl.requestFullscreen || docEl.webkitRequestFullscreen || docEl.mozRequestFullScreen || docEl.msRequestFullscreen;
            if (requestFS) {
                requestFS.call(docEl).then(() => {
                    showToast("Mode Layar Penuh Aktif! Tekan <strong>F11</strong> atau <strong>ESC</strong> untuk keluar.");
                }).catch(err => {
                    showToast("Untuk layar penuh, tekan tombol <strong>F11</strong> pada keyboard Anda!");
                });
            } else {
                showToast("Browser tidak mendukung otomatis fullscreen, silakan tekan <strong>F11</strong>.");
            }
        } else {
            const exitFS = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
            if (exitFS) exitFS.call(document);
        }
    }

    btnFullscreen.addEventListener('click', toggleTrueFullscreen);

    function updateFsButtonState() {
        const isFS = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
        if (isFS) {
            fsIcon.className = 'bi bi-fullscreen-exit';
            fsLabel.textContent = 'Keluar [F11]';
        } else {
            fsIcon.className = 'bi bi-arrows-fullscreen';
            fsLabel.textContent = 'Layar Penuh [F11]';
        }
    }

    document.addEventListener('fullscreenchange', updateFsButtonState);
    document.addEventListener('webkitfullscreenchange', updateFsButtonState);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'f' || e.key === 'F') {
            if (['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase())) return;
            toggleTrueFullscreen();
        }
    });

    // 3. CHART.JS CONFIGURATION (Curated Dark Slate, High Legibility, Zero Color Clash)
    let chartData = @json($data['charts']);
    let currentChart3Mode = 'critical';
    let currentVendorMode = 'realisasi';

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
    }

    function formatShortCurrency(value) {
        if (Math.abs(value) >= 1e12) return (value / 1e12).toFixed(1) + 'T';
        if (Math.abs(value) >= 1e9) return (value / 1e9).toFixed(1) + 'M';
        if (Math.abs(value) >= 1e6) return (value / 1e6).toFixed(0) + 'Jt';
        return value;
    }

    // Unified Dark Slate Tooltip Theme
    const darkTooltipOptions = {
        backgroundColor: '#0f172a',
        titleColor: '#ffffff',
        bodyColor: '#e2e8f0',
        borderColor: 'rgba(56, 189, 248, 0.4)',
        borderWidth: 1,
        padding: 8,
        titleFont: { size: 11, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
        bodyFont: { size: 10, family: "'Plus Jakarta Sans', sans-serif" }
    };

    // --- CHART 1 (Top Left): Tren Penyerapan Bulanan (Area Chart) ---
    const ctxMonthly = document.getElementById('chartMonthlyTrend').getContext('2d');
    const monthlyLabels = chartData.chart4_komparasi.monthly?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthlyReal = chartData.chart4_komparasi.monthly?.realisasi || [];
    const monthlyProg = chartData.chart4_komparasi.monthly?.prognosa || [];

    const chartMonthlyTrend = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: 'Realisasi Biaya Aktual',
                    data: monthlyReal,
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#0f172a',
                    pointBorderWidth: 1.5,
                    pointRadius: 3.5,
                    pointHoverRadius: 6
                },
                {
                    label: 'Prognosa Estimasi',
                    data: monthlyProg,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.05)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    borderDash: [5, 4],
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#0f172a',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 10,
                        color: '#94a3b8',
                        font: { size: 9, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 6
                    }
                },
                tooltip: {
                    ...darkTooltipOptions,
                    callbacks: {
                        title: function(items) {
                            const b = items[0].label;
                            return `Periode: ${b} {{ $data['summary']['current_year'] }}`;
                        },
                        label: function(context) {
                            const val = formatRupiah(context.parsed.y);
                            return `${context.dataset.label}: ${val}`;
                        },
                        footer: function(items) {
                            const m = items[0].label;
                            if (m === 'Mar') return '⚡ Puncak Realisasi Tahunan (Rp 56,2 M)';
                            if (m === 'Jul') return '📌 Titik Transisi Realisasi Aktual ke Prognosa';
                            if (m === 'Des') return '🎯 Target Penutupan Buku Tahun {{ $data['summary']['current_year'] }}';
                            return '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 9, family: "'Plus Jakarta Sans', sans-serif" } }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: "'JetBrains Mono', monospace" },
                        callback: formatShortCurrency
                    }
                }
            }
        }
    });

    function setTrendMode(mode) {
        document.querySelectorAll('#btnToggleAll, #btnToggleReal, #btnToggleProg').forEach(b => b.classList.remove('active'));
        if (mode === 'all') {
            document.getElementById('btnToggleAll').classList.add('active');
            chartMonthlyTrend.data.datasets[0].hidden = false;
            chartMonthlyTrend.data.datasets[1].hidden = false;
        } else if (mode === 'realisasi') {
            document.getElementById('btnToggleReal').classList.add('active');
            chartMonthlyTrend.data.datasets[0].hidden = false;
            chartMonthlyTrend.data.datasets[1].hidden = true;
        } else if (mode === 'prognosa') {
            document.getElementById('btnToggleProg').classList.add('active');
            chartMonthlyTrend.data.datasets[0].hidden = true;
            chartMonthlyTrend.data.datasets[1].hidden = false;
        }
        chartMonthlyTrend.update();
    }

    // --- CHART 2 (Top Right): Rasio Serapan Donut & Classification ---
    const ctxDonut = document.getElementById('chartBudgetDonut').getContext('2d');
    const totalPagu = {{ (float)$data['summary']['total_pagu'] }};
    const totalReal = {{ (float)$data['summary']['total_realisasi'] }};
    const totalSisa = {{ (float)$data['summary']['sisa_anggaran'] }};

    const chartBudgetDonut = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Realisasi Terpakai', 'Sisa Anggaran Tersedia'],
            datasets: [{
                data: [totalReal, totalSisa],
                backgroundColor: ['#10b981', 'rgba(148, 163, 184, 0.16)'],
                borderWidth: 1.5,
                borderColor: ['#10b981', 'rgba(148, 163, 184, 0.3)'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...darkTooltipOptions,
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${formatRupiah(context.parsed)}`;
                        }
                    }
                }
            }
        }
    });

    // --- CHART 3 (Bottom Left): Early Warning / Proyek Kritis / SM Workload ---
    const ctxChart3 = document.getElementById('chartOrangKontrak').getContext('2d');

    function getChart3Data(mode) {
        if (mode === 'critical') {
            const crit = chartData.chart4_komparasi.critical_projects || { labels: [], realisasi: [], pagu: [], pct: [] };
            return {
                labels: crit.labels,
                datasets: [
                    {
                        label: 'Realisasi Biaya Aktual',
                        data: crit.realisasi,
                        backgroundColor: '#f43f5e',
                        borderRadius: 4,
                        maxBarThickness: 11
                    },
                    {
                        label: 'Pagu Kontrak Terdaftar',
                        data: crit.pagu,
                        backgroundColor: '#0284c7',
                        borderRadius: 4,
                        maxBarThickness: 11
                    }
                ],
                title: 'Early Warning: Proyek Serapan Tertinggi'
            };
        } else if (mode === 'top_pagu') {
            const proj = chartData.chart4_komparasi.projects;
            return {
                labels: proj.labels,
                datasets: [
                    {
                        label: 'Pagu Anggaran Kontrak',
                        data: proj.pagu,
                        backgroundColor: '#0284c7',
                        borderRadius: 4,
                        maxBarThickness: 11
                    },
                    {
                        label: 'Realisasi Biaya Terserap',
                        data: proj.realisasi,
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        maxBarThickness: 11
                    }
                ],
                title: 'Top Proyek: Pagu Anggaran Terbesar'
            };
        } else {
            // mode === 'sm'
            return {
                labels: chartData.chart1_orang.sm.labels,
                datasets: [{
                    label: 'Kontrak Dikelola',
                    data: chartData.chart1_orang.sm.data,
                    backgroundColor: '#38bdf8',
                    borderRadius: 4,
                    maxBarThickness: 14
                }],
                title: 'Distribusi Kontrak per Personel SM'
            };
        }
    }

    const initC3 = getChart3Data('critical');
    const chartOrang = new Chart(ctxChart3, {
        type: 'bar',
        data: {
            labels: initC3.labels,
            datasets: initC3.datasets
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            layout: {
                padding: { left: 8, right: 14 }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 9,
                        color: '#94a3b8',
                        font: { size: 9, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 6
                    }
                },
                tooltip: {
                    ...darkTooltipOptions,
                    callbacks: {
                        title: function(items) {
                            const idx = items[0].dataIndex;
                            if (currentChart3Mode === 'critical') {
                                return (chartData.chart4_komparasi.critical_projects.full_names && chartData.chart4_komparasi.critical_projects.full_names[idx]) || items[0].label;
                            }
                            return items[0].label;
                        },
                        label: function(context) {
                            if (currentChart3Mode === 'sm') {
                                return `Beban Kontrak: ${context.parsed.x} Proyek Dikelola`;
                            }
                            const val = formatRupiah(context.parsed.x);
                            if (currentChart3Mode === 'critical') {
                                const idx = context.dataIndex;
                                const pct = (chartData.chart4_komparasi.critical_projects.pct && chartData.chart4_komparasi.critical_projects.pct[idx]) || 0;
                                if (context.datasetIndex === 0) {
                                    const over = pct > 100 ? ` (OVER +${(pct - 100).toFixed(1)}%)` : '';
                                    return `Realisasi: ${val} [Serapan: ${pct}%${over}]`;
                                } else {
                                    return `Pagu Kontrak: ${val}`;
                                }
                            }
                            return `${context.dataset.label}: ${val}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: "'JetBrains Mono', monospace" },
                        callback: function(val) {
                            return currentChart3Mode === 'sm' ? val : formatShortCurrency(val);
                        }
                    }
                },
                y: {
                    afterFit: function(axis) {
                        axis.width += 24;
                    },
                    grid: { color: 'rgba(255, 255, 255, 0.02)', drawBorder: false },
                    ticks: {
                        color: '#cbd5e1',
                        font: { size: 9, weight: '600', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 6,
                        callback: function(val, idx) {
                            const lbl = this.getLabelForValue(idx) || '';
                            return lbl.length > 28 ? lbl.substr(0, 26) + '..' : lbl;
                        }
                    }
                }
            }
        }
    });

    function setChart3Mode(mode) {
        currentChart3Mode = mode;
        document.getElementById('btnToggleCritical').classList.toggle('active', mode === 'critical');
        document.getElementById('btnToggleTopPagu').classList.toggle('active', mode === 'top_pagu');
        document.getElementById('btnToggleSM').classList.toggle('active', mode === 'sm');

        const cfg = getChart3Data(mode);
        document.getElementById('chart3TitleText').textContent = cfg.title;
        chartOrang.data.labels = cfg.labels;
        chartOrang.data.datasets = cfg.datasets;
        chartOrang.options.plugins.legend.display = (mode !== 'sm');

        const subEl = document.getElementById('chart3Subtitle');
        if (subEl) {
            if (mode === 'critical') {
                subEl.innerHTML = '<span class="text-danger fw-bold">■ Realisasi Aktual</span> melebihi <span class="text-info fw-bold">■ Pagu Kontrak</span> &bull; Memerlukan addendum/evaluasi';
            } else if (mode === 'top_pagu') {
                subEl.innerHTML = '<span class="text-info fw-bold">■ Pagu Anggaran Terbesar</span> vs <span class="text-success fw-bold">■ Realisasi Terserap</span> &bull; Portofolio Nilai Tertinggi';
            } else {
                subEl.innerHTML = '<span class="text-info fw-bold">■ Beban Jumlah Kontrak</span> per Service Manager &bull; Distribusi Penugasan Tim';
            }
        }

        chartOrang.update();
    }

    // --- CHART 4 (Bottom Right): Top Vendor Spend Analysis ---
    const ctxTopVendor = document.getElementById('chartTopVendorCompare').getContext('2d');
    
    function getVendorChartData(mode) {
        if (mode === 'realisasi') {
            const vLabels = chartData.chart3_vendor_realisasi.labels.slice(0, 6);
            const vData = chartData.chart3_vendor_realisasi.data.slice(0, 6);
            return {
                labels: vLabels,
                datasets: [{
                    label: 'Realisasi Belanja Aktual',
                    data: vData,
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    maxBarThickness: 14
                }]
            };
        } else {
            const vLabels = chartData.chart2_vendor_pagu.labels.slice(0, 6);
            const vData = chartData.chart2_vendor_pagu.data.slice(0, 6);
            return {
                labels: vLabels,
                datasets: [{
                    label: 'Total Nilai Pagu Vendor',
                    data: vData,
                    backgroundColor: '#38bdf8',
                    borderRadius: 4,
                    maxBarThickness: 14
                }]
            };
        }
    }

    const initV = getVendorChartData('realisasi');
    const chartTopVendorCompare = new Chart(ctxTopVendor, {
        type: 'bar',
        data: {
            labels: initV.labels,
            datasets: initV.datasets
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            layout: {
                padding: { left: 8, right: 14 }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 9,
                        color: '#94a3b8',
                        font: { size: 9, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 6
                    }
                },
                tooltip: {
                    ...darkTooltipOptions,
                    callbacks: {
                        title: function(items) {
                            const idx = items[0].dataIndex;
                            if (currentVendorMode === 'realisasi') {
                                return (chartData.chart3_vendor_realisasi.full_labels && chartData.chart3_vendor_realisasi.full_labels[idx]) || items[0].label;
                            } else {
                                return (chartData.chart2_vendor_pagu.full_labels && chartData.chart2_vendor_pagu.full_labels[idx]) || items[0].label;
                            }
                        },
                        label: function(context) {
                            const idx = context.dataIndex;
                            const val = formatRupiah(context.parsed.x);
                            if (currentVendorMode === 'realisasi') {
                                const share = (chartData.chart3_vendor_realisasi.percentages && chartData.chart3_vendor_realisasi.percentages[idx]) || 0;
                                const cnt = (chartData.chart3_vendor_realisasi.contracts && chartData.chart3_vendor_realisasi.contracts[idx]) || 1;
                                return `Realisasi: ${val} (${share}% Pangsa • ${cnt} Kontrak)`;
                            } else {
                                const cnt = (chartData.chart2_vendor_pagu.contracts && chartData.chart2_vendor_pagu.contracts[idx]) || 1;
                                return `Pagu Kontrak: ${val} (${cnt} Kontrak)`;
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: "'JetBrains Mono', monospace" },
                        callback: formatShortCurrency
                    }
                },
                y: {
                    afterFit: function(axis) {
                        axis.width += 24;
                    },
                    grid: { color: 'rgba(255, 255, 255, 0.02)', drawBorder: false },
                    ticks: {
                        color: '#cbd5e1',
                        font: { size: 9, weight: '600', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 6,
                        callback: function(val, idx) {
                            const lbl = this.getLabelForValue(idx) || '';
                            return lbl.length > 28 ? lbl.substr(0, 26) + '..' : lbl;
                        }
                    }
                }
            }
        }
    });

    function setVendorMode(mode) {
        currentVendorMode = mode;
        document.getElementById('btnToggleVendorReal').classList.toggle('active', mode === 'realisasi');
        document.getElementById('btnToggleVendorPagu').classList.toggle('active', mode === 'pagu');

        const vCfg = getVendorChartData(mode);
        chartTopVendorCompare.data.labels = vCfg.labels;
        chartTopVendorCompare.data.datasets = vCfg.datasets;
        chartTopVendorCompare.update();
    }

    // 4. DYNAMIC 10-SECOND AUTO-REFRESH & SMOOTH RADIAL COUNTDOWN
    const REFRESH_INTERVAL = 10;
    let secondsRemaining = REFRESH_INTERVAL;
    const countdownEl = document.getElementById('countdownDisplay');
    const circleBar = document.getElementById('syncProgressCircle');
    const FULL_CIRCUMFERENCE = 44;

    async function fetchDashboardUpdates() {
        countdownEl.textContent = 'Sync...';
        try {
            const res = await fetch('{{ route("standby.data") }}');
            if (!res.ok) throw new Error('Network response not ok');
            const json = await res.json();
            if (json.status === 'success') {
                updateUIElements(json.data);
                triggerPulseWave();
            }
        } catch (err) {
            console.error("Gagal sinkronisasi data:", err);
        } finally {
            secondsRemaining = REFRESH_INTERVAL;
            updateRingProgress();
        }
    }

    function triggerPulseWave() {
        const kpiRow = document.getElementById('kpiRowContainer');
        kpiRow.classList.add('sync-wave-active');
        setTimeout(() => kpiRow.classList.remove('sync-wave-active'), 900);
    }

    function updateRingProgress() {
        const fraction = secondsRemaining / REFRESH_INTERVAL;
        const offset = FULL_CIRCUMFERENCE * (1 - fraction);
        circleBar.style.strokeDashoffset = offset;
        countdownEl.textContent = `${secondsRemaining}s`;
    }

    function updateUIElements(data) {
        // 1. Update KPI Cards
        document.getElementById('valPagu').textContent = formatRupiah(data.summary.total_pagu);
        document.getElementById('valRealisasi').textContent = formatRupiah(data.summary.total_realisasi);
        document.getElementById('valSisa').textContent = formatRupiah(data.summary.sisa_anggaran);
        document.getElementById('valSerapanPct').textContent = `${data.summary.serapan_pct}%`;

        const sisaPct = data.summary.total_pagu > 0 
            ? ((data.summary.sisa_anggaran / data.summary.total_pagu) * 100).toFixed(1)
            : 0;
        document.getElementById('valSisaPct').textContent = `${sisaPct}% Sisa`;

        if (data.health_summary) {
            document.getElementById('valHealthSummary').innerHTML = `
                <span class="badge-status-soft status-safe">${data.health_summary.safe || 0} Sehat</span>
                <span class="badge-status-soft status-warning">${data.health_summary.warning || 0} Waspada</span>
                <span class="badge-status-soft status-critical">${data.health_summary.critical || 0} Kritis</span>
            `;
        }

        // Cache chart data
        chartData = data.charts;

        // 2. Update Chart 1: Monthly Trend
        if (data.charts.chart4_komparasi.monthly) {
            chartMonthlyTrend.data.labels = data.charts.chart4_komparasi.monthly.labels;
            chartMonthlyTrend.data.datasets[0].data = data.charts.chart4_komparasi.monthly.realisasi;
            chartMonthlyTrend.data.datasets[1].data = data.charts.chart4_komparasi.monthly.prognosa;
            chartMonthlyTrend.update('none');
        }

        // 3. Update Chart 2: Donut Serapan
        chartBudgetDonut.data.datasets[0].data = [data.summary.total_realisasi, data.summary.sisa_anggaran];
        chartBudgetDonut.update('none');
        document.getElementById('centerPctText').textContent = `${data.summary.serapan_pct}%`;
        document.getElementById('badgeSerapanOverview').textContent = `Serapan: ${data.summary.serapan_pct}%`;

        // 4. Update Chart 3: Early Warning / Proyek Kritis
        const c3Updated = getChart3Data(currentChart3Mode);
        chartOrang.data.labels = c3Updated.labels;
        chartOrang.data.datasets = c3Updated.datasets;
        chartOrang.update('none');
        const badgeCrit = document.getElementById('badgeCriticalCount');
        if (badgeCrit && data.charts.chart4_komparasi.critical_projects) {
            badgeCrit.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> ${data.charts.chart4_komparasi.critical_projects.over_count || 6} Over-Budget (>100%)`;
        }

        // 5. Update Chart 4: Top Vendor Compare
        const vUpdated = getVendorChartData(currentVendorMode);
        chartTopVendorCompare.data.labels = vUpdated.labels;
        chartTopVendorCompare.data.datasets = vUpdated.datasets;
        chartTopVendorCompare.update('none');
        const badgeVen = document.getElementById('badgeVendorShare');
        if (badgeVen && data.charts.chart3_vendor_realisasi) {
            badgeVen.textContent = `Top 2 Vendor = ${data.charts.chart3_vendor_realisasi.top2_share || 54.2}% Belanja`;
        }
    }

    // 10-Second Countdown Loop
    setInterval(() => {
        secondsRemaining--;
        if (secondsRemaining <= 0) {
            fetchDashboardUpdates();
        } else {
            updateRingProgress();
        }
    }, 1000);

    // Manual Refresh Trigger
    document.getElementById('btnSyncTrigger').addEventListener('click', () => {
        fetchDashboardUpdates();
    });
</script>

</body>
</html>
