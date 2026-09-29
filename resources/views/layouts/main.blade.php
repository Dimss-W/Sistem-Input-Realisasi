<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Input Realisasi') — Sistem Monitoring Realisasi Biaya</title>
    <meta name="description" content="Sistem web input dan monitoring data realisasi biaya perusahaan">

    <!-- Favicon PGNCOM Logo -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=5">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=5">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon-pgncom.png') }}?v=5">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary:        #0F172A;
            --primary-light:  #1E293B;
            --primary-dark:   #020617;
            --accent:         #3B82F6;
            --accent-hover:   #2563EB;
            --accent-glow:    rgba(59, 130, 246, 0.15);
            --success:        #10B981;
            --warning:        #F59E0B;
            --danger:         #EF4444;
            --sidebar-width:  270px;
            --topbar-height:  70px;
            --body-bg:        #F8FAFC;
            --card-bg:        #FFFFFF;
            --border-color:   #E2E8F0;
            --text-primary:   #0F172A;
            --text-secondary: #475569;
            --text-muted:     #94A3B8;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            font-size: 0.875rem;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-weight: 700;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: radial-gradient(circle at 10% 20%, #1E293B 0%, #0F172A 100%);
            z-index: 1050;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
        }

        .sidebar-brand {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
            background: rgba(15, 23, 42, 0.6);
        }

        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon-wrapper {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
        }

        .sidebar-brand .brand-logo:hover .brand-icon-wrapper {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(56, 189, 248, 0.4);
            transform: scale(1.04);
        }

        .sidebar-brand .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.25);
        }

        .sidebar-brand .brand-name {
            color: white;
            font-weight: 800;
            font-size: 1rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .sidebar-brand .brand-subtitle {
            color: #94A3B8;
            font-size: 0.72rem;
            font-weight: 500;
            opacity: 0.8;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }

        .nav-section-title {
            padding: 12px 12px 6px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748B;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            color: #94A3B8;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.04);
            color: white;
        }

        .sidebar-nav .nav-link:hover i {
            transform: translateX(2px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(59, 130, 246, 0.1);
            color: #60A5FA;
            font-weight: 600;
        }

        .sidebar-nav .nav-link.active i {
            color: #60A5FA;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-footer .app-version {
            font-size: 0.72rem;
            color: #475569;
            text-align: center;
            font-weight: 500;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1040;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
        }

        .topbar .page-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .topbar .breadcrumb {
            font-size: 0.75rem;
            margin: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-badge {
            background: #F1F5F9;
            color: var(--text-secondary);
            border-radius: 12px;
            padding: 6px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--border-color);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ===== CARDS ===== */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.015), 0 1px 2px rgba(15, 23, 42, 0.008);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -10px rgba(15, 23, 42, 0.08);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0 !important;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border-radius: 16px;
            padding: 24px 20px;
            border: 1px solid var(--border-color);
            background: white;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 6px;
            letter-spacing: -0.03em;
            color: var(--text-primary);
        }

        .stat-card .stat-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ===== TABLES ===== */
        .table-custom thead th {
            background: #F8FAFC;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
            padding: 12px 16px;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.85rem;
            color: var(--text-primary);
        }

        .table-custom tbody tr {
            transition: background 0.15s ease;
        }

        .table-custom tbody tr:hover {
            background: #F8FAFC;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BADGES ===== */
        .badge-status-paid        { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-status-unpaid      { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
        .badge-status-pending     { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .badge-status-cancel      { background: #F9FAFB; color: #374151; border: 1px solid #E5E7EB; }
        .badge-status-proses      { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .badge-status-popay       { background: #FDF4FF; color: #701A75; border: 1px solid #F5D0FE; }
        .badge-status-in-progres  { background: #FFF7ED; color: #9A3412; border: 1px solid #FED7AA; }
        .badge-status-wait-inv    { background: #ECFEFF; color: #083344; border: 1px solid #CFFAFE; }
        .badge-status-default     { background: #F5F3FF; color: #4C1D95; border: 1px solid #DDD6FE; }

        /* ===== FORMS ===== */
        .form-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .form-control, .form-select {
            font-size: 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.2s ease;
            color: var(--text-primary);
            background-color: #FFFFFF;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
            background-color: #FFFFFF;
        }

        .form-section-title {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-secondary);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 20px;
        }

        /* ===== BUTTONS ===== */
        .btn {
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .btn-accent:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        }

        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-secondary);
        }

        .btn-outline-secondary:hover {
            background: #F1F5F9;
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
            border-radius: 8px;
        }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: 12px;
            border: 1px solid transparent;
            font-size: 0.85rem;
            padding: 14px 20px;
        }

        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border-color: #A7F3D0;
        }

        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border-color: #FCA5A5;
        }

        /* ===== PAGINATION ===== */
        .pagination .page-link {
            font-size: 0.8rem;
            padding: 8px 14px;
            border-radius: 8px !important;
            margin: 0 3px;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 600;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* ===== RUPIAH FORMAT ===== */
        .currency-display {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
            color: var(--primary);
        }

        /* ===== IMPORT AREA ===== */
        .upload-zone {
            border: 2px dashed var(--border-color);
            border-radius: 16px;
            padding: 56px 28px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #F8FAFC;
        }

        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--accent);
            background: rgba(59, 130, 246, 0.02);
        }

        .upload-zone .upload-icon {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 16px;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #F1F5F9; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
        }

        /* ===== UTILITY ===== */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hover-underline:hover { text-decoration: underline; }

        .number-display {
            font-variant-numeric: tabular-nums;
        }

        /* ===== UNIVERSAL ENTRANCE ANIMATION SYSTEM ===== */
        .page-entrance {
            animation: globalPageEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes globalPageEntrance {
            0% {
                opacity: 0;
                transform: translateY(26px) scale(0.985);
                filter: blur(3px);
            }
            100% {
                opacity: 1;
                transform: none;
                filter: none;
            }
        }

        /* Modal Z-Index and Stacking Context Fix */
        .modal {
            z-index: 1065 !important;
        }
        .modal-backdrop {
            z-index: 1055 !important;
        }

        .card-entrance {
            animation: globalCardEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) calc(0.08s * var(--card-delay, 1)) both;
        }

        @keyframes globalCardEntrance {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stagger-row {
            animation: globalRowSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) calc(0.1s + var(--row-delay, 0s)) both;
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        @keyframes globalRowSlideIn {
            0% {
                opacity: 0;
                transform: translateX(-16px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .stagger-row:hover {
            background-color: #F8FAFC !important;
            transform: scale(1.002) translateX(2px);
        }

        .btn-action-animated {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-action-animated:hover {
            transform: scale(1.05) translateY(-1px);
        }
        .btn-action-animated:active {
            transform: scale(0.98);
        }

        /* ===== STICKY TABLE HEADER ===== */
        .table-sticky-header thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 5 !important;
            background: #F8FAFC !important;
            box-shadow: inset 0 -1px 0 #E2E8F0, 0 2px 4px rgba(0,0,0,0.02) !important;
        }

        /* ===== NOTIFICATION BELL ===== */
        .notif-bell-btn {
            position: relative;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
            transition: all 0.2s ease;
        }
        .notif-bell-btn:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
        .notif-pulse {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 9px;
            height: 9px;
            background: #EF4444;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            animation: notifPulse 1.8s infinite;
        }
        @keyframes notifPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* ===== USER AVATAR & DROPDOWN ===== */
        .user-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1E3A8A 0%, #0284C7 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);
            border: 2px solid #FFFFFF;
            flex-shrink: 0;
        }

        /* ===== QUICK SEARCH TRIGGER ===== */
        .search-trigger-btn {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: #64748B;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .search-trigger-btn:hover {
            background: #FFFFFF;
            border-color: #CBD5E1;
            color: #0F172A;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 6mm 5mm 6mm;
            }
            *, *::before, *::after {
                box-sizing: border-box !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            html, body {
                background: #FFFFFF !important;
                color: #0F172A !important;
                font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif !important;
                font-size: 8pt !important;
                line-height: 1.3 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .sidebar, .topbar, .filter-card, .btn-print-hide, #sidebarToggle,
            .screen-only-dashboard, .modal, .tooltip, .popover, .sidebar-footer,
            .d-print-none, .no-print {
                display: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
            .page-entrance {
                animation: none !important;
                transform: none !important;
                filter: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .print-report-container {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 0 4mm 0 2mm !important;
                box-sizing: border-box !important;
            }
            .print-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                margin-bottom: 9px !important;
            }
            .print-table th {
                background: #005A9C !important;
                color: #FFFFFF !important;
                border: 1px solid #00467A !important;
                padding: 4px 6px !important;
                font-size: 7pt !important;
                font-weight: 700 !important;
                text-align: left !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-table td {
                border: 1px solid #CBD5E1 !important;
                padding: 3.5px 5px !important;
                font-size: 7pt !important;
                vertical-align: middle !important;
            }
            .print-table tr:nth-child(even) td {
                background-color: #F8FAFC !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-table-sky th {
                background: #0284C7 !important;
                border: 1px solid #0369A1 !important;
                color: #FFFFFF !important;
            }
            .print-table-emerald th {
                background: #059669 !important;
                border: 1px solid #047857 !important;
                color: #FFFFFF !important;
            }
            .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* ===== SIDEBAR ACCORDION GROUPS ===== */
        .sidebar-accordion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748B;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
            user-select: none;
            margin-top: 6px;
            margin-bottom: 2px;
        }
        .sidebar-accordion-header:hover {
            color: #94A3B8;
            background: rgba(255, 255, 255, 0.04);
        }
        .sidebar-accordion-header .chevron-icon {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.65rem;
            color: #64748B;
        }
        .sidebar-accordion-header:not(.collapsed) .chevron-icon {
            transform: rotate(180deg);
            color: #60A5FA;
        }
        .sidebar-accordion-header:not(.collapsed) {
            color: #93C5FD;
        }
        .sidebar-accordion-body {
            padding-left: 2px;
            margin-bottom: 4px;
        }

        /* ===== TABLE HEADER & INFO TOOLTIP ALIGNMENT ===== */
        .table th, .data-table th {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }
        .th-content {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            vertical-align: middle;
        }
        .th-content i.bi-info-circle {
            font-size: 0.72rem;
            line-height: 1;
            color: #94A3B8;
            opacity: 0.7;
            transition: color 0.15s ease, opacity 0.15s ease;
            flex-shrink: 0;
            display: inline-block;
        }
        .th-content:hover i.bi-info-circle,
        th:hover .th-content i.bi-info-circle {
            color: #2563EB;
            opacity: 1;
        }

        /* ===== TABLE COMPACT DENSITY ===== */
        .table-compact th {
            padding: 5px 8px !important;
            font-size: 0.68rem !important;
        }
        .table-compact td {
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
        }
        .table-compact .action-btn {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }
        .table-compact .badge {
            font-size: 0.65rem !important;
            padding: 0.2em 0.5em !important;
        }

        /* ===== QUICK FILTER PILLS ===== */
        .quick-filter-pills {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 12px;
        }
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            font-size: 0.74rem;
            font-weight: 600;
            border-radius: 20px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .filter-pill:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #CBD5E1;
            transform: translateY(-1px);
        }
        .filter-pill.active {
            background: #3B82F6 !important;
            color: #FFFFFF !important;
            border-color: #3B82F6 !important;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3) !important;
        }

        /* ===== AGING SLA BADGE ===== */
        .sla-badge-fresh {
            background-color: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }
        .sla-badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #FCD34D;
            animation: pulse-soft 2s infinite;
        }
        .sla-badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
            animation: pulse-alert 1.5s infinite;
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.75; }
        }
        @keyframes pulse-alert {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        /* ===== PAGU EARLY WARNING ===== */
        .cap-warning-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 6px;
        }
        .cap-warning-yellow {
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FCD34D;
        }
        .cap-warning-red {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
            animation: pulse-alert 1.5s infinite;
        }
    </style>

    @stack('styles')
</head>
<body>    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-brand">
            <a href="{{ auth()->check() && auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->check() && auth()->user()->hasRole('procurement') ? route('procurement.dashboard') : route('dashboard')) }}" class="brand-logo" style="gap: 12px; align-items: center; text-decoration: none;">
                <div class="d-flex flex-column align-items-center" style="flex-shrink: 0;">
                    <div class="brand-icon-wrapper" style="width: auto; height: 36px; padding: 2px 8px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px;">
                        <img src="{{ asset('assets/images/logo-pgncom-white.png') }}" alt="PGNCOM Logo" style="height: 22px; width: auto; object-fit: contain;">
                    </div>
                    <div class="brand-subtitle text-center" style="font-size: 0.58rem; color: #38BDF8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 4px; white-space: nowrap;">PGNCOM Monitoring</div>
                </div>
                <div>
                    <div class="brand-name" style="font-size: 0.98rem; font-weight: 800; color: #ffffff; letter-spacing: -0.01em; line-height: 1.25;">Input Realisasi</div>
                </div>
            </a>
        </div>

        <div class="sidebar-user px-3 py-2 border-bottom mb-2 text-center text-white" style="font-size: 0.8rem;">
            <div class="fw-bold">{{ auth()->user()->name }}</div>
            <div class="text-white-50 fs-8">{{ strtoupper(auth()->user()->role) }}</div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->check())
                @php
                    $smPendingBasto = auth()->user()->hasRole('osm_service_manager')
                        ? \App\Models\Basto::where('status', 'submitted')->where('sm_user_id', auth()->id())->count()
                        : 0;
                    $qcPendingBasto = auth()->user()->hasRole('osm_qc')
                        ? \App\Models\Basto::where('status', 'submitted')->count()
                        : 0;
                @endphp

                {{-- ====================================================================== --}}
                {{-- 1. ADMIN ROLE (Pengawasan Sistem, Tata Kelola & Monitoring Kinerja)    --}}
                {{-- ====================================================================== --}}
                @if(auth()->user()->hasRole('admin'))
                    @php
                        $isMonitoringActive = request()->routeIs('basto.*') || request()->routeIs('monitoring.*') || request()->routeIs('invoice.*');
                        $isDataActive       = request()->routeIs('realisasi.*') || request()->routeIs('admin.master-data.*') || request()->routeIs('prognosa.*');
                        $isSistemActive     = request()->routeIs('admin.dashboard') || request()->routeIs('admin.users.*') || request()->routeIs('admin.period-locks.*') || request()->routeIs('audit.log');
                    @endphp

                    {{-- 1. Dashboard Utama --}}
                    <div class="nav-section-title">Navigasi Utama</div>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard Realisasi</span>
                    </a>

                    {{-- 2. Accordion: Monitoring Kinerja --}}
                    <div class="sidebar-accordion-header {{ $isMonitoringActive ? '' : 'collapsed' }}" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseMonitoring" 
                         aria-expanded="{{ $isMonitoringActive ? 'true' : 'false' }}">
                        <span><i class="bi bi-pie-chart me-1.5 text-primary"></i> Monitoring Kinerja</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="collapse {{ $isMonitoringActive ? 'show' : '' }} sidebar-accordion-body" id="collapseMonitoring">
                        <a href="{{ route('basto.index') }}"
                           class="nav-link {{ request()->routeIs('basto.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-check"></i>
                            <span>Monitoring BASTO</span>
                        </a>
                        <a href="{{ route('monitoring.cost-kontrak') }}"
                           class="nav-link {{ request()->routeIs('monitoring.cost-kontrak') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>Monitoring Cost Kontrak</span>
                        </a>
                        <a href="{{ route('monitoring.invoice-vendor') }}"
                           class="nav-link {{ request()->routeIs('monitoring.invoice-vendor') ? 'active' : '' }}">
                            <i class="bi bi-receipt-cutoff"></i>
                            <span>Monitoring Invoice Vendor</span>
                        </a>
                        <a href="{{ route('invoice.index') }}"
                           class="nav-link {{ request()->routeIs('invoice.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Kelola &amp; Input Invoice</span>
                        </a>
                    </div>

                    {{-- 3. Accordion: Data & Finansial --}}
                    <div class="sidebar-accordion-header {{ $isDataActive ? '' : 'collapsed' }}" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseData" 
                         aria-expanded="{{ $isDataActive ? 'true' : 'false' }}">
                        <span><i class="bi bi-database me-1.5 text-info"></i> Data &amp; Finansial</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="collapse {{ $isDataActive ? 'show' : '' }} sidebar-accordion-body" id="collapseData">
                        <a href="{{ route('realisasi.index') }}"
                           class="nav-link {{ request()->routeIs('realisasi.index') || request()->routeIs('realisasi.show') ? 'active' : '' }}">
                            <i class="bi bi-table"></i>
                            <span>Data Realisasi</span>
                        </a>
                        <a href="{{ route('admin.master-data.index') }}"
                           class="nav-link {{ request()->routeIs('admin.master-data.*') ? 'active' : '' }}">
                            <i class="bi bi-database-fill-gear"></i>
                            <span>Master Data</span>
                        </a>
                        <a href="{{ route('prognosa.index') }}"
                           class="nav-link {{ request()->routeIs('prognosa.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Prognosa &amp; Budget</span>
                        </a>
                    </div>

                    {{-- 4. Accordion: Sistem & Tata Kelola --}}
                    <div class="sidebar-accordion-header {{ $isSistemActive ? '' : 'collapsed' }}" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseSistem" 
                         aria-expanded="{{ $isSistemActive ? 'true' : 'false' }}">
                        <span><i class="bi bi-shield-lock me-1.5 text-warning"></i> Tata Kelola &amp; Akun</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </div>
                    <div class="collapse {{ $isSistemActive ? 'show' : '' }} sidebar-accordion-body" id="collapseSistem">
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span>User Management</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Kelola Akun</span>
                        </a>
                        <a href="{{ route('admin.period-locks.index') }}"
                           class="nav-link {{ request()->routeIs('admin.period-locks.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar2-check-fill"></i>
                            <span>Kunci Periode</span>
                        </a>
                        <a href="{{ route('audit.log') }}"
                           class="nav-link {{ request()->routeIs('audit.log') ? 'active' : '' }}">
                            <i class="bi bi-shield-check"></i>
                            <span>Audit Log</span>
                        </a>
                    </div>
                @endif

                {{-- ====================================================================== --}}
                {{-- 2. DMO ROLE (Master Proyek, Pagu, Data Realisasi & Buat BASTO)        --}}
                {{-- ====================================================================== --}}
                @if(auth()->user()->hasRole('dmo'))
                    <div class="nav-section-title">Operasional DMO</div>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard Utama</span>
                    </a>
                    <a href="{{ route('admin.master-data.index', ['tab' => 'projects']) }}"
                       class="nav-link {{ request()->routeIs('admin.master-data.*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Input &amp; Kelola Proyek</span>
                    </a>
                    <a href="{{ route('realisasi.index') }}"
                       class="nav-link {{ request()->routeIs('realisasi.index') || request()->routeIs('realisasi.show') ? 'active' : '' }}">
                        <i class="bi bi-table"></i>
                        <span>Data Realisasi</span>
                    </a>
                    <a href="{{ route('realisasi.create') }}"
                       class="nav-link {{ request()->routeIs('realisasi.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Realisasi</span>
                    </a>
                    <a href="{{ route('realisasi.import.form') }}"
                       class="nav-link {{ request()->routeIs('realisasi.import.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <span>Import Realisasi Excel</span>
                    </a>
                    <a href="{{ route('dmo.import.history') }}"
                       class="nav-link {{ request()->routeIs('dmo.import.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Import Log</span>
                    </a>

                    <div class="nav-section-title mt-2">Pengajuan Dokumen</div>
                    <a href="{{ route('basto.create') }}"
                       class="nav-link {{ request()->routeIs('basto.create') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-plus"></i>
                        <span>Buat BASTO (Upload PDF)</span>
                    </a>
                    {{-- Work Order disembunyikan sementara sesuai arahan user --}}
                    @if(config('app.show_work_order', false))
                    <a href="{{ route('work-order.index') }}"
                       class="nav-link {{ request()->routeIs('work-order.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Work Order</span>
                    </a>
                    @endif

                    <div class="nav-section-title mt-2">Monitoring Portofolio</div>
                    <a href="{{ route('basto.index') }}"
                       class="nav-link {{ request()->routeIs('basto.index') || request()->routeIs('basto.show') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-check"></i>
                        <span>Monitoring BASTO</span>
                    </a>
                    <a href="{{ route('monitoring.cost-kontrak') }}"
                       class="nav-link {{ request()->routeIs('monitoring.cost-kontrak') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i>
                        <span>Monitoring Cost Kontrak</span>
                    </a>
                    <a href="{{ route('monitoring.invoice-vendor') }}"
                       class="nav-link {{ request()->routeIs('monitoring.invoice-vendor') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>Monitoring Invoice Vendor</span>
                    </a>
                @endif

                {{-- ====================================================================== --}}
                {{-- 3. SERVICE MANAGER ROLE (Persetujuan BASTO, Prognosa & Pengawasan SM)   --}}
                {{-- ====================================================================== --}}
                @if(auth()->user()->hasRole('osm_service_manager'))
                    <div class="nav-section-title">Manajemen Service</div>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard Supervised</span>
                    </a>
                    <a href="{{ route('basto.index') }}"
                       class="nav-link {{ request()->routeIs('basto.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-check"></i>
                        <span>Persetujuan BASTO</span>
                        @if($smPendingBasto > 0)
                            <span class="badge bg-danger rounded-pill ms-auto" style="font-size:0.65rem;">{{ $smPendingBasto }} Perlu Review</span>
                        @endif
                    </a>
                    <a href="{{ route('realisasi.index') }}"
                       class="nav-link {{ request()->routeIs('realisasi.index') || request()->routeIs('realisasi.show') ? 'active' : '' }}">
                        <i class="bi bi-table"></i>
                        <span>Realisasi Proyek Saya</span>
                    </a>
                    <a href="{{ route('prognosa.index') }}"
                       class="nav-link {{ request()->routeIs('prognosa.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Prognosa &amp; Update Bulanan</span>
                    </a>
                    {{-- Work Order disembunyikan sementara sesuai arahan user --}}
                    @if(config('app.show_work_order', false))
                    <a href="{{ route('work-order.index') }}"
                       class="nav-link {{ request()->routeIs('work-order.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Work Order Proyek</span>
                    </a>
                    @endif

                    <div class="nav-section-title mt-2">Pengawasan Biaya</div>
                    <a href="{{ route('monitoring.cost-kontrak') }}"
                       class="nav-link {{ request()->routeIs('monitoring.cost-kontrak') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i>
                        <span>Monitoring Cost Kontrak</span>
                    </a>
                @endif

                {{-- ====================================================================== --}}
                {{-- 4. QUALITY CONTROL ROLE (qc & qc2: Inspeksi Mutu & Validasi BASTO)     --}}
                {{-- ====================================================================== --}}
                @if(auth()->user()->hasRole('osm_qc'))
                    <div class="nav-section-title">Quality Assurance</div>
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard Mutu QC</span>
                    </a>
                    <a href="{{ route('basto.index') }}"
                       class="nav-link {{ request()->routeIs('basto.*') ? 'active' : '' }}">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Inspeksi Mutu BASTO</span>
                        @if($qcPendingBasto > 0)
                            <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:0.65rem;">{{ $qcPendingBasto }} Antrean</span>
                        @endif
                    </a>
                    <a href="{{ route('realisasi.index') }}"
                       class="nav-link {{ request()->routeIs('realisasi.index') || request()->routeIs('realisasi.show') ? 'active' : '' }}">
                        <i class="bi bi-table"></i>
                        <span>Data Realisasi Lapangan</span>
                    </a>
                    {{-- Work Order disembunyikan sementara sesuai arahan user --}}
                    @if(config('app.show_work_order', false))
                    <a href="{{ route('work-order.index') }}"
                       class="nav-link {{ request()->routeIs('work-order.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Work Order Fisik</span>
                    </a>
                    @endif
                @endif

                {{-- ====================================================================== --}}
                {{-- 5. PROCUREMENT ROLE (Pengadaan: Vendor, PO/SPK, 3-Way Match & Tagihan) --}}
                {{-- ====================================================================== --}}
                @if(auth()->user()->hasRole('procurement'))
                    <div class="nav-section-title">Pengadaan Barang &amp; Jasa</div>
                    <a href="{{ route('procurement.dashboard') }}"
                       class="nav-link {{ request()->routeIs('procurement.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-cart-check-fill"></i>
                        <span>Dashboard Pengadaan</span>
                    </a>
                    <a href="{{ route('procurement.vendors') }}"
                       class="nav-link {{ request()->routeIs('procurement.vendors') ? 'active' : '' }}">
                        <i class="bi bi-buildings-fill"></i>
                        <span>Direktori Rekanan Vendor</span>
                    </a>
                    <a href="{{ route('procurement.orders') }}"
                       class="nav-link {{ request()->routeIs('procurement.orders*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-ruled-fill"></i>
                        <span>PO / SPK Pengadaan</span>
                    </a>
                    <a href="{{ route('procurement.verification') }}"
                       class="nav-link {{ request()->routeIs('procurement.verification') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i>
                        <span>Verifikasi 3-Way Match</span>
                    </a>
                    <a href="{{ route('monitoring.invoice-vendor') }}"
                       class="nav-link {{ request()->routeIs('monitoring.invoice-vendor') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>Tagihan Rekanan Vendor</span>
                    </a>
                @endif
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="app-version">Input Realisasi v2.0 &bull; {{ date('Y') }}</div>
        </div>
    </aside>

    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <button class="btn btn-sm btn-light d-lg-none me-2" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="flex-column">
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->check() && auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->check() && auth()->user()->hasRole('procurement') ? route('procurement.dashboard') : route('dashboard')) }}" class="text-decoration-none">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        <div class="topbar-right ms-auto d-flex align-items-center gap-2.5">
            <!-- Subtle Date Badge -->
            <div class="d-none d-lg-inline-flex align-items-center gap-1.5 px-3 py-1.5 bg-light rounded-pill border text-secondary" style="font-size: 0.78rem; font-weight: 600; height: 32px;" id="realtimeClock" title="Hari & Tanggal Aktif">
                <i class="bi bi-calendar3 text-primary" style="font-size: 0.8rem;"></i>
                <span id="clockValue">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span>
            </div>

            <!-- Quick Search Trigger -->
            <button type="button" class="search-trigger-btn d-none d-sm-inline-flex" data-bs-toggle="modal" data-bs-target="#quickSearchModal" title="Pencarian Cepat (Ctrl + K)">
                <i class="bi bi-search text-primary"></i>
                <span class="d-none d-md-inline">Cari...</span>
                <kbd class="bg-white border text-secondary px-1.5 py-0.5 rounded shadow-xs" style="font-size:0.65rem;">Ctrl K</kbd>
            </button>

            @if(auth()->check())
                @php
                    $user = auth()->user();
                    $notifList = [];
                    if ($user->hasRole('osm_service_manager')) {
                        $pendingB = \App\Models\Basto::where('status', 'submitted')->where('sm_user_id', $user->id)->get();
                        foreach ($pendingB as $pb) {
                            $notifList[] = [
                                'icon' => 'bi-clipboard-check text-warning',
                                'title' => 'Persetujuan BASTO',
                                'desc' => "{$pb->basto_number} butuh review Anda.",
                                'url' => route('basto.show', $pb->id),
                                'time' => $pb->submitted_at ? \Carbon\Carbon::parse($pb->submitted_at)->diffForHumans() : 'Baru'
                            ];
                        }
                    }
                    if ($user->hasRole('procurement')) {
                        $pendingInvCount = \App\Models\Realisasi::whereIn('status', ['WAIT INV', 'PROSES'])->count();
                        if ($pendingInvCount > 0) {
                            $notifList[] = [
                                'icon' => 'bi-receipt-cutoff text-warning',
                                'title' => 'Tagihan Menunggu Verifikasi',
                                'desc' => "{$pendingInvCount} tagihan vendor siap diverifikasi.",
                                'url' => route('procurement.verification'),
                                'time' => 'Sistem'
                            ];
                        }
                    }
                    if ($user->hasRole(['dmo', 'admin'])) {
                        $rejectedB = \App\Models\Basto::where('status', 'rejected')->limit(2)->get();
                        foreach ($rejectedB as $rb) {
                            $notifList[] = [
                                'icon' => 'bi-x-circle-fill text-danger',
                                'title' => 'BASTO Ditolak',
                                'desc' => "BASTO {$rb->basto_number} ditolak. Periksa alasan revisi.",
                                'url' => route('basto.show', $rb->id),
                                'time' => $rb->reviewed_at ? \Carbon\Carbon::parse($rb->reviewed_at)->diffForHumans() : 'Baru'
                            ];
                        }
                    }
                    if ($user->hasRole('admin')) {
                        try {
                            $activeLocksCount = \App\Models\PeriodLock::where('is_locked', true)->count();
                            if ($activeLocksCount > 0) {
                                $notifList[] = [
                                    'icon' => 'bi-lock-fill text-primary',
                                    'title' => 'Proteksi Periode Aktif',
                                    'desc' => "{$activeLocksCount} periode terkunci untuk mencegah modifikasi non-admin.",
                                    'url' => route('admin.period-locks.index'),
                                    'time' => 'Sistem'
                                ];
                            }
                        } catch (\Exception $e) {}
                    }

                    $notifCount = count($notifList);
                    $roleBadges = [
                        'admin' => 'bg-dark',
                        'osm_service_manager' => 'bg-primary',
                        'osm_qc' => 'bg-info text-dark',
                        'dmo' => 'bg-warning text-dark',
                        'procurement' => 'bg-indigo text-white',
                    ];
                    $roleLabel = [
                        'admin' => 'Administrator',
                        'osm_service_manager' => 'Service Manager',
                        'osm_qc' => 'QC Inspector',
                        'dmo' => 'DMO Staff',
                        'procurement' => 'Procurement Officer',
                    ];
                    $roleColor = $roleBadges[$user->role] ?? 'bg-secondary';
                    $roleText = $roleLabel[$user->role] ?? strtoupper($user->role);

                    $words = explode(' ', trim($user->name));
                    $initials = strtoupper(substr($words[0] ?? 'U', 0, 1) . substr($words[1] ?? '', 0, 1));
                @endphp

                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="notif-bell-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pusat Pemberitahuan">
                        <i class="bi bi-bell-fill fs-6"></i>
                        @if($notifCount > 0)
                            <span class="notif-pulse"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="width: 330px; z-index: 1070;">
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light rounded-top-4">
                            <span class="fw-bold text-dark fs-7"><i class="bi bi-bell-fill text-primary me-1.5"></i>Pemberitahuan</span>
                            <span class="badge {{ $notifCount > 0 ? 'bg-danger' : 'bg-secondary' }} rounded-pill px-2">{{ $notifCount }}</span>
                        </div>
                        <div class="p-2" style="max-height: 300px; overflow-y: auto;">
                            @forelse($notifList as $ntf)
                                <a href="{{ $ntf['url'] }}" class="d-flex align-items-start gap-2.5 p-2.5 rounded-3 text-decoration-none text-dark hover-bg-light mb-1 border-bottom border-light">
                                    <i class="bi {{ $ntf['icon'] }} fs-5 mt-0.5"></i>
                                    <div style="flex:1; overflow:hidden;">
                                        <div class="fw-bold fs-8 text-truncate">{{ $ntf['title'] }}</div>
                                        <div class="text-secondary small fs-9 text-truncate-2">{{ $ntf['desc'] }}</div>
                                        <div class="text-muted fs-9 mt-1">{{ $ntf['time'] }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-4 text-secondary">
                                    <i class="bi bi-check2-circle text-success fs-2 d-block mb-1"></i>
                                    <span class="fs-8">Semua tugas &amp; dokumen up-to-date!</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Unified User Profile Dropdown -->
                <div class="dropdown ms-1">
                    <button class="btn p-1 d-flex align-items-center gap-2 border-0 bg-transparent text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Pengguna">
                        <div class="user-avatar-circle">
                            {{ $initials ?: 'U' }}
                        </div>
                        <div class="d-none d-xl-block" style="line-height: 1.2;">
                            <div class="fw-bold text-dark fs-8">{{ $user->name }}</div>
                            <span class="badge {{ $roleColor }} px-2 py-0.5 rounded-pill mt-0.5" style="font-size:0.62rem; font-weight: 600;">
                                {{ $roleText }}
                            </span>
                        </div>
                        <i class="bi bi-chevron-down text-secondary fs-9 ms-0.5 d-none d-xl-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2" style="min-width: 230px; z-index: 1070;">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-bold text-dark fs-8">{{ $user->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $user->email ?? '-' }}</div>
                            <span class="badge {{ $roleColor }} rounded-pill mt-1" style="font-size: 0.62rem;">{{ $roleText }}</span>
                        </li>
                        @if($user->hasRole('admin'))
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fs-8" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-lock text-primary"></i> User Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fs-8" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people text-secondary"></i> Kelola Akun
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fs-8" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 text-primary"></i> Dashboard
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0" id="topbarLogoutForm">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fs-8 text-danger fw-semibold">
                                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">

        {{-- Impersonation Mode Permanent Banner (Never Auto-Dismiss) --}}
        @if(session()->has('impersonator_id'))
            <div class="impersonate-banner no-autodismiss d-flex align-items-center justify-content-between p-3 mb-4 shadow-sm" style="background: linear-gradient(90deg, #fff3cd 0%, #ffe69c 100%); border-left: 6px solid #ffc107; border-top: 1px solid #ffeeba; border-right: 1px solid #ffeeba; border-bottom: 1px solid #ffeeba; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px; min-width: 42px;">
                        <i class="bi bi-person-fill-gear fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-7">Mode Penyamaran Aktif (Impersonate Mode)</div>
                        <div class="text-muted fs-8">
                            Anda sedang melihat sistem sebagai <strong>{{ auth()->user()->name }}</strong> 
                            (Role: <span class="badge bg-dark text-white px-2 py-1">{{ strtoupper(auth()->user()->role) }}</span>). 
                            Banner ini akan tetap tampil sampai Anda kembali ke akun Admin.
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.stop-impersonate') }}" method="POST" class="m-0 ms-3 flex-shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-dark btn-sm d-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm rounded-pill" title="Kembali ke akun Admin semula">
                        <i class="bi bi-box-arrow-left"></i> Kembali ke Admin
                    </button>
                </form>
            </div>
        @endif

        {{-- DMO Alert Banner: BASTO Perlu Tindak Lanjut Revisi QC --}}
        @php
            $dmoGlobalRevisions = 0;
            if (auth()->check() && auth()->user()->hasRole(['dmo', 'admin'])) {
                $revQuery = \App\Models\Basto::where('qc_status', 'revision_needed');
                if (auth()->user()->hasRole('dmo')) {
                    $revQuery->where('dmo_user_id', auth()->id());
                }
                $dmoGlobalRevisions = $revQuery->count();
            }
        @endphp
        @if($dmoGlobalRevisions > 0)
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 py-2.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 bg-warning bg-opacity-25 text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-bell-fill text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                            Tindak Lanjut Revisi QC Diperlukan:
                            <span class="badge bg-danger text-white ms-1">{{ $dmoGlobalRevisions }} Dokumen BASTO</span>
                        </div>
                        <div class="text-secondary" style="font-size: 0.75rem;">
                            Tim Quality Control menemukan catatan perbaikan mutu pada berkas fisik BASTO. Silakan segera unggah dokumen revisi agar proses persetujuan Service Manager dapat berlanjut.
                        </div>
                    </div>
                </div>
                <a href="{{ route('basto.index', ['tab' => 'revision_needed']) }}" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                    <i class="bi bi-arrow-repeat"></i> Buka Antrean Revisi
                </a>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="page-entrance">
            @yield('content')
        </div>
    </main>

    <!-- Quick Search Modal (Ctrl + K) -->
    <div class="modal fade" id="quickSearchModal" tabindex="-1" aria-labelledby="quickSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="p-3 border-bottom bg-white d-flex align-items-center gap-2">
                    <i class="bi bi-search text-primary fs-5 ms-2"></i>
                    <input type="text" id="quickSearchInput" class="form-control border-0 shadow-none fs-6" placeholder="Ketik nomor kontrak, nama proyek, invoice, BASTO... (Esc untuk keluar)" autocomplete="off">
                    <kbd class="bg-light border text-secondary px-2 py-1 rounded small me-2" style="font-size:0.7rem;">Esc</kbd>
                </div>
                <div class="p-2.5" id="quickSearchResults" style="max-height: 380px; overflow-y: auto; background: #FAFAFA;">
                    <div class="text-center py-4 text-muted fs-8">
                        <i class="bi bi-keyboard fs-3 d-block mb-1 text-secondary opacity-50"></i>
                        Ketik minimal 2 karakter untuk mencari di seluruh sistem
                    </div>
                </div>
                <div class="p-2.5 px-3 bg-light border-top d-flex justify-content-between align-items-center fs-8 text-secondary">
                    <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Pencarian Cepat Antar Modul</span>
                    <span>Tekan <kbd class="bg-white border text-dark px-1.5 py-0.5 rounded shadow-xs" style="font-size:0.65rem;">Ctrl K</kbd> untuk buka cepat</span>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Ensure all Bootstrap modals are direct children of body so they are never blocked by any parent stacking contexts
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal').forEach(function(modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        });

        // Sidebar toggle mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('mainSidebar').classList.toggle('show');
        });

        // Auto dismiss alerts after 5s (except those marked as no-autodismiss)
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.no-autodismiss)').forEach(el => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                if(bsAlert) bsAlert.close();
            });
        }, 5000);

        // Realtime Date Update
        function updateRealtimeClock() {
            const clockEl = document.getElementById('clockValue');
            if (clockEl) {
                const now = new Date();
                const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' };
                clockEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
        updateRealtimeClock();
        setInterval(updateRealtimeClock, 60000);

        // Format number input as thousand-separated display
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // SweetAlert confirm delete
        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Hapus Data?',
                html: `Data <strong>${itemName}</strong> akan dihapus secara permanen.<br><small class="text-muted">Tindakan ini dapat diurungkan melalui admin database.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Global Quick Search (Ctrl + K / Cmd + K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const modalEl = document.getElementById('quickSearchModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            }
        });

        const quickInput = document.getElementById('quickSearchInput');
        const searchResults = document.getElementById('quickSearchResults');
        let searchDebounce = null;

        if (quickInput) {
            document.getElementById('quickSearchModal')?.addEventListener('shown.bs.modal', function() {
                quickInput.focus();
                quickInput.select();
            });

            quickInput.addEventListener('input', function() {
                clearTimeout(searchDebounce);
                const q = this.value.trim();
                if (q.length < 2) {
                    searchResults.innerHTML = '<div class="text-center py-4 text-muted fs-8"><i class="bi bi-keyboard fs-3 d-block mb-1 text-secondary opacity-50"></i>Ketik minimal 2 karakter untuk mencari di seluruh sistem</div>';
                    return;
                }

                searchResults.innerHTML = '<div class="text-center py-4 text-secondary fs-8"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Mencari data...</div>';

                searchDebounce = setTimeout(() => {
                    fetch(`/quick-search?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                searchResults.innerHTML = `<div class="text-center py-4 text-muted fs-8"><i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>Tidak ada data yang cocok dengan "<strong>${q}</strong>"</div>`;
                                return;
                            }
                            let html = '';
                            data.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="d-flex align-items-center gap-3 p-2.5 rounded-3 text-decoration-none text-dark bg-white border mb-2 shadow-xs hover-bg-light transition-all">
                                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center ${item.badge} text-white flex-shrink-0" style="width:38px; height:38px;">
                                            <i class="bi ${item.icon} fs-5"></i>
                                        </div>
                                        <div style="flex:1; overflow:hidden;">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge ${item.badge} rounded-pill px-2 py-0.5" style="font-size:0.65rem;">${item.type}</span>
                                                <div class="fw-bold fs-7 text-truncate">${item.title}</div>
                                            </div>
                                            <div class="text-secondary small fs-8 text-truncate mt-0.5">${item.subtitle}</div>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted fs-7 flex-shrink-0"></i>
                                    </a>
                                `;
                            });
                            searchResults.innerHTML = html;
                        })
                        .catch(() => {
                            searchResults.innerHTML = '<div class="text-center py-3 text-danger fs-8">Gagal memuat data pencarian.</div>';
                        });
                }, 250);
            });
        }

        // Global Bootstrap Tooltip Initialization
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Apply saved Table Density preference
            const savedDensity = localStorage.getItem('table_density');
            if (savedDensity === 'compact') {
                document.querySelectorAll('.data-table, table.table').forEach(tbl => {
                    tbl.classList.add('table-compact');
                });
                document.querySelectorAll('.btn-density-toggle').forEach(btn => {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="bi bi-arrows-expand me-1"></i>Tampilan Luas';
                });
            }
        });

        // Global Table Density Toggle Function
        window.toggleTableDensity = function(tableSelector = '.data-table, table.table') {
            const tables = document.querySelectorAll(tableSelector);
            if (tables.length === 0) return;
            const isCurrentlyCompact = tables[0].classList.contains('table-compact');
            
            tables.forEach(tbl => {
                if (isCurrentlyCompact) {
                    tbl.classList.remove('table-compact');
                } else {
                    tbl.classList.add('table-compact');
                }
            });

            const newDensity = isCurrentlyCompact ? 'comfortable' : 'compact';
            localStorage.setItem('table_density', newDensity);

            document.querySelectorAll('.btn-density-toggle').forEach(btn => {
                if (newDensity === 'compact') {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="bi bi-arrows-expand me-1"></i>Tampilan Luas';
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="bi bi-arrows-collapse me-1"></i>Tampilan Rapat';
                }
            });
        };
    </script>

    @stack('scripts')
</body>
</html>
