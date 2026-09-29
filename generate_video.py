import os
import sys
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import imageio

WIDTH = 1920
HEIGHT = 1088  # Macroblock divisible by 16 for H.264 standard
FPS = 24
OUTPUT_FILE = "video_alur_proses_proyek_pgn.mp4"

# Real Website Color Palette
NAVY_SIDEBAR = (15, 23, 42)        # #0F172A
NAVY_SIDEBAR_LIGHT = (30, 41, 59)  # #1E293B
WHITE = (255, 255, 255)
LIGHT_BG = (248, 250, 252)         # #F8FAFC
BORDER_COLOR = (226, 232, 240)     # #E2E8F0
BORDER_DARK = (51, 65, 85)
TEXT_DARK = (15, 23, 42)
TEXT_SECONDARY = (71, 85, 105)
TEXT_MUTED = (148, 163, 184)
TEXT_WHITE = (248, 250, 252)

PGN_BLUE = (2, 132, 199)          # #0284C7
PGN_CYAN = (56, 189, 248)         # #38BDF8
ACCENT_BLUE = (59, 130, 246)      # #3B82F6
GREEN = (16, 185, 129)            # #10B981
GREEN_BG = (236, 253, 245)
AMBER = (245, 158, 11)
AMBER_BG = (255, 251, 235)
RED = (239, 68, 68)
PURPLE = (139, 92, 246)

# Fonts
try:
    font_title = ImageFont.truetype("arialbd.ttf", 26)
    font_heading = ImageFont.truetype("arialbd.ttf", 20)
    font_body = ImageFont.truetype("arial.ttf", 16)
    font_bold = ImageFont.truetype("arialbd.ttf", 16)
    font_mono = ImageFont.truetype("consola.ttf", 17)
    font_mono_bold = ImageFont.truetype("consolab.ttf", 18)
    font_small = ImageFont.truetype("arial.ttf", 13)
    font_badge = ImageFont.truetype("arialbd.ttf", 12)
except Exception:
    font_title = ImageFont.load_default()
    font_heading = ImageFont.load_default()
    font_body = ImageFont.load_default()
    font_bold = ImageFont.load_default()
    font_mono = ImageFont.load_default()
    font_mono_bold = ImageFont.load_default()
    font_small = ImageFont.load_default()
    font_badge = ImageFont.load_default()

try:
    img_pgncom_white = Image.open("public/assets/images/logo-pgncom-white.png").convert("RGBA")
    img_pgncom_color = Image.open("public/assets/images/logo-pgncom.png").convert("RGBA")
except Exception:
    img_pgncom_white = None
    img_pgncom_color = None

SIDEBAR_WIDTH = 340
TOPBAR_HEIGHT = 70
BANNER_HEIGHT = 80

