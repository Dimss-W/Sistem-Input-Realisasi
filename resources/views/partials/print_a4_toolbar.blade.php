{{-- Toolbar Pengendali Cetak & Simpan Dokumen Resmi A4 --}}
<div class="print-a4-toolbar d-print-none">
    <div class="container-fluid px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 py-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>STANDAR A4
            </span>
            <div class="d-flex flex-column">
                <span class="fw-bold text-white small lh-1">Pratinjau Dokumen Cetak Resmi PGNCOM</span>
                <span class="text-white-50 small" style="font-size: 0.68rem;">
                    Format: A4 {{ strtoupper($orientation ?? 'landscape') }} ({{ ($orientation ?? 'landscape') === 'portrait' ? '210 x 297 mm' : '297 x 210 mm' }}) &bull; Siap Dicetak atau Disimpan
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Tombol 1: Cetak Langsung / Dialog Bawaan Browser --}}
            <button type="button" onclick="window.print()" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 shadow-sm" title="Buka dialog cetak browser (pilih printer fisik atau Save as PDF)">
                <i class="bi bi-printer-fill me-1.5"></i> Cetak Dokumen
            </button>

            {{-- Tombol 2: Simpan PDF Langsung ke Penyimpanan Komputer --}}
            <button type="button" id="btnSavePdfStorage" onclick="saveA4ReportToStorage('{{ $targetId ?? 'printableReportArea' }}', '{{ $filename ?? 'Laporan_Resmi_A4_' . date('Ymd_His') }}', '{{ $orientation ?? 'landscape' }}')" class="btn btn-sm btn-success fw-bold px-3 py-1.5 shadow-sm" title="Unduh dan simpan berkas PDF langsung ke folder penyimpanan/Downloads komputer Anda">
                <i class="bi bi-download me-1.5"></i> Simpan PDF ke Penyimpanan
            </button>

            {{-- Tombol 3: Tutup Tab Pratinjau --}}
            <button type="button" onclick="closeOrBackPrintPreview()" class="btn btn-sm btn-outline-light px-2.5 py-1.5" title="Tutup pratinjau dan kembali ke halaman sistem">
                <i class="bi bi-x-lg me-1"></i> Tutup
            </button>
        </div>
    </div>
</div>

<style>
.print-a4-toolbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10050;
    background: #0f172a;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    border-bottom: 1px solid #1e293b;
}

@media print {
    .print-a4-toolbar,
    .print-a4-toolbar * {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>

<script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>
<script>
function closeOrBackPrintPreview() {
    if (window.opener || window.history.length <= 1) {
        window.close();
    } else {
        // Hapus parameter print=1 dari URL agar kembali ke tabel biasa
        const url = new URL(window.location.href);
        url.searchParams.delete('print');
        window.location.href = url.toString();
    }
}

function saveA4ReportToStorage(targetId, filename, orientation = 'landscape') {
    const el = document.getElementById(targetId);
    if (!el) {
        alert('Area dokumen tidak ditemukan.');
        return;
    }

    const btn = document.getElementById('btnSavePdfStorage');
    let originalHtml = '';
    if (btn) {
        originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span>Memproses PDF A4...';
    }

    // Opsi konfigurasi Standar A4 untuk html2pdf
    const cleanFilename = (filename || 'Laporan_A4').replace(/[^a-zA-Z0-9_-]/g, '_') + '.pdf';
    
    const opt = {
        margin: [8, 8, 8, 8],
        filename: cleanFilename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false,
            letterRendering: true,
            windowWidth: orientation === 'portrait' ? 1100 : 1400
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: orientation 
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    html2pdf().set(opt).from(el).save().then(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1.5 text-white"></i>Tersimpan!';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 2500);
        }
    }).catch(function(err) {
        console.error('Gagal generate PDF via html2pdf:', err);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
        alert('Mengalihkan ke dialog cetak browser untuk menyimpan sebagai PDF...');
        window.print();
    });
}
</script>
