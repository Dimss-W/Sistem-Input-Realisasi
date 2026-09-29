<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PGNCOM Operations Portal | PT PGAS Telekomunikasi Nusantara</title>
    
    <!-- Favicon PGNCOM Logo -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=6">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=6">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=6">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans, Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --pgn-blue: #0284c7;
            --pgn-blue-dark: #0369a1;
            --pgn-navy: #090d16;
            --pgn-red: #e11d48;
            --pgn-green: #10b981;
            --pgn-cyan: #06b6d4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            width: 100%;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #050811;
            color: #0f172a;
            position: relative;
            overflow-x: hidden;
        }

        @media (min-width: 992px) {
            html, body {
                height: 100vh;
                overflow-y: hidden;
            }
        }

        /* ----------------------------------------------------
           4K ULTRA-HD NOC COMMAND CENTER BACKGROUND
        ---------------------------------------------------- */
        .bg-4k-viewport {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url("{{ asset('assets/images/bg-login-4k.jpg') }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            filter: brightness(0.92) contrast(1.08) saturate(1.15);
            transform: scale(1.02);
            transition: transform 0.25s cubic-bezier(0.2, 0, 0.2, 1);
            will-change: transform;
        }

        /* Balanced cinematic vignette: comfortably dark, rich contrast, screens pop vividly */
        .bg-vignette {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: radial-gradient(circle at 50% 50%, rgba(4, 8, 20, 0.22) 0%, rgba(3, 7, 18, 0.48) 70%, rgba(2, 5, 14, 0.72) 100%);
            pointer-events: none;
        }

        /* Subtle high-tech mesh grid overlay */
        .bg-grid-mesh {
            position: fixed;
            inset: 0;
            z-index: 2;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
            opacity: 0.28;
        }

        /* Ambient Glowing Aura Orbs - translucent */
        .glow-aura {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
            filter: blur(80px);
            opacity: 0.18;
            animation: auraFloat 10s ease-in-out infinite alternate;
        }
        .glow-aura-1 {
            width: 440px; height: 440px;
            background: radial-gradient(circle, #0284c7, #0369a1 70%, transparent 100%);
            top: -10%; left: -5%;
        }
        .glow-aura-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, #06b6d4, #10b981 70%, transparent 100%);
            bottom: -10%; right: -5%;
            animation-delay: -5s;
        }
        .glow-aura-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #10b981, transparent 70%);
            bottom: 20%; left: 30%;
            animation-delay: -7s;
            opacity: 0.12;
        }

        @keyframes auraFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -25px) scale(1.1); }
            100% { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* Animated Horizontal Telemetry Scan Laser */
        .telemetry-laser {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(6, 182, 212, 0.3), rgba(2, 132, 199, 0.8), rgba(16, 185, 129, 0.7), transparent);
            z-index: 2;
            pointer-events: none;
            opacity: 0.55;
            animation: laserSweep 9s linear infinite;
        }

        @keyframes laserSweep {
            0% { top: -2%; opacity: 0; }
            5% { opacity: 0.7; }
            95% { opacity: 0.7; }
            100% { top: 102%; opacity: 0; }
        }

        /* ----------------------------------------------------
           MAIN LOGIN APP SHELL & LAYOUT (ZOOMED OUT COMPACT)
        ---------------------------------------------------- */
        .login-main-container {
            position: relative;
            z-index: 10;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 14px;
        }

        /* Balanced Dual-Zone Glassmorphism Shell - Zoomed Out */
        .login-glass-shell {
            width: 100%;
            max-width: 880px;
            background: rgba(10, 18, 38, 0.68);
            border-radius: 24px;
            border: 1.5px solid rgba(255, 255, 255, 0.22);
            box-shadow: 
                0 30px 80px -15px rgba(0, 0, 0, 0.8),
                0 0 50px rgba(2, 132, 199, 0.22),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            transform: scale(0.92);
            transform-origin: center center;
        }

        @media (min-width: 992px) {
            .login-glass-shell {
                flex-direction: row;
                min-height: 520px;
            }
        }

        /* ----------------------------------------------------
           LEFT ZONE: ENTERPRISE SHOWCASE & TELEMETRY
        ---------------------------------------------------- */
        .showcase-zone {
            flex: 1.05;
            padding: 30px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.18) 0%, rgba(8, 16, 36, 0.52) 60%, rgba(16, 185, 129, 0.1) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        @media (min-width: 992px) {
            .showcase-zone {
                border-bottom: none;
                border-right: 1px solid rgba(255, 255, 255, 0.14);
                padding: 32px 30px;
            }
        }

        /* Subtle Corner Cyber Accents */
        .cyber-corner-tl {
            position: absolute;
            top: 18px; left: 18px;
            width: 14px; height: 14px;
            border-top: 2px solid rgba(2, 132, 199, 0.6);
            border-left: 2px solid rgba(2, 132, 199, 0.6);
            pointer-events: none;
        }
        .cyber-corner-br {
            position: absolute;
            bottom: 18px; right: 18px;
            width: 14px; height: 14px;
            border-bottom: 2px solid rgba(16, 185, 129, 0.6);
            border-right: 2px solid rgba(16, 185, 129, 0.6);
            pointer-events: none;
        }

        /* PGN Logo Presentation Box */
        .pgn-brand-pill {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            padding: 7px 16px;
            display: inline-flex;
            align-items: center;
            box-shadow: 
                0 8px 20px -4px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            margin-bottom: 14px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pgn-brand-pill:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 12px 24px -4px rgba(2, 132, 199, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.9);
        }
        .pgn-brand-logo {
            height: 30px;
            max-width: 165px;
            object-fit: contain;
            display: block;
        }

        /* Status Pill with live pulse dot */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(2, 132, 199, 0.16);
            border: 1px solid rgba(2, 132, 199, 0.4);
            color: #38bdf8;
            font-size: 0.66rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }

        .pulse-core {
            width: 7px; height: 7px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #10b981;
            animation: pulseStatus 1.6s infinite ease-in-out;
        }

        @keyframes pulseStatus {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 14px #10b981; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        .showcase-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.42rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .showcase-title span {
            background: linear-gradient(135deg, #38bdf8 0%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-desc {
            color: #94a3b8;
            font-size: 0.78rem;
            line-height: 1.48;
            margin-bottom: 18px;
            max-width: 420px;
        }

        /* 3 Interactive Feature Value Cards - Zoomed Out */
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 7px;
            margin-bottom: 18px;
        }

        .feature-card-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 11px;
            padding: 7px 11px;
            display: flex;
            align-items: center;
            gap: 11px;
            transition: all 0.25s ease;
        }
        .feature-card-item:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(56, 189, 248, 0.35);
            transform: translateX(4px);
        }

        .feature-card-icon {
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .icon-blue {
            background: rgba(2, 132, 199, 0.25);
            color: #38bdf8;
            border: 1px solid rgba(2, 132, 199, 0.4);
        }
        .icon-green {
            background: rgba(16, 185, 129, 0.22);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .icon-purple {
            background: rgba(139, 92, 246, 0.25);
            color: #c084fc;
            border: 1px solid rgba(139, 92, 246, 0.4);
        }

        .feature-card-title {
            font-size: 0.77rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 1px;
        }
        .feature-card-sub {
            font-size: 0.68rem;
            color: #94a3b8;
            line-height: 1.3;
        }

        /* Showcase Bottom Live Telemetry Footer */
        .showcase-telemetry-footer {
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            color: #64748b;
            font-size: 0.7rem;
        }

        .live-clock-badge {
            font-family: 'Outfit', monospace;
            font-weight: 700;
            color: #38bdf8;
            background: rgba(2, 132, 199, 0.14);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(2, 132, 199, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
        }

        /* ----------------------------------------------------
           RIGHT ZONE: MODERN HIGH-CONTRAST FORM - ZOOMED OUT
        ---------------------------------------------------- */
        .form-zone {
            flex: 0.95;
            background: rgba(255, 255, 255, 0.94);
            padding: 30px 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        @media (min-width: 992px) {
            .form-zone {
                padding: 32px 30px;
            }
        }

        /* Top Accent Rainbow Line */
        .form-zone::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0284c7 0%, #10b981 50%, #e11d48 100%);
        }

        .greeting-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
            letter-spacing: -0.02em;
        }

        .greeting-sub {
            color: #64748b;
            font-size: 0.78rem;
            margin-bottom: 18px;
            font-weight: 500;
        }

        .form-label-modern {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #334155;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-box-modern {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 13px;
        }

        .input-box-modern .icon-prefix {
            position: absolute;
            left: 13px;
            color: #94a3b8;
            font-size: 1.05rem;
            z-index: 5;
            pointer-events: none;
            transition: color 0.25s ease, transform 0.25s ease;
        }

        .input-box-modern .form-control {
            height: 42px;
            padding-left: 38px;
            padding-right: 38px;
            border-radius: 11px;
            border: 1.5px solid #cbd5e1;
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .input-box-modern .form-control:focus {
            border-color: #0284c7;
            background-color: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(2, 132, 199, 0.16), 0 4px 10px rgba(2, 132, 199, 0.08);
            outline: none;
        }

        .input-box-modern:focus-within .icon-prefix {
            color: #0284c7;
            transform: scale(1.1);
        }

        .btn-toggle-visibility {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.05rem;
            z-index: 6;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .btn-toggle-visibility:hover {
            color: #0284c7;
            background-color: #f1f5f9;
        }

        /* CapsLock Detection Alert */
        .capslock-alert {
            display: none;
            align-items: center;
            gap: 6px;
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-top: -8px;
            margin-bottom: 10px;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Submit Button with Gradient & Shimmer - Zoomed Out */
        .btn-modern-login {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #1e3a8a 100%);
            border: none;
            border-radius: 11px;
            height: 43px;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.88rem;
            color: #ffffff;
            box-shadow: 0 6px 18px -3px rgba(2, 132, 199, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }

        /* Continuous subtle light wave shimmer */
        .btn-modern-login::after {
            content: "";
            position: absolute;
            top: -50%; bottom: -50%;
            left: -60%; width: 40%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: rotate(25deg);
            pointer-events: none;
            animation: shimmerSweep 4s infinite;
        }

        @keyframes shimmerSweep {
            0% { left: -60%; }
            35% { left: 140%; }
            100% { left: 140%; }
        }

        .btn-modern-login:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 50%, #0f172a 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -4px rgba(2, 132, 199, 0.6);
            color: #ffffff;
        }

        .btn-modern-login:hover .btn-arrow-icon {
            transform: translateX(4px);
        }

        .btn-modern-login:active {
            transform: translateY(0);
        }

        .btn-arrow-icon {
            font-size: 1.25rem;
            transition: transform 0.25s ease;
        }

        /* Form Auxiliary Links */
        .form-check-input {
            width: 17px; height: 17px;
            border-radius: 5px;
            border-color: #cbd5e1;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #0284c7;
            border-color: #0284c7;
        }

        .link-access-help {
            font-size: 0.78rem;
            color: #0284c7;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .link-access-help:hover {
            color: #0369a1;
            text-decoration: underline;
        }

        /* Security Certificate Seal Footer */
        .form-security-seal {
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #64748b;
            font-size: 0.72rem;
        }

        .ssl-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-weight: 700;
        }

        /* Global Alert Modals */
        .alert-modern {
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 12px 14px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<!-- 1. 4K NOC COMMAND CENTER BACKGROUND LAYER -->
<div class="bg-4k-viewport" id="bgViewport"></div>
<div class="bg-vignette"></div>
<div class="bg-grid-mesh"></div>

<!-- 2. AMBIENT GLOW AURAS -->
<div class="glow-aura glow-aura-1"></div>
<div class="glow-aura glow-aura-2"></div>
<div class="glow-aura glow-aura-3"></div>

<!-- 3. ANIMATED TELEMETRY SCAN LASER -->
<div class="telemetry-laser"></div>

<!-- 4. INTERACTIVE PARTICLES CANVAS -->
<canvas id="particleCanvas" style="position:fixed; inset:0; z-index:3; pointer-events:none;"></canvas>

<!-- 5. MAIN LOGIN VIEWPORT WRAPPER -->
<div class="login-main-container">
    <div class="login-glass-shell" id="loginShell">
        
        <!-- ==============================================
             LEFT ZONE: ENTERPRISE SHOWCASE & TELEMETRY
             ============================================== -->
        <div class="showcase-zone">
            <div class="cyber-corner-tl"></div>
            <div class="cyber-corner-br"></div>

            <!-- Top Header & Brand -->
            <div>
                <div class="pgn-brand-pill" style="background: rgba(255, 255, 255, 0.95); padding: 8px 16px; border-radius: 12px; display: inline-block;">
                    <img src="{{ asset('assets/images/logo-pgncom.png') }}" alt="PGNCOM Logo" class="pgn-brand-logo" style="height: 38px; width: auto; object-fit: contain;">
                </div>

                <div class="d-block">
                    <div class="status-pill">
                        <span class="pulse-core"></span>
                        <span>LIVE NOC OPERATION • 99.98% UPTIME</span>
                    </div>

                    <h1 class="showcase-title">
                        Monitoring Realisasi Biaya & <span>Quality Control</span>
                    </h1>

                    <p class="showcase-desc">
                        Sistem manajemen enterprise terintegrasi untuk pemantauan realisasi anggaran proyek, pengesahan dokumen BASTO, verifikasi mutu QC teknis, dan administrasi invoicing PT PGAS Telekomunikasi Nusantara (PGNCOM).
                    </p>
                </div>

                <!-- 3 Interactive Feature Value Highlights -->
                <div class="feature-grid">
                    <div class="feature-card-item">
                        <div class="feature-card-icon icon-blue">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="feature-card-title">Standarisasi Mutu QC (4 Kriteria)</div>
                            <div class="feature-card-sub">Pemeriksaan live berkas fisik & checklist digital otomatis sebelum approval.</div>
                        </div>
                    </div>

                    <div class="feature-card-item">
                        <div class="feature-card-icon icon-green">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <div class="feature-card-title">Multi-Currency IDR & USD Real-Time</div>
                            <div class="feature-card-sub">Sinkronisasi otomatis kurs transaksi Bank Indonesia & portofolio terpusat.</div>
                        </div>
                    </div>

                    <div class="feature-card-item">
                        <div class="feature-card-icon icon-purple">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <div>
                            <div class="feature-card-title">Role-Based Governance & Audit Log</div>
                            <div class="feature-card-sub">Otorisasi bertingkat untuk SM, DMO, QC, Procurement, dan Administrator.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Live Telemetry Footer -->
            <div class="showcase-telemetry-footer">
                <div class="d-flex align-items-center gap-2">
                    <span class="live-clock-badge">
                        <i class="bi bi-clock-fill"></i>
                        <span id="liveClockDisplay">--:--:-- WIB</span>
                    </span>
                    <span class="d-none d-sm-inline text-white-50">• Jakarta (UTC+7)</span>
                </div>
                <div class="d-flex align-items-center gap-1.5 text-white-50">
                    <i class="bi bi-hdd-network-fill text-success"></i>
                    <span>Server: Protected & Active</span>
                </div>
            </div>
        </div>

        <!-- ==============================================
             RIGHT ZONE: MODERN HIGH-CONTRAST FORM
             ============================================== -->
        <div class="form-zone">
            <!-- Dynamic Time Greeting & Header -->
            <div class="mb-3">
                <h2 class="greeting-title" id="greetingTitle">Selamat Datang 👋</h2>
                <p class="greeting-sub">Silakan masuk menggunakan akun resmi PGNCOM Anda</p>
            </div>

            <!-- Flash Success Alert -->
            @if(session('success'))
                <div class="alert alert-success alert-modern alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Flash Error Alert -->
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-modern alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1">{{ $errors->first() }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Authentication Form -->
            <form action="{{ route('login') }}" method="POST" id="authLoginForm">
                @csrf

                <!-- 1. Username / Email Input -->
                <div class="mb-1">
                    <label for="login" class="form-label-modern">
                        <span>Username / Email PGN</span>
                        <span class="text-muted fw-normal" style="font-size: 0.68rem;">SSO / Akun Terdaftar</span>
                    </label>
                    <div class="input-box-modern">
                        <i class="bi bi-person-badge-fill icon-prefix"></i>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="login" 
                            name="login" 
                            value="{{ old('login') }}" 
                            placeholder="nama.user@pgncom.co.id" 
                            required 
                            autofocus 
                            autocomplete="username"
                        >
                    </div>
                </div>

                <!-- 2. Password Input -->
                <div class="mb-2">
                    <label for="password" class="form-label-modern">
                        <span>Password Kredensial</span>
                    </label>
                    <div class="input-box-modern mb-1">
                        <i class="bi bi-shield-lock-fill icon-prefix"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••••••" 
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn-toggle-visibility" id="togglePasswordBtn" title="Tampilkan / Sembunyikan Password" tabindex="-1">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    
                    <!-- Caps Lock Warning Tooltip -->
                    <div class="capslock-alert" id="capsLockWarning">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Peringatan: <b>Caps Lock sedang aktif</b></span>
                    </div>
                </div>

                <!-- 3. Remember Me & Quick Help -->
                <div class="d-flex align-items-center justify-content-between mb-4 pt-1">
                    <div class="form-check d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary fw-semibold" for="remember" style="font-size: 0.8rem; cursor: pointer; user-select: none;">
                            Ingat Sesi Saya
                        </label>
                    </div>
                    <button type="button" class="link-access-help" data-bs-toggle="modal" data-bs-target="#accessHelpModal">
                        <i class="bi bi-question-circle-fill"></i> Bantuan Akses?
                    </button>
                </div>

                <!-- 4. Submit Button -->
                <button type="submit" class="btn btn-modern-login w-100" id="btnSubmitLogin">
                    <span id="btnText"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Portal Operasional</span>
                    <i class="bi bi-arrow-right btn-arrow-icon" id="btnArrow"></i>
                    <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status"></span>
                </button>
            </form>

            <!-- Bottom Security & Department Seal -->
            <div class="form-security-seal">
                <div class="ssl-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>256-Bit SSL Encrypted</span>
                </div>
                <div>Departemen IT & OCS Operations</div>
            </div>
        </div>

    </div>
</div>

<!-- ==============================================
     MODAL: BANTUAN AKSES & IT SUPPORT
     ============================================== -->
<div class="modal fade" id="accessHelpModal" tabindex="-1" aria-labelledby="accessHelpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="accessHelpLabel">Bantuan Akses &amp; Dukungan Portal</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Aplikasi ini merupakan portal internal resmi PT PGAS Telekomunikasi Nusantara (PGNCOM) untuk manajemen realisasi biaya proyek dan jaminan mutu (QC). Jika Anda mengalami kendala saat masuk:
                </p>

                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item px-0 py-2.5 d-flex gap-3 border-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="small">
                            <strong class="d-block text-dark">Email Layanan Helpdesk</strong>
                            <a href="mailto:it.support@pgncom.co.id" class="text-decoration-none text-primary fw-medium">it.support@pgncom.co.id</a>
                        </div>
                    </div>

                    <div class="list-group-item px-0 py-2.5 d-flex gap-3 border-0">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="small">
                            <strong class="d-block text-dark">Internal Extension</strong>
                            <span class="text-muted">Ext. 5500 / 5501 (Operasional IT &amp; NOC PGN)</span>
                        </div>
                    </div>

                    <div class="list-group-item px-0 py-2.5 d-flex gap-3 border-0">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div class="small">
                            <strong class="d-block text-dark">Reset Password</strong>
                            <span class="text-muted">Hubungi Superadmin portal untuk aktivasi ulang atau pembaharuan kata sandi akun Anda.</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 small border text-secondary">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                    Pastikan Anda memasukkan <b>username</b> atau <b>alamat email resmi</b> yang telah didaftarkan dalam modul Master Data Administrator.
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2.5 px-4">
                <button type="button" class="btn btn-secondary rounded-3 btn-sm fw-bold px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. DYNAMIC TIME-BASED GREETING
    function setDynamicGreeting() {
        const hour = new Date().getHours();
        const greetingEl = document.getElementById('greetingTitle');
        if (!greetingEl) return;

        if (hour >= 4 && hour < 11) {
            greetingEl.textContent = 'Selamat Pagi 🌅';
        } else if (hour >= 11 && hour < 15) {
            greetingEl.textContent = 'Selamat Siang ☀️';
        } else if (hour >= 15 && hour < 18) {
            greetingEl.textContent = 'Selamat Sore 🌇';
        } else {
            greetingEl.textContent = 'Selamat Malam 🌙';
        }
    }
    setDynamicGreeting();

    // 2. REAL-TIME DIGITAL CLOCK WITH DATE
    function updateLiveClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClockDisplay');
        if (clockEl) {
            clockEl.textContent = `${h}:${m}:${s} WIB`;
        }
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    // 3. TOGGLE PASSWORD VISIBILITY
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            const icon = this.querySelector('i');
            if (icon) {
                if (isPassword) {
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                } else {
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                }
            }
        });
    }

    // 4. CAPS LOCK DETECTION
    const capsWarning = document.getElementById('capsLockWarning');
    if (passwordInput && capsWarning) {
        passwordInput.addEventListener('keyup', function (e) {
            if (e.getModifierState && e.getModifierState('CapsLock')) {
                capsWarning.style.display = 'flex';
            } else {
                capsWarning.style.display = 'none';
            }
        });
        passwordInput.addEventListener('keydown', function (e) {
            if (e.getModifierState && e.getModifierState('CapsLock')) {
                capsWarning.style.display = 'flex';
            } else {
                capsWarning.style.display = 'none';
            }
        });
    }

    // 5. INTERACTIVE 3D PARALLAX EFFECT
    document.addEventListener('mousemove', function (e) {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        const dx = (e.clientX - cx) / cx;
        const dy = (e.clientY - cy) / cy;

        const bg = document.getElementById('bgViewport');
        if (bg) {
            bg.style.transform = `scale(1.04) translate3d(${dx * -16}px, ${dy * -16}px, 0)`;
        }

        const aura1 = document.querySelector('.glow-aura-1');
        const aura2 = document.querySelector('.glow-aura-2');
        if (aura1) aura1.style.transform = `translate3d(${dx * 28}px, ${dy * 28}px, 0)`;
        if (aura2) aura2.style.transform = `translate3d(${dx * -28}px, ${dy * -28}px, 0)`;
    });

    // 6. HIGH-PERFORMANCE INTERACTIVE CANVAS PARTICLES
    const canvas = document.getElementById('particleCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const particles = [];
        const count = 42;

        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.45,
                radius: Math.random() * 2 + 1,
                color: Math.random() > 0.45 ? 'rgba(56, 189, 248, ' : 'rgba(52, 211, 153, '
            });
        }

        let mouseX = -1000, mouseY = -1000;
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function renderParticles() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = p.color + '0.6)';
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 130) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = p.color + (0.24 * (1 - dist / 130)) + ')';
                        ctx.lineWidth = 0.65;
                        ctx.stroke();
                    }
                }

                const mDist = Math.hypot(p.x - mouseX, p.y - mouseY);
                if (mDist < 160) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouseX, mouseY);
                    ctx.strokeStyle = p.color + (0.45 * (1 - mDist / 160)) + ')';
                    ctx.lineWidth = 0.85;
                    ctx.stroke();
                }
            }
            requestAnimationFrame(renderParticles);
        }
        renderParticles();
    }

    // 7. FORM SUBMISSION SPINNER & FEEDBACK
    const form = document.getElementById('authLoginForm');
    const submitBtn = document.getElementById('btnSubmitLogin');
    const btnText = document.getElementById('btnText');
    const btnArrow = document.getElementById('btnArrow');
    const btnSpinner = document.getElementById('btnSpinner');

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            btnText.textContent = 'Mengautentikasi...';
            btnArrow.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
        });
    }
</script>

</body>
</html>