def draw_real_app_shell(scene_num, total_scenes, role_name, role_color, user_name, user_role, breadcrumb_text, page_title, active_menu_idx, banner_title, banner_desc):
    img = Image.new("RGB", (WIDTH, HEIGHT), LIGHT_BG)
    draw = ImageDraw.Draw(img)

    # 1. AUTHENTIC REAL SIDEBAR
    draw.rectangle([(0, 0), (SIDEBAR_WIDTH, HEIGHT)], fill=NAVY_SIDEBAR)
    draw.line([(SIDEBAR_WIDTH, 0), (SIDEBAR_WIDTH, HEIGHT)], fill=BORDER_DARK, width=1)

    # Sidebar Brand
    draw.rectangle([(0, 0), (SIDEBAR_WIDTH, 80)], fill=(11, 17, 32))
    draw.line([(0, 80), (SIDEBAR_WIDTH, 80)], fill=BORDER_DARK, width=1)
    
    # Emblem Icon / PGNCOM Logo
    if img_pgncom_white is not None:
        lw = 130
        lh = int(lw * (img_pgncom_white.height / img_pgncom_white.width))
        l_res = img_pgncom_white.resize((lw, lh), Image.Resampling.LANCZOS)
        img.paste(l_res, (20, 18), l_res)
        draw.text((20, 18 + lh + 4), "PGNCOM MONITORING", fill=PGN_CYAN, font=font_badge)
        draw.text((170, 26), "Input Realisasi", fill=WHITE, font=font_heading)
    else:
        draw.rounded_rectangle([(24, 18), (68, 62)], radius=10, fill=PGN_BLUE)
        draw.text((32, 22), "PGN", fill=WHITE, font=font_heading)
        draw.text((80, 22), "Input Realisasi", fill=WHITE, font=font_heading)
        draw.text((80, 48), "PGNCOM MONITORING", fill=PGN_CYAN, font=font_badge)

    # User Profile Card in Sidebar
    draw.rectangle([(0, 80), (SIDEBAR_WIDTH, 140)], fill=(18, 28, 50))
    draw.line([(0, 140), (SIDEBAR_WIDTH, 140)], fill=BORDER_DARK, width=1)
    # Online dot
    draw.ellipse([(24, 105), (34, 115)], fill=GREEN)
    draw.text((44, 96), user_name, fill=WHITE, font=font_bold)
    draw.text((44, 117), user_role, fill=TEXT_MUTED, font=font_small)

    # Nav Sections
    menu_items = [
        ("Dashboard Realisasi", "speedometer2"),
        ("Monitoring BASTO", "clipboard"),
        ("Cost Kontrak", "pie-chart"),
        ("Invoice Vendor", "receipt"),
        ("PO Pengadaan", "cart"),
        ("Master Data Proyek", "database")
    ]

    draw.text((24, 155), "TIGA PILAR & OPERASIONAL", fill=(100, 116, 139), font=font_badge)

    start_y = 180
    for idx, (m_title, _) in enumerate(menu_items):
        is_active = (idx == active_menu_idx)
        btn_y = start_y + (idx * 46)

        if is_active:
            draw.rounded_rectangle([(16, btn_y), (SIDEBAR_WIDTH - 16, btn_y + 38)], radius=8, fill=(30, 58, 100))
            draw.line([(16, btn_y), (16, btn_y + 38)], fill=ACCENT_BLUE, width=4)
            draw.text((38, btn_y + 8), m_title, fill=PGN_CYAN, font=font_bold)
        else:
            draw.text((38, btn_y + 8), m_title, fill=TEXT_MUTED, font=font_body)

    # Sidebar Footer
    draw.rectangle([(0, HEIGHT - 45), (SIDEBAR_WIDTH, HEIGHT)], fill=(11, 17, 32))
    draw.text((24, HEIGHT - 30), "PGN Solution v2.4 • Secured", fill=(100, 116, 139), font=font_small)

    # 2. AUTHENTIC REAL TOPBAR
    draw.rectangle([(SIDEBAR_WIDTH, 0), (WIDTH, TOPBAR_HEIGHT)], fill=WHITE)
    draw.line([(SIDEBAR_WIDTH, TOPBAR_HEIGHT), (WIDTH, TOPBAR_HEIGHT)], fill=BORDER_COLOR, width=1)

    draw.text((SIDEBAR_WIDTH + 30, 14), breadcrumb_text, fill=TEXT_MUTED, font=font_small)
    draw.text((SIDEBAR_WIDTH + 30, 34), page_title, fill=TEXT_DARK, font=font_title)

    # Topbar Right: Search trigger & Bell & User Avatar
    search_x = WIDTH - 380
    draw.rounded_rectangle([(search_x, 18), (WIDTH - 120, 52)], radius=8, fill=(241, 245, 249), outline=BORDER_COLOR, width=1)
    draw.text((search_x + 15, 26), "Cari Data, Proyek, PO, BASTO... (Ctrl+K)", fill=TEXT_MUTED, font=font_small)

    # User circle
    draw.ellipse([(WIDTH - 70, 16), (WIDTH - 32, 54)], fill=PGN_BLUE)
    draw.text((WIDTH - 58, 25), "PGN", fill=WHITE, font=font_badge)

    # 3. FLOATING EXPLANATORY CALLOUT BANNER (TOP OF CONTENT AREA)
    banner_y1 = TOPBAR_HEIGHT + 15
    banner_y2 = banner_y1 + BANNER_HEIGHT
    content_x1 = SIDEBAR_WIDTH + 30
    content_x2 = WIDTH - 30

    draw.rounded_rectangle([(content_x1, banner_y1), (content_x2, banner_y2)], radius=12, fill=NAVY_SIDEBAR, outline=PGN_CYAN, width=1)

    # Role Badge
    draw.rounded_rectangle([(content_x1 + 20, banner_y1 + 22), (content_x1 + 180, banner_y1 + 58)], radius=8, fill=role_color)
    draw.text((content_x1 + 35, banner_y1 + 30), role_name, fill=WHITE, font=font_bold)

    # Explanatory Text
    draw.text((content_x1 + 200, banner_y1 + 16), banner_title, fill=WHITE, font=font_heading)
    draw.text((content_x1 + 200, banner_y1 + 46), banner_desc, fill=TEXT_MUTED, font=font_body)

    # Step Badge
    step_txt = f"LANGKAH {scene_num} DARI {total_scenes}"
    draw.rounded_rectangle([(content_x2 - 180, banner_y1 + 24), (content_x2 - 20, banner_y1 + 56)], radius=16, fill=(30, 41, 59), outline=PGN_CYAN, width=1)
    draw.text((content_x2 - 165, banner_y1 + 32), step_txt, fill=PGN_CYAN, font=font_badge)

    # 4. BOTTOM VIDEO TIMELINE
    timeline_y = HEIGHT - 20
    draw.rectangle([(SIDEBAR_WIDTH, timeline_y), (WIDTH, HEIGHT)], fill=(15, 23, 42))
    progress_w = SIDEBAR_WIDTH + int(((scene_num) / total_scenes) * (WIDTH - SIDEBAR_WIDTH))
    draw.rectangle([(SIDEBAR_WIDTH, timeline_y), (progress_w, HEIGHT)], fill=PGN_BLUE)

    return img, draw

def render_scene_1(writer):
    # Scene 1: Master Data Proyek (DMO Staff)
    chars_id = "PRJ-2026-PGN-001"
    chars_name = "Operasi & Pemeliharaan Gas GMS"
    chars_pagu = "2.500.000.000"

    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            1, 8, "DMO STAFF", PGN_BLUE, "Masyita Mustika", "DMO OPERASIONAL",
            "Home > Admin > Master Data > Proyek & Pagu",
            "Pusat Kelola Master Data & Pagu Proyek",
            5, # Master data active
            "Babak 1: Inisiasi Proyek Baru di Master Data",
            "DMO Staff mendaftarkan proyek baru & mengunci pagu anggaran (budget ceiling) resmi."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 35

        # Modal Box "Daftarkan Master Proyek & Pagu Baru"
        modal_w = 1100
        modal_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - modal_w) // 2
        modal_y1 = content_top
        modal_x2 = modal_x1 + modal_w
        modal_y2 = modal_y1 + 680

        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y2)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=2)
        
        # Modal Header
        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y1 + 65)], radius=14, fill=(248, 250, 252))
        draw.line([(modal_x1, modal_y1 + 65), (modal_x2, modal_y1 + 65)], fill=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 30, modal_y1 + 18), "Daftarkan Master Proyek & Pagu Anggaran Baru", fill=TEXT_DARK, font=font_heading)
        draw.text((modal_x1 + 30, modal_y1 + 42), "Pagu ini menjadi batas plafon serapan realisasi dan kontrol biaya operasional", fill=TEXT_MUTED, font=font_small)

        # Inputs typing simulation
        f1_progress = min(1.0, max(0.0, (i - 10) / 25))
        f2_progress = min(1.0, max(0.0, (i - 35) / 30))
        f3_progress = min(1.0, max(0.0, (i - 65) / 25))

        txt_id = chars_id[:int(f1_progress * len(chars_id))]
        txt_name = chars_name[:int(f2_progress * len(chars_name))]
        txt_pagu = chars_pagu[:int(f3_progress * len(chars_pagu))]

        # Field 1: Project ID
        fy1 = modal_y1 + 95
        draw.text((modal_x1 + 40, fy1), "PROJECT ID / KODE PROYEK *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy1 + 22), (modal_x1 + 480, fy1 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f1_progress > 0 else BORDER_COLOR, width=2 if f1_progress > 0 else 1)
        draw.text((modal_x1 + 55, fy1 + 32), txt_id, fill=ACCENT_BLUE, font=font_mono_bold)

        # Field 2: Nama Proyek
        draw.text((modal_x1 + 520, fy1), "NAMA LENGKAP PROYEK *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 520, fy1 + 22), (modal_x2 - 40, fy1 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f2_progress > 0 else BORDER_COLOR, width=2 if f2_progress > 0 else 1)
        draw.text((modal_x1 + 535, fy1 + 32), txt_name, fill=TEXT_DARK, font=font_bold)

        # Field 3: Pagu Anggaran
        fy2 = fy1 + 95
        draw.text((modal_x1 + 40, fy2), "NILAI PAGU ANGGARAN (KONTRAK INDUK) *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy2 + 22), (modal_x1 + 600, fy2 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f3_progress > 0 else BORDER_COLOR, width=2 if f3_progress > 0 else 1)
        draw.text((modal_x1 + 55, fy2 + 32), f"Rp {txt_pagu}", fill=TEXT_DARK, font=font_mono_bold)

        if f3_progress >= 1.0:
            draw.text((modal_x1 + 55, fy2 + 75), "Terbilang: Dua Koma Lima Miliar Rupiah", fill=PGN_BLUE, font=font_small)

        # Field 4: Tahun & SM
        draw.text((modal_x1 + 640, fy2), "TAHUN ANGGARAN", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 640, fy2 + 22), (modal_x1 + 820, fy2 + 68)], radius=8, fill=(241, 245, 249), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 660, fy2 + 32), "2026", fill=TEXT_DARK, font=font_bold)

        draw.text((modal_x1 + 850, fy2), "SERVICE MANAGER / PIC", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 850, fy2 + 22), (modal_x2 - 40, fy2 + 68)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 865, fy2 + 32), "GILANG (OSM)", fill=TEXT_DARK, font=font_bold)

        # Field 5: Client
        fy3 = fy2 + 105
        draw.text((modal_x1 + 40, fy3), "CLIENT / PEMILIK PEKERJAAN", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy3 + 22), (modal_x2 - 40, fy3 + 68)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 55, fy3 + 32), "PT Perusahaan Gas Negara Tbk (Strategic Customer)", fill=TEXT_DARK, font=font_body)

        # Save Button
        btn_y = modal_y2 - 80
        is_clicked = (i > 100)
        btn_color = (16, 185, 129) if is_clicked else PGN_BLUE
        draw.rounded_rectangle([(modal_x2 - 280, btn_y), (modal_x2 - 40, btn_y + 48)], radius=8, fill=btn_color)
        btn_label = "Tersimpan di Master Data!" if is_clicked else "Simpan Master Proyek"
        draw.text((modal_x2 - 255, btn_y + 14), btn_label, fill=WHITE, font=font_bold)

        # Moving Cursor
        if i < 35:
            cx, cy = modal_x1 + 200, fy1 + 45
        elif i < 65:
            cx, cy = modal_x1 + 650, fy1 + 45
        elif i < 95:
            cx, cy = modal_x1 + 300, fy2 + 45
        else:
            cx, cy = modal_x2 - 160, btn_y + 24

        draw.polygon([(cx, cy), (cx + 18, cy + 18), (cx + 8, cy + 18), (cx + 14, cy + 28), (cx + 9, cy + 30), (cx + 4, cy + 20), (cx, cy + 24)], fill=(15, 23, 42), outline=WHITE)

        writer.append_data(np.array(img))

def render_scene_2(writer):
    # Scene 2: Procurement PO & Prognosa
    chars_po = "PO/2026/PGN-SCM/014"
    chars_item = "Pengadaan Pipa High-Pressure & Valve"
    chars_val = "850.000.000"

    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            2, 8, "PROCUREMENT", PURPLE, "Reza Pahlevi", "PROCUREMENT OFFICER",
            "Home > Pengadaan > PO & SPK Pengadaan",
            "PO / SPK Pengadaan Barang & Jasa",
            4, # PO Pengadaan active
            "Babak 2: Perencanaan PO & Prognosa Biaya",
            "Divisi Pengadaan menerbitkan PO/SPK dan mengunci komitmen prognosa serapan anggaran."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 35
        modal_w = 1100
        modal_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - modal_w) // 2
        modal_y1 = content_top
        modal_x2 = modal_x1 + modal_w
        modal_y2 = modal_y1 + 680

        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y2)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=2)
        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y1 + 65)], radius=14, fill=(248, 250, 252))
        draw.line([(modal_x1, modal_y1 + 65), (modal_x2, modal_y1 + 65)], fill=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 30, modal_y1 + 18), "Penerbitan Surat Pesanan (PO / SPK) & Prognosa", fill=TEXT_DARK, font=font_heading)
        draw.text((modal_x1 + 30, modal_y1 + 42), "Mengunci alokasi biaya rekanan agar tidak melebihi sisa pagu proyek", fill=TEXT_MUTED, font=font_small)

        f1 = min(1.0, max(0.0, (i - 10) / 25))
        f2 = min(1.0, max(0.0, (i - 40) / 25))
        f3 = min(1.0, max(0.0, (i - 70) / 25))

        # Fields
        fy1 = modal_y1 + 95
        draw.text((modal_x1 + 40, fy1), "NOMOR PO / SPK PENGADAAN *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy1 + 22), (modal_x1 + 480, fy1 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f1 > 0 else BORDER_COLOR, width=2 if f1 > 0 else 1)
        draw.text((modal_x1 + 55, fy1 + 32), chars_po[:int(f1 * len(chars_po))], fill=ACCENT_BLUE, font=font_mono_bold)

        draw.text((modal_x1 + 520, fy1), "JUDUL PENGADAAN / ITEM PEKERJAAN *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 520, fy1 + 22), (modal_x2 - 40, fy1 + 68)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 535, fy1 + 32), chars_item[:int(f2 * len(chars_item))], fill=TEXT_DARK, font=font_bold)

        fy2 = fy1 + 95
        draw.text((modal_x1 + 40, fy2), "REKANAN VENDOR TERPILIH *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy2 + 22), (modal_x1 + 580, fy2 + 68)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 55, fy2 + 32), "PT Mitra Gas Fabrikasi (Vendor Terdaftar SCM)", fill=TEXT_DARK, font=font_bold)

        draw.text((modal_x1 + 620, fy2), "TERM OF PAYMENT (TOP)", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 620, fy2 + 22), (modal_x2 - 40, fy2 + 68)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 640, fy2 + 32), "30 Hari Kalender (Standard PGN)", fill=TEXT_DARK, font=font_bold)

        fy3 = fy2 + 95
        draw.text((modal_x1 + 40, fy3), "NILAI KOMITMEN PO (ESTIMASI PROGNOSA) *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy3 + 22), (modal_x1 + 600, fy3 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f3 > 0 else BORDER_COLOR, width=2 if f3 > 0 else 1)
        draw.text((modal_x1 + 55, fy3 + 32), f"Rp {chars_val[:int(f3 * len(chars_val))]}", fill=ACCENT_BLUE, font=font_mono_bold)

        if f3 >= 1.0:
            draw.text((modal_x1 + 55, fy3 + 75), "✓ Terverifikasi: Nilai PO di bawah plafon pagu (Rp 2,50 Miliar)", fill=GREEN, font=font_small)

        # Button
        btn_y = modal_y2 - 80
        is_sub = (i > 100)
        draw.rounded_rectangle([(modal_x2 - 320, btn_y), (modal_x2 - 40, btn_y + 48)], radius=8, fill=GREEN if is_sub else PURPLE)
        draw.text((modal_x2 - 300, btn_y + 14), "PO Terbit & Prognosa Terkunci" if is_sub else "Terbitkan PO & Kunci Prognosa", fill=WHITE, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_3(writer):
    # Scene 3: Monitoring Invoice Vendor & Aging
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            3, 8, "DMO & SCM", AMBER, "Masyita Mustika", "DMO OPERASIONAL",
            "Home > Monitoring > Invoice Vendor",
            "Monitoring Invoice Vendor & Sensor Aging 90 Hari",
            3, # Invoice active
            "Babak 3: Realisasi Tagihan & Sensor Aging 90 Hari",
            "Sistem otomatis menghitung umur tagihan (Aging) untuk mencegah denda penalti keterlambatan."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 25

        # 4 Aging Status Metric Cards
        card_w = 260
        cards = [
            ("AGING < 30 HARI", "18 Invoice", "Aman Terkendali", GREEN, GREEN_BG),
            ("AGING 30 - 60 HARI", "6 Invoice", "Jatuh Tempo Normal", PGN_BLUE, (239, 246, 255)),
            ("AGING 61 - 90 HARI", "2 Invoice", "Perlu Perhatian", AMBER, AMBER_BG),
            ("AGING > 90 HARI", "0 Invoice", "Zero Overdue (Aman)", RED, (254, 242, 242)),
        ]

        for idx, (ctitle, cval, cstatus, ccol, cbg) in enumerate(cards):
            cx = SIDEBAR_WIDTH + 30 + (idx * (card_w + 20))
            draw.rounded_rectangle([(cx, content_top), (cx + card_w, content_top + 85)], radius=10, fill=cbg, outline=ccol, width=1)
            draw.text((cx + 15, content_top + 12), ctitle, fill=ccol, font=font_badge)
            draw.text((cx + 15, content_top + 32), cval, fill=TEXT_DARK, font=font_heading)
            draw.text((cx + 15, content_top + 60), cstatus, fill=ccol, font=font_small)

        # Real Table
        table_y = content_top + 105
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 30, table_y), (WIDTH - 30, table_y + 500)], radius=12, fill=WHITE, outline=BORDER_COLOR, width=1)

        # Table Header
        draw.rectangle([(SIDEBAR_WIDTH + 30, table_y), (WIDTH - 30, table_y + 45)], fill=(241, 245, 249))
        draw.line([(SIDEBAR_WIDTH + 30, table_y + 45), (WIDTH - 30, table_y + 45)], fill=BORDER_COLOR, width=1)

        headers = ["NO", "PROJECT ID", "NO. INVOICE", "REKANAN VENDOR", "NILAI TAGIHAN", "TGL JATUH TEMPO", "STATUS INVOICE", "SENSOR AGING"]
        offsets = [20, 70, 240, 440, 740, 920, 1100, 1260]

        for h, off in zip(headers, offsets):
            draw.text((SIDEBAR_WIDTH + 30 + off, table_y + 14), h, fill=TEXT_SECONDARY, font=font_bold)

        # Row 1 (Highlight)
        r1_y = table_y + 45
        draw.rectangle([(SIDEBAR_WIDTH + 30, r1_y), (WIDTH - 30, r1_y + 60)], fill=(239, 246, 255))
        draw.line([(SIDEBAR_WIDTH + 30, r1_y + 60), (WIDTH - 30, r1_y + 60)], fill=BORDER_COLOR, width=1)

        draw.text((SIDEBAR_WIDTH + 50, r1_y + 18), "1", fill=TEXT_DARK, font=font_bold)
        draw.text((SIDEBAR_WIDTH + 100, r1_y + 18), "PRJ-2026-PGN-001", fill=ACCENT_BLUE, font=font_mono_bold)
        draw.text((SIDEBAR_WIDTH + 270, r1_y + 18), "INV-2026-0442", fill=TEXT_DARK, font=font_mono_bold)
        draw.text((SIDEBAR_WIDTH + 470, r1_y + 18), "PT Mitra Gas Fabrikasi", fill=TEXT_DARK, font=font_body)
        draw.text((SIDEBAR_WIDTH + 770, r1_y + 18), "Rp 820.000.000", fill=TEXT_DARK, font=font_mono_bold)
        draw.text((SIDEBAR_WIDTH + 950, r1_y + 18), "28 Okt 2026", fill=TEXT_DARK, font=font_body)

        # Status Badge
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 1120, r1_y + 12), (SIDEBAR_WIDTH + 1230, r1_y + 44)], radius=6, fill=(219, 234, 254))
        draw.text((SIDEBAR_WIDTH + 1140, r1_y + 20), "PROSES", fill=(30, 64, 175), font=font_bold)

        # Aging Sensor Badge
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 1280, r1_y + 12), (SIDEBAR_WIDTH + 1480, r1_y + 44)], radius=6, fill=GREEN_BG, outline=GREEN, width=1)
        draw.text((SIDEBAR_WIDTH + 1295, r1_y + 20), "15 HARI (AMAN < 90 HARI)", fill=GREEN, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_4(writer):
    # Scene 4: Monitoring Cost Kontrak
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            4, 8, "COST CONTROL", GREEN, "Gilang Ramadhan", "SERVICE MANAGER & COST CONTROL",
            "Home > Monitoring > Cost Kontrak",
            "Monitoring Cost Kontrak (Cost vs Realisasi)",
            2, # Cost Kontrak active
            "Babak 4: Evaluasi Serapan Anggaran & Status Kesehatan",
            "Status kesehatan anggaran proyek dihitung otomatis (🟢 Sehat jika serapan < 75% dari pagu)."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 25

        # 3 Real KPI Health Cards
        hw = 360
        cards = [
            ("🟢 KATEGORI SEHAT", "28 Proyek", "Serapan < 75% dari Pagu", GREEN, GREEN_BG),
            ("🟡 KATEGORI WASPADA", "4 Proyek", "Serapan 75% - 90% dari Pagu", AMBER, AMBER_BG),
            ("🔴 KATEGORI KRITIS", "0 Proyek", "Serapan > 90% (Zero Overrun)", RED, (254, 242, 242)),
        ]

        for idx, (ct, cv, cd, cc, cb) in enumerate(cards):
            cx = SIDEBAR_WIDTH + 30 + (idx * (hw + 25))
            draw.rounded_rectangle([(cx, content_top), (cx + hw, content_top + 95)], radius=12, fill=cb, outline=cc, width=1)
            draw.text((cx + 20, content_top + 16), ct, fill=cc, font=font_bold)
            draw.text((cx + 20, content_top + 40), cv, fill=TEXT_DARK, font=font_title)
            draw.text((cx + 20, content_top + 70), cd, fill=TEXT_SECONDARY, font=font_small)

        # Cost Analysis Card
        card_y = content_top + 120
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 30, card_y), (WIDTH - 30, card_y + 480)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=1)

        draw.text((SIDEBAR_WIDTH + 60, card_y + 25), "Rasio Serapan Anggaran Proyek PRJ-2026-PGN-001", fill=TEXT_DARK, font=font_heading)

        # 3 Value Boxes
        vw = 360
        v_boxes = [
            ("NILAI PAGU ANGGARAN", "Rp 2.500.000.000", TEXT_DARK),
            ("REALISASI TAGIHAN VENDOR", "Rp 820.000.000", ACCENT_BLUE),
            ("SISA ANGGARAN TERSEDIA", "Rp 1.680.000.000", GREEN),
        ]

        for idx, (vt, vv, vc) in enumerate(v_boxes):
            vx = SIDEBAR_WIDTH + 60 + (idx * (vw + 30))
            draw.rounded_rectangle([(vx, card_y + 70), (vx + vw, card_y + 160)], radius=10, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
            draw.text((vx + 20, card_y + 88), vt, fill=TEXT_MUTED, font=font_small)
            draw.text((vx + 20, card_y + 118), vv, fill=vc, font=font_mono_bold)

        # Progress Bar Animation
        progress_pct = min(0.328, (i / 70) * 0.328)
        bar_y = card_y + 200
        draw.text((SIDEBAR_WIDTH + 60, bar_y), "Tingkat Serapan Anggaran Terhadap Pagu Kontrak", fill=TEXT_SECONDARY, font=font_bold)
        draw.text((WIDTH - 200, bar_y), f"{progress_pct * 100:.1f}%", fill=ACCENT_BLUE, font=font_mono_bold)

        draw.rounded_rectangle([(SIDEBAR_WIDTH + 60, bar_y + 30), (WIDTH - 60, bar_y + 60)], radius=15, fill=(226, 232, 240))
        fill_w = SIDEBAR_WIDTH + 60 + int(progress_pct * (WIDTH - 120 - SIDEBAR_WIDTH))
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 60, bar_y + 30), (fill_w, bar_y + 60)], radius=15, fill=ACCENT_BLUE)

        # Health Badge
        draw.rounded_rectangle([(SIDEBAR_WIDTH + 60, bar_y + 80), (SIDEBAR_WIDTH + 340, bar_y + 120)], radius=8, fill=GREEN_BG, outline=GREEN, width=1)
        draw.text((SIDEBAR_WIDTH + 80, bar_y + 92), "STATUS: 🟢 SEHAT (< 75% PAGU)", fill=GREEN, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_5(writer):
    # Scene 5: Monitoring BASTO & Upload PDF
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            5, 8, "DMO STAFF", PGN_BLUE, "Masyita Mustika", "DMO OPERASIONAL",
            "Home > Monitoring > BASTO > Upload Berkas",
            "Monitoring BASTO & Alur Verifikasi Bertingkat",
            1, # BASTO active
            "Babak 5: Pengajuan Dokumen BASTO & Upload PDF",
            "DMO mengunggah berkas Berita Acara Serah Terima Operasional bertanda tangan basah ke alur QC."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 30
        modal_w = 1100
        modal_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - modal_w) // 2
        modal_y1 = content_top
        modal_x2 = modal_x1 + modal_w
        modal_y2 = modal_y1 + 680

        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y2)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=2)
        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y1 + 65)], radius=14, fill=(248, 250, 252))
        draw.line([(modal_x1, modal_y1 + 65), (modal_x2, modal_y1 + 65)], fill=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 30, modal_y1 + 18), "Buat & Upload Berkas BASTO Baru", fill=TEXT_DARK, font=font_heading)
        draw.text((modal_x1 + 30, modal_y1 + 42), "Unggah scan PDF dokumen sah untuk diverifikasi mutu oleh OSM QC", fill=TEXT_MUTED, font=font_small)

        f1 = min(1.0, max(0.0, (i - 10) / 25))
        chars_basto = "004/BASTO/SMO/2026"

        fy1 = modal_y1 + 95
        draw.text((modal_x1 + 40, fy1), "NOMOR BASTO RESMI *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy1 + 22), (modal_x1 + 480, fy1 + 68)], radius=8, fill=WHITE, outline=ACCENT_BLUE if f1 > 0 else BORDER_COLOR, width=2 if f1 > 0 else 1)
        draw.text((modal_x1 + 55, fy1 + 32), chars_basto[:int(f1 * len(chars_basto))], fill=ACCENT_BLUE, font=font_mono_bold)

        draw.text((modal_x1 + 520, fy1), "TERKAIT PROJECT ID", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 520, fy1 + 22), (modal_x2 - 40, fy1 + 68)], radius=8, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 535, fy1 + 32), "PRJ-2026-PGN-001 (Operasi Gas GMS)", fill=TEXT_DARK, font=font_bold)

        fy2 = fy1 + 95
        draw.text((modal_x1 + 40, fy2), "NILAI BASTO DIAJUKAN *", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy2 + 22), (modal_x1 + 480, fy2 + 68)], radius=8, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 55, fy2 + 32), "Rp 820.000.000", fill=TEXT_DARK, font=font_mono_bold)

        draw.text((modal_x1 + 520, fy2), "SERVICE MANAGER PENANGGUNG JAWAB", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 520, fy2 + 22), (modal_x2 - 40, fy2 + 68)], radius=8, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 535, fy2 + 32), "GILANG (OSM Service Manager)", fill=TEXT_DARK, font=font_bold)

        # Upload Zone
        uz_y = fy2 + 95
        draw.text((modal_x1 + 40, uz_y), "LAMPIRAN BERKAS FISIK PDF (STEMPEL BASAH) *", fill=TEXT_SECONDARY, font=font_badge)

        has_file = (i > 45)
        uz_bg = GREEN_BG if has_file else (248, 250, 252)
        uz_border = GREEN if has_file else BORDER_COLOR

        draw.rounded_rectangle([(modal_x1 + 40, uz_y + 22), (modal_x2 - 40, uz_y + 140)], radius=12, fill=uz_bg, outline=uz_border, width=2)
        if has_file:
            draw.text((modal_x1 + 400, uz_y + 55), "✓ BASTO_Fisik_Signed.pdf (3.4 MB)", fill=GREEN, font=font_heading)
            draw.text((modal_x1 + 420, uz_y + 85), "Dokumen stempel basah siap diajukan ke QC Inspector", fill=TEXT_SECONDARY, font=font_small)
        else:
            draw.text((modal_x1 + 360, uz_y + 65), "Menunggu lampiran berkas fisik PDF...", fill=TEXT_MUTED, font=font_body)

        # Submit Button
        btn_y = modal_y2 - 80
        is_sub = (i > 80)
        draw.rounded_rectangle([(modal_x2 - 320, btn_y), (modal_x2 - 40, btn_y + 48)], radius=8, fill=GREEN if is_sub else PGN_BLUE)
        draw.text((modal_x2 - 290, btn_y + 14), "Terkirim ke Inbox QC" if is_sub else "Ajukan Dokumen ke QC", fill=WHITE, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_6(writer):
    # Scene 6: QC Pass & Mutu (OSM QC)
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            6, 8, "QUALITY CONTROL", GREEN, "QC Inspector", "OSM QUALITY CONTROL",
            "Home > BASTO > Verifikasi Mutu Lapangan",
            "Verifikasi Mutu & Inspeksi Fisik Lapangan",
            1, # BASTO active
            "Babak 6: Verifikasi Mutu & QC Pass Bertingkat",
            "QC Inspector memvalidasi 4 butir checklist fisik & administrasi sebelum menerbitkan QC-PASS."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 30
        modal_w = 1100
        modal_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - modal_w) // 2
        modal_y1 = content_top
        modal_x2 = modal_x1 + modal_w
        modal_y2 = modal_y1 + 680

        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y2)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=2)
        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y1 + 65)], radius=14, fill=(240, 253, 244))
        draw.line([(modal_x1, modal_y1 + 65), (modal_x2, modal_y1 + 65)], fill=(220, 252, 231), width=1)
        draw.text((modal_x1 + 30, modal_y1 + 18), "Lembar Pemeriksaan Mutu & Checklist QC Lapangan", fill=TEXT_DARK, font=font_heading)
        draw.text((modal_x1 + 30, modal_y1 + 42), "Dokumen: 004/BASTO/SMO/2026 | Nilai: Rp 820.000.000 | Vendor: PT Mitra Gas Fabrikasi", fill=TEXT_MUTED, font=font_small)

        # 4 Checklist Items
        check_items = [
            "1. Fisik berkas BASTO lengkap, bertanda tangan basah & cap asli rekanan vendor.",
            "2. Berita Acara Pemeriksaan Pekerjaan Lapangan (BAPP) sesuai spesifikasi teknis.",
            "3. Kesesuaian nominal BASTO terhadap serapan SPK dan invoice (Rp 820.000.000).",
            "4. Foto dokumentasi pekerjaan fisik lapangan terlampir valid 100%."
        ]

        cy = modal_y1 + 95
        for idx, itm in enumerate(check_items):
            is_checked = (i > (15 + idx * 20))
            bg = GREEN_BG if is_checked else WHITE
            border = GREEN if is_checked else BORDER_COLOR

            draw.rounded_rectangle([(modal_x1 + 40, cy), (modal_x2 - 40, cy + 60)], radius=8, fill=bg, outline=border, width=1)
            chk_icon = "[✓]" if is_checked else "[  ]"
            draw.text((modal_x1 + 60, cy + 18), chk_icon, fill=GREEN if is_checked else TEXT_MUTED, font=font_bold)
            draw.text((modal_x1 + 110, cy + 18), itm, fill=TEXT_DARK, font=font_body)
            cy += 75

        # QC Code Badge
        all_checked = (i > 80)
        if all_checked:
            draw.rounded_rectangle([(modal_x1 + 40, cy + 10), (modal_x1 + 420, cy + 60)], radius=8, fill=GREEN_BG, outline=GREEN, width=1)
            draw.text((modal_x1 + 60, cy + 22), "QC PASSED • KODE: QC-PASS-99214", fill=GREEN, font=font_mono_bold)

        # Button
        btn_y = modal_y2 - 80
        draw.rounded_rectangle([(modal_x2 - 280, btn_y), (modal_x2 - 40, btn_y + 48)], radius=8, fill=GREEN if all_checked else (148, 163, 184))
        draw.text((modal_x2 - 250, btn_y + 14), "Terbitkan QC Pass" if not all_checked else "QC Pass Diterbitkan ✓", fill=WHITE, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_7(writer):
    # Scene 7: Service Manager Approval (GILANG)
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            7, 8, "SERVICE MANAGER", GREEN, "GILANG", "OSM SERVICE MANAGER",
            "Home > BASTO > Review & Otorisasi Service Manager",
            "Persetujuan Akhir & Otorisasi Service Manager",
            1, # BASTO active
            "Babak 7: Otorisasi & Persetujuan Final BASTO",
            "Service Manager mereview kelengkapan audit dan memberikan otorisasi legal atas pekerjaan."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 30
        modal_w = 1100
        modal_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - modal_w) // 2
        modal_y1 = content_top
        modal_x2 = modal_x1 + modal_w
        modal_y2 = modal_y1 + 680

        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y2)], radius=14, fill=WHITE, outline=BORDER_COLOR, width=2)
        draw.rounded_rectangle([(modal_x1, modal_y1), (modal_x2, modal_y1 + 65)], radius=14, fill=(248, 250, 252))
        draw.line([(modal_x1, modal_y1 + 65), (modal_x2, modal_y1 + 65)], fill=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 30, modal_y1 + 18), "Persetujuan Resmi Berita Acara (BASTO)", fill=TEXT_DARK, font=font_heading)
        draw.text((modal_x1 + 30, modal_y1 + 42), "Otorisasi final oleh Service Manager: GILANG", fill=TEXT_MUTED, font=font_small)

        # QC Status Banner Inside
        draw.rounded_rectangle([(modal_x1 + 40, modal_y1 + 90), (modal_x2 - 40, modal_y1 + 160)], radius=10, fill=GREEN_BG, outline=GREEN, width=1)
        draw.text((modal_x1 + 60, modal_y1 + 105), "✓ Dokumen Telah Memenuhi Syarat Mutu 100% (QC-PASS-99214)", fill=GREEN, font=font_bold)
        draw.text((modal_x1 + 60, modal_y1 + 130), "Berkas sah untuk disetujui Service Manager dan diproses pembayaran.", fill=TEXT_SECONDARY, font=font_small)

        # Summary Fields
        fy1 = modal_y1 + 180
        draw.rounded_rectangle([(modal_x1 + 40, fy1), (modal_x1 + 480, fy1 + 80)], radius=8, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 60, fy1 + 15), "NOMOR BASTO", fill=TEXT_MUTED, font=font_badge)
        draw.text((modal_x1 + 60, fy1 + 40), "004/BASTO/SMO/2026", fill=TEXT_DARK, font=font_bold)

        draw.rounded_rectangle([(modal_x1 + 520, fy1), (modal_x2 - 40, fy1 + 80)], radius=8, fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 540, fy1 + 15), "NILAI YANG DISETUJUI", fill=TEXT_MUTED, font=font_badge)
        draw.text((modal_x1 + 540, fy1 + 40), "Rp 820.000.000", fill=ACCENT_BLUE, font=font_mono_bold)

        # Notes
        fy2 = fy1 + 100
        draw.text((modal_x1 + 40, fy2), "CATATAN OTORISASI SERVICE MANAGER", fill=TEXT_SECONDARY, font=font_badge)
        draw.rounded_rectangle([(modal_x1 + 40, fy2 + 22), (modal_x2 - 40, fy2 + 75)], radius=8, fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((modal_x1 + 60, fy2 + 38), "Pekerjaan selesai 100% sesuai SLA kontrak. Disetujui untuk proses pembayaran.", fill=TEXT_DARK, font=font_body)

        # Approve Button
        btn_y = modal_y2 - 80
        is_approved = (i > 60)
        draw.rounded_rectangle([(modal_x2 - 340, btn_y), (modal_x2 - 40, btn_y + 48)], radius=8, fill=(5, 150, 105) if is_approved else GREEN)
        draw.text((modal_x2 - 310, btn_y + 14), "✓ APPROVED & LOCKED (SAH)" if is_approved else "Setujui BASTO (Approve Final)", fill=WHITE, font=font_bold)

        writer.append_data(np.array(img))

def render_scene_8(writer):
    # Scene 8: Authentic Print Sheet Layout
    total_frames = FPS * 5
    for i in range(total_frames):
        img, draw = draw_real_app_shell(
            8, 8, "MANAJEMEN & AUDIT", (71, 85, 105), "Masyita Mustika", "ADMINISTRATOR & REPORTING",
            "Home > Laporan > Cetak Laporan Sah",
            "Lembar Cetak Laporan Resmi Perusahaan",
            2, # Cost Kontrak active
            "Babak 8: Cetak Laporan Sah dengan 3 Kolom Tanda Tangan",
            "Format A4 Landscape sah lengkap dengan Kop PGN Solution, Rekapitulasi, dan 3 Tanda Tangan."
        )

        content_top = TOPBAR_HEIGHT + BANNER_HEIGHT + 25

        # Print Sheet Card
        sheet_w = 1200
        sheet_x1 = SIDEBAR_WIDTH + (WIDTH - SIDEBAR_WIDTH - sheet_w) // 2
        sheet_y1 = content_top
        sheet_x2 = sheet_x1 + sheet_w
        sheet_y2 = sheet_y1 + 720

        draw.rounded_rectangle([(sheet_x1, sheet_y1), (sheet_x2, sheet_y2)], radius=8, fill=WHITE, outline=(203, 213, 225), width=2)

        # Kop Surat PGNCOM
        if img_pgncom_color is not None:
            kw = 190
            kh = int(kw * (img_pgncom_color.height / img_pgncom_color.width))
            k_res = img_pgncom_color.resize((kw, kh), Image.Resampling.LANCZOS)
            img.paste(k_res, (sheet_x1 + 30, sheet_y1 + 22), k_res)
            draw.text((sheet_x1 + 240, sheet_y1 + 25), "PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)", fill=TEXT_DARK, font=font_heading)
            draw.text((sheet_x1 + 240, sheet_y1 + 48), "Enterprise Project Management & Financial Cost Control System", fill=TEXT_MUTED, font=font_small)
        else:
            draw.rounded_rectangle([(sheet_x1 + 30, sheet_y1 + 25), (sheet_x1 + 170, sheet_y1 + 65)], radius=6, fill=(0, 90, 156))
            draw.text((sheet_x1 + 45, sheet_y1 + 32), "PGNCOM", fill=WHITE, font=font_bold)
            draw.text((sheet_x1 + 190, sheet_y1 + 25), "PT PGAS TELEKOMUNIKASI NUSANTARA (PGNCOM)", fill=TEXT_DARK, font=font_heading)
            draw.text((sheet_x1 + 190, sheet_y1 + 48), "Enterprise Project Management & Financial Cost Control System", fill=TEXT_MUTED, font=font_small)

        draw.line([(sheet_x1 + 30, sheet_y1 + 80), (sheet_x2 - 30, sheet_y1 + 80)], fill=(0, 90, 156), width=3)

        # Document Title
        draw.text((sheet_x1 + 320, sheet_y1 + 95), "LAPORAN RESMI MONITORING REALISASI & REKAPITULASI BASTO", fill=TEXT_DARK, font=font_heading)
        draw.text((sheet_x1 + 350, sheet_y1 + 120), "Tahun: 2026 | Service Manager: GILANG | Client: PT PGN Tbk | Status: APPROVED", fill=TEXT_SECONDARY, font=font_small)

        # Financial Summary Strip
        fy = sheet_y1 + 150
        draw.rectangle([(sheet_x1 + 30, fy), (sheet_x2 - 30, fy + 55)], fill=(248, 250, 252), outline=BORDER_COLOR, width=1)
        draw.text((sheet_x1 + 50, fy + 18), "PAGU: Rp 2.500.000.000", fill=TEXT_DARK, font=font_bold)
        draw.text((sheet_x1 + 340, fy + 18), "REALISASI: Rp 820.000.000", fill=ACCENT_BLUE, font=font_bold)
        draw.text((sheet_x1 + 660, fy + 18), "SISA: Rp 1.680.000.000", fill=GREEN, font=font_bold)
        draw.text((sheet_x1 + 950, fy + 18), "STATUS: 🟢 SEHAT (32.8%)", fill=GREEN, font=font_bold)

        # Table
        ty = fy + 70
        draw.rectangle([(sheet_x1 + 30, ty), (sheet_x2 - 30, ty + 35)], fill=(0, 90, 156))
        draw.text((sheet_x1 + 40, ty + 8), "NO", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 80, ty + 8), "PROJECT ID", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 240, ty + 8), "NAMA PROYEK", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 540, ty + 8), "NO. BASTO", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 750, ty + 8), "NILAI REALISASI", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 940, ty + 8), "STATUS QC", fill=WHITE, font=font_bold)
        draw.text((sheet_x1 + 1060, ty + 8), "STATUS BASTO", fill=WHITE, font=font_bold)

        # Row 1
        ry = ty + 35
        draw.rectangle([(sheet_x1 + 30, ry), (sheet_x2 - 30, ry + 40)], fill=WHITE, outline=BORDER_COLOR, width=1)
        draw.text((sheet_x1 + 45, ry + 10), "1", fill=TEXT_DARK, font=font_body)
        draw.text((sheet_x1 + 80, ry + 10), "PRJ-2026-PGN-001", fill=TEXT_DARK, font=font_mono_bold)
        draw.text((sheet_x1 + 240, ry + 10), "Operasi & Pemeliharaan Gas GMS", fill=TEXT_DARK, font=font_body)
        draw.text((sheet_x1 + 540, ry + 10), "004/BASTO/SMO/2026", fill=TEXT_DARK, font=font_mono)
        draw.text((sheet_x1 + 750, ry + 10), "Rp 820.000.000", fill=TEXT_DARK, font=font_mono_bold)
        draw.text((sheet_x1 + 940, ry + 10), "✓ QC PASSED", fill=GREEN, font=font_bold)
        draw.text((sheet_x1 + 1060, ry + 10), "APPROVED", fill=(5, 150, 105), font=font_bold)

        # 3-Column Formal Signature Block
        sig_y = ry + 80
        draw.line([(sheet_x1 + 30, sig_y), (sheet_x2 - 30, sig_y)], fill=BORDER_COLOR, width=1)

        cols = [
            ("Disiapkan Oleh (DMO Staff):", "Masyita Mustika", "Data Management Officer", sheet_x1 + 100),
            ("Diperiksa Oleh (QC):", "QC Inspector Lapangan", "OSM Quality Control", sheet_x1 + 500),
            ("Disetujui Oleh (SM):", "GILANG", "OSM Service Manager", sheet_x1 + 900)
        ]

        for title, name, role_desc, x in cols:
            draw.text((x, sig_y + 20), title, fill=TEXT_MUTED, font=font_small)
            draw.text((x, sig_y + 100), name, fill=TEXT_DARK, font=font_bold)
            draw.text((x, sig_y + 120), role_desc, fill=TEXT_MUTED, font=font_small)

        # Official Stamp on SM
        draw.rounded_rectangle([(sheet_x1 + 950, sig_y + 40), (sheet_x1 + 1120, sig_y + 85)], radius=6, outline=(5, 150, 105), width=2)
        draw.text((sheet_x1 + 965, sig_y + 52), "APPROVED & SAH", fill=(5, 150, 105), font=font_bold)

        writer.append_data(np.array(img))

def main():
    print(f"Rendering Real Website Video to {OUTPUT_FILE} (Resolution: {WIDTH}x{HEIGHT}, FPS: {FPS})...")
    writer = imageio.get_writer(
        OUTPUT_FILE,
        fps=FPS,
        codec='libx264',
        pixelformat='yuv420p',
        quality=8
    )

    scenes = [
        ("Scene 1: Master Data Proyek (DMO Staff)", render_scene_1),
        ("Scene 2: Procurement PO & Prognosa (Procurement)", render_scene_2),
        ("Scene 3: Realisasi Tagihan & Aging Invoice (DMO/SCM)", render_scene_3),
        ("Scene 4: Monitoring Cost Kontrak (Cost Control)", render_scene_4),
        ("Scene 5: Monitoring BASTO & Upload PDF (DMO Staff)", render_scene_5),
        ("Scene 6: Inspeksi Mutu & QC Pass (OSM QC)", render_scene_6),
        ("Scene 7: Persetujuan Akhir Service Manager (GILANG)", render_scene_7),
        ("Scene 8: Cetak Laporan Sah 3 Tanda Tangan (Manajemen)", render_scene_8),
    ]

    for title, func in scenes:
        print(f"  Rendering {title}...")
        func(writer)

    writer.close()
    file_size_mb = os.path.getsize(OUTPUT_FILE) / (1024 * 1024)
    print(f"Render Selesai! Video tersimpan: {OUTPUT_FILE} ({file_size_mb:.2f} MB)")

if __name__ == "__main__":
    main()
