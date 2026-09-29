# LAPORAN PRAKTIK KERJA LAPANGAN (PKL)
## PROYEK INOVASI SISTEM INFORMASI (CAPSTONE PROJECT)

---

### JUDUL:
**RANCANG BANGUN SISTEM INFORMASI MONITORING REALISASI ANGGARAN PROYEK DAN DOKUMEN BASTO TERINTEGRASI POWER BI PADA PT PGAS TELEKOMUNIKASI NUSANTARA**

---

## DAFTAR ISI STRUKTUR LAPORAN

- Lembar Judul Laporan PKL
- Lembar Persetujuan Laporan PKL
- Kata Pengantar
- Daftar Isi
- Daftar Simbol
- Daftar Gambar
- Daftar Tabel
- Daftar Lampiran
- **BAB I PROJECT CHARTER**
  - 1.1. Latar Belakang
  - 1.2. Permasalahan
  - 1.3. Tujuan dan Manfaat Aplikasi
  - 1.4. Deskripsi Produk
  - 1.5. Keuntungan Yang Diharapkan
  - 1.6. Perencanaan Aktivitas Secara Global
- **BAB II PROJECT REPORT**
  - 2.1. Metode Pengembangan Perangkat Lunak
  - 2.2. Tahap Pengembangan Perangkat Lunak
    - 2.2.1. Analisis Kebutuhan Sistem
    - 2.2.2. Perancangan Sistem (UML, Basis Data & Antarmuka)
    - 2.2.3. Pengkodean Sistem (Konstruksi Perangkat Lunak)
    - 2.2.4. Pengujian Sistem (Black Box Testing)
- **BAB III PENUTUP**
  - 3.1. Kesimpulan
  - 3.2. Saran
- **DAFTAR PUSTAKA**
- **DAFTAR RIWAYAT HIDUP**
- **LAMPIRAN-LAMPIRAN**
  - Surat Keterangan PKL
  - Lembar Nilai PKL
  - Surat Tanda Terima untuk Aplikasi yang Diimplementasikan
  - Dokumentasi Presentasi Aplikasi
  - Dokumentasi Serah Terima Aplikasi
  - Dokumen Pendukung Lainnya

---

# LEMBAR JUDUL LAPORAN PKL
*(Format Halaman Judul Luar & Dalam)*

**LAPORAN PRAKTIK KERJA LAPANGAN (PKL)**  
**RANCANG BANGUN SISTEM INFORMASI MONITORING REALISASI ANGGARAN PROYEK DAN DOKUMEN BASTO TERINTEGRASI POWER BI PADA PT PGAS TELEKOMUNIKASI NUSANTARA**  

Diajukan untuk memenuhi mata kuliah Praktik Kerja Lapangan (PKL) / Capstone Project  
Program Studi Sistem Informasi / Teknik Informatika  

**Disusun Oleh:**  
**Nama : Dimas Wijanarko**  
**NIM  : [Isi NIM Anda]**  

**PROGRAM STUDI [SISTEM INFORMASI / REKAYASA PERANGKAT LUNAK]**  
**FAKULTAS TEKNOLOGI INFORMASI**  
**[NAMA UNIVERSITAS, MISAL: UNIVERSITAS BINA SARANA INFORMATIKA / NUSA MANDIRI]**  
**JAKARTA**  
**2026**  

---

# LEMBAR PERSETUJUAN LAPORAN PKL

Laporan Praktik Kerja Lapangan (PKL) dengan judul:  
**"RANCANG BANGUN SISTEM INFORMASI MONITORING REALISASI ANGGARAN PROYEK DAN DOKUMEN BASTO TERINTEGRASI POWER BI PADA PT PGAS TELEKOMUNIKASI NUSANTARA"**  

Telah diperiksa dan disetujui pada tanggal: ............................ 2026  

Menyetujui,  

**Dosen Penasihat Akademik / Pembimbing PKL**  
  
  
*(...............................................................)*  
NIP/NIDN: ....................................................  

Mengetahui,  
**Ketua Program Studi [Nama Program Studi]**  
  
  
*(...............................................................)*  
NIP/NIDN: ....................................................  

---

# KATA PENGANTAR

Puji dan syukur penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas berkat, rahmat, dan karunia-Nya, sehingga penulis dapat menyelesaikan kegiatan Praktik Kerja Lapangan (PKL) serta menyelesaikan laporan yang berjudul:

**"RANCANG BANGUN SISTEM INFORMASI MONITORING REALISASI ANGGARAN PROYEK DAN DOKUMEN BASTO TERINTEGRASI POWER BI PADA PT PGAS TELEKOMUNIKASI NUSANTARA"**

Laporan ini disusun guna memenuhi salah satu syarat kelulusan mata kuliah Praktik Kerja Lapangan (PKL) berbasis *Capstone Project* pada Program Studi [Nama Program Studi], [Nama Universitas].

Dalam pelaksanaan PKL hingga penyusunan laporan ini, penulis telah menerima banyak bimbingan, arahan, serta dukungan dari berbagai pihak. Oleh karena itu, penulis menyampaikan ucapan terima kasih yang sebesar-besarnya kepada:

1. Rektor, Dekan, dan segenap civitas akademika [Nama Universitas].
2. Ketua Program Studi beserta seluruh dosen dan staf akademik yang telah membekali ilmu pengetahuan selama masa perkuliahan.
3. Dosen Pembimbing PKL yang telah meluangkan waktu memberikan bimbingan, petunjuk, dan saran yang bermanfaat.
4. Pimpinan dan Pembimbing Lapangan di PT PGAS Telekomunikasi Nusantara (PGN COM) yang telah memberikan kesempatan magang, fasilitas, arahan, serta pengalaman kerja yang berharga.
5. Seluruh staf dan rekan kerja di PT PGAS Telekomunikasi Nusantara yang telah membantu dalam pengumpulan data dan pengujian sistem.
6. Orang tua dan keluarga tercinta atas limpahan doa, motivasi, dan dukungan yang tiada henti.
7. Rekan-rekan mahasiswa serta seluruh pihak yang telah membantu dan bertukar pikiran selama masa magang.

Penulis menyadari bahwa penulisan laporan ini masih memiliki kekurangan. Oleh karena itu, kritik dan saran yang membangun sangat diharapkan demi penyempurnaan di masa mendatang. Semoga laporan ini dapat memberikan manfaat bagi pembaca, instansi perusahaan, dan pengembangan ilmu pengetahuan.

<br>

Jakarta, September 2026  
Penulis,  

<br><br>

**Dimas Wijanarko**  
NIM. [Isi NIM Anda]  

---

# DAFTAR SIMBOL

### A. Simbol Use Case Diagram
| Simbol | Nama Simbol | Deskripsi |
| :---: | :--- | :--- |
| ![Actor](https://via.placeholder.com/40x50?text=Actor) | **Actor** | Menggambarkan seseorang atau sistem eksternal yang berinteraksi secara langsung dengan sistem perangkat lunak. |
| ![Use Case](https://via.placeholder.com/60x35?text=UseCase) | **Use Case** | Menggambarkan fungsionalitas, layanan, atau proses yang disediakan oleh sistem kepada aktor. |
| ![Association](https://via.placeholder.com/60x10?text=Assoc) | **Association** | Menghubungkan antara aktor dengan use case yang dapat diaksesnya. |
| ![Include](https://via.placeholder.com/60x15?text=%3C%3Cinclude%3E%3E) | **Include** | Menunjukkan relasi keharusan pemanggilan fungsionalitas use case lain saat suatu use case dijalankan. |
| ![Extend](https://via.placeholder.com/60x15?text=%3C%3Cextend%3E%3E) | **Extend** | Menunjukkan fungsionalitas tambahan yang dieksekusi berdasarkan kondisi tertentu. |
| ![System Boundary](https://via.placeholder.com/50x40?text=Boundary) | **System Boundary** | Membatasi ruang lingkup fungsionalitas sistem dengan dunia luar (lingkungan eksternal). |

### B. Simbol Activity Diagram
| Simbol | Nama Simbol | Deskripsi |
| :---: | :--- | :--- |
| ● | **Initial Node** | Menandakan titik awal dimulainya suatu aliran aktivitas pada proses bisnis. |
| ⬭ | **Action / Activity** | Menandakan eksekusi pekerjaan, langkah operasional, atau proses komputasi yang sedang berlangsung. |
| ◇ | **Decision Node** | Menunjukkan titik percabangan kondisi logika dengan lebih dari satu kemungkinan arah alur. |
| ▬ | **Fork / Join Node** | Digunakan untuk membagi satu aliran menjadi beberapa proses paralel (Fork) atau menggabungkan kembali (Join). |
| ◉ | **Activity Final Node**| Menandakan bahwa seluruh rangkaian aliran kerja dalam diagram aktivitas telah berakhir. |
| ▋ | **Swimlane** | Mengelompokkan aktivitas berdasarkan pembagian peran, unit kerja, atau aktor penanggung jawab. |

---

# DAFTAR GAMBAR
- Gambar 1.1 Struktur Siklus Alur Kerja Proyek PGN COM
- Gambar 2.1 Alur Metode Waterfall (Pressman)
- Gambar 2.2 Use Case Diagram Modul Pengawasan, Akun & Hak Akses (Admin)
- Gambar 2.3 Use Case Diagram Peran Delivery Management Office (DMO)
- Gambar 2.4 Use Case Diagram Peran OSM Service Manager
- Gambar 2.5 Use Case Diagram Peran Quality Control (OSM QC)
- Gambar 2.6 Use Case Diagram Peran Procurement & Finance
- Gambar 2.7 Activity Diagram Workflow Verifikasi & Approval BASTO
- Gambar 2.8 Activity Diagram Proses Import Realisasi dari Spreadsheet Excel
- Gambar 2.9 Entity Relationship Diagram (ERD) Sistem Monitoring Realisasi
- Gambar 2.10 Antarmuka Halaman Login Multi-Role
- Gambar 2.11 Antarmuka Dashboard Eksekutif Monitoring Realisasi & Pagu
- Gambar 2.12 Antarmuka Modul Pilar 1: Monitoring Dokumen BASTO
- Gambar 2.13 Antarmuka Modul Pilar 2: Cost Kontrak vs Realisasi
- Gambar 2.14 Antarmuka Modul Pilar 3: Monitoring Invoice Vendor
- Gambar 2.15 Antarmuka Command Center Kiosk Standby Dashboard (TV Display)

---

# DAFTAR TABEL
- Tabel 1.1 Analisis Masalah dan Solusi Terapan
- Tabel 1.2 Estimasi Keuntungan Berwujud (Tangible Benefits)
- Tabel 1.3 Matriks Perencanaan Aktivitas Global (WBS & Jadwal Pelaksanaan)
- Tabel 2.1 Matriks Kebutuhan Fungsional Berdasarkan Hak Akses Pengguna
- Tabel 2.2 Skenario Use Case Login Pengguna
- Tabel 2.3 Skenario Use Case Import Realisasi Anggaran dari Dokumen Excel
- Tabel 2.4 Skenario Use Case Verifikasi Dokumen BASTO oleh OSM QC
- Tabel 2.5 Skenario Use Case Approval Dokumen BASTO oleh OSM Service Manager
- Tabel 2.6 Skenario Use Case Period Locking (Kunci Transaksi Bulanan)
- Tabel 2.7 Kamus Data dan Struktur Tabel Basis Data Relasional
- Tabel 2.8 Rencana Pengujian Black Box Testing
- Tabel 2.9 Hasil Pengujian Black Box Modul Autentikasi dan Otorisasi (RBAC)
- Tabel 2.10 Hasil Pengujian Black Box Modul Engine Import Data Excel
- Tabel 2.11 Hasil Pengujian Black Box Modul Workflow BASTO Bertingkat
- Tabel 2.12 Hasil Pengujian Black Box Modul Tiga Pilar Monitoring & Kiosk Dashboard

---

# DAFTAR LAMPIRAN
- Lampiran 1 : Surat Keterangan Selesai Praktik Kerja Lapangan
- Lampiran 2 : Lembar Penilaian Kinerja Mahasiswa PKL dari Perusahaan
- Lampiran 3 : Surat Tanda Terima untuk Aplikasi yang Diimplementasikan
- Lampiran 4 : Berita Acara Serah Terima Aplikasi Perangkat Lunak
- Lampiran 5 : Dokumentasi Presentasi dan Sosialisasi Aplikasi di Tempat PKL
- Lampiran 6 : Foto Dokumentasi Implementasi dan Serah Terima Sistem
- Lampiran 7 : Struktur Template File Import Excel Realisasi Proyek

---

# BAB I
# PROJECT CHARTER

## 1.1. Latar Belakang

PT PGAS Telekomunikasi Nusantara (PGN COM) merupakan anak perusahaan dari PT Perusahaan Gas Negara Tbk (PGN) yang bergerak di bidang penyediaan infrastruktur telekomunikasi, layanan jaringan serat optik (*fiber optic*), solusi teknologi informasi, serta transmisi data berkeandalan tinggi bagi industri energi, korporasi swasta, dan institusi pemerintahan. Dalam menjalankan kegiatan operasional bisnisnya, PGN COM mengelola portofolio proyek multi-tahunan (*multi-years project*) dengan skala investasi bernilai miliaran rupiah yang melibatkan banyak pihak, meliputi penyedia jasa (*vendor/subcontractor*), mitra klien strategis, manajer layanan operasional (*Service Manager*), divisi *Delivery Management Office* (DMO), *Quality Control* (QC), hingga unit *Procurement*.

Pada era transformasi digital saat ini, kecepatan, ketepatan, dan keandalan rekonsiliasi data operasional serta realisasi finansial menjadi penentu profitabilitas dan reputasi korporasi. Namun, pada proses bisnis eksisting di lapangan, manajemen realisasi proyek menghadapi kendala signifikan akibat pengelolaan data yang masih bersifat terfragmentasi. Pencatatan biaya riil (*actual cost*), pengalokasian pagu anggaran (*cost based*), dan estimasi pengeluaran ke depan (*prognosa*) sebagian besar masih dilakukan menggunakan aplikasi lembar kerja (*spreadsheet/Excel*) secara terpisah di masing-masing komputer *Service Manager* dan staf DMO.

Fragmentasi lembar kerja tersebut menimbulkan berbagai masalah kritikal, antara lain: tingginya potensi selisih pencatatan (*discrepancy*), sulitnya konsolidasi data laporan bulanan, risiko ketidaksinkronan data antara unit operasi dengan bagian pengadaan (*Procurement*), serta kerentanan integritas data akibat ketiadaan mekanisme penguncian periode (*period locking*) pasca penutupan buku transaksi bulanan. Selain itu, alur pengesahan berkas Berita Acara Serah Terima Operasional (BASTO) yang menjadi prasyarat mutlak penagihan termin proyek seringkali memakan waktu lama karena masih bergantung pada pertukaran berkas manual tanpa adanya notifikasi pelacakan status (*realtime tracking status*) yang transparan bagi divisi QC dan Service Manager. Di sisi lain, ketiadaan integrasi data otomatis ke platform analitik modern seperti Microsoft Power BI menyebabkan tim operasional harus mengolah ulang lembar kerja berkali-kali untuk menyajikan laporan visual kepada jajaran manajemen eksekutif.

Kondisi tersebut menuntut adanya sebuah inovasi sistem informasi terpusat berbasis web yang mampu mengintegrasikan seluruh tahapan pemantauan proyek ke dalam satu platform tunggal yang mendukung sistem penunjang keputusan. Berdasarkan pertimbangan tersebut, penulis melaksanakan proyek inovasi sistem informasi (*Capstone Project*) dengan merancang dan mengimplementasikan **"Sistem Informasi Monitoring Realisasi Anggaran Proyek dan Dokumen BASTO Terintegrasi Power BI pada PT PGAS Telekomunikasi Nusantara"**. Sistem ini dibangun dengan kerangka kerja modern berbasis Laravel, menerapkan kontrol akses berbasis peran (*Role-Based Access Control*), mesin integrasi data Excel otomatis, tiga pilar monitoring komprehensif, modul pemantau layar dinamis (*Command Center Kiosk Display*), serta kemampuan integrasi data ke platform Microsoft Power BI guna mendukung pengambilan keputusan manajemen secara cepat, terukur, dan akurat.

---

## 1.2. Permasalahan

Berdasarkan analisis observasi lapangan dan wawancara dengan pemangku kepentingan (*stakeholders*) di PT PGAS Telekomunikasi Nusantara, permasalahan pokok yang dihadapi dirumuskan sebagai berikut:

1. **Fragmentasi dan Redundansi Data Realisasi Proyek**: Pencatatan data pagu kontrak (*contract value*), anggaran dasar (*cost based*), realisasi pengeluaran riil, dan angka prognosa bulanan masih dikelola secara mandiri oleh masing-masing unit menggunakan file Excel lokal, sehingga rentan terjadi inkonsistensi versi file dan duplikasi entri data antar unit.
2. **Keterlambatan dan Inefisiensi Alur Pengesahan Dokumen BASTO**: Proses pengajuan, pengecekan mutu oleh Quality Control (QC), hingga penandatanganan/persetujuan oleh Service Manager belum terintegrasi secara digital dalam satu sistem *workflow*. Hal ini mengakibatkan dokumen sering tertahan tanpa visibilitas kendala, yang secara langsung menunda penerbitan penagihan (*invoice*) ke pelanggan.
3. **Ketidaksinkronan Tagihan Vendor dengan Anggaran Riil**: Sulitnya melakukan rekonsiliasi silang (*cross-reconciliation*) antara tagihan masuk dari vendor rekanan (*subcontractor*) dengan prognosa biaya yang dialokasikan dalam kontrak proyek, memicu risiko pembengkakan biaya proyek (*cost overrun*).
4. **Ketiadaan Mekanisme Penguncian Periode Akuntansi Proyek**: Tidak tersedianya fitur proteksi transaksi berbasis periode (*period locking*) mengakibatkan data transaksi bulan-bulan yang telah diaudit (*closing period*) masih rentan diubah atau terhapus secara tidak sengaja oleh pengguna.
5. **Keterbatasan Visualisasi Eksekutif dan Penunjang Keputusan**: Belum tersedianya jalur integrasi data terpusat ke platform *Business Intelligence* (seperti Microsoft Power BI) serta ketiadaan sarana pemantau kinerja proyek pada layar ruang kendali (*Command Center Display*), sehingga manajemen kesulitan melakukan analisis tren serapan anggaran dan evaluasi portofolio proyek secara interaktif.

---

## 1.3. Tujuan dan Manfaat Aplikasi

### 1.3.1. Tujuan Aplikasi
Tujuan yang ingin dicapai melalui perancangan dan implementasi sistem informasi ini adalah:
1. Menghasilkan sistem informasi terintegrasi berbasis web yang memusatkan seluruh data kontrak, realisasi biaya, dan prognosa proyek PT PGAS Telekomunikasi Nusantara ke dalam basis data relasional yang aman dan terstruktur.
2. Membangun modul alur kerja (*workflow engine*) BASTO bertingkat yang menghubungkan staf DMO, pengawas QC, dan Service Manager secara digital dilengkapi fitur pelacakan riwayat dan *batch approval*.
3. Mengembangkan modul monitoring "Tiga Pilar Proyek" (Monitoring BASTO, Monitoring Cost Kontrak, dan Monitoring Invoice Vendor) yang menyajikan perbandingan komparatif antara pagu, realisasi aktual, dan selisih margin keuntungan secara otomatis.
4. Menerapkan mekanisme penguncian periode (*Period Locking*) guna menjamin kepatuhan audit internal dan mencegah modifikasi data histori pasca penutupan buku.
5. Menyediakan fitur *Kiosk Standby Dashboard* untuk monitor ruang kendali serta konektivitas basis data terstruktur ke platform Microsoft Power BI sebagai Sistem Penunjang Keputusan (SPK) manajerial.

### 1.3.2. Manfaat Aplikasi

#### A. Manfaat bagi PT PGAS Telekomunikasi Nusantara (PGN COM):
- **Efisiensi Operasional**: Memangkas waktu konsolidasi laporan bulanan dari yang semula membutuhkan 3–5 hari kerja menjadi hitungan detik melalui mesin kalkulasi otomatis.
- **Transparansi dan Akuntabilitas**: Setiap perubahan data riil, unggahan revisi BASTO, maupun persetujuan transaksi terekam secara otomatis ke dalam *Audit Log System*, sehingga meminimalisir perselisihan antar divisi.
- **Optimalisasi Arus Kas (Cash Flow)**: Mempercepat siklus penerbitan penagihan termin proyek kepada klien dengan terselesaikannya validasi BASTO secara tepat waktu.
- **Pengambilan Keputusan Cepat & Akurat**: Membantu jajaran pimpinan melakukan analisis kinerja proyek secara mendalam (*drill-down analysis*) melalui integrasi dashboard Microsoft Power BI.

#### B. Manfaat bagi Mahasiswa:
- Mengasah kompetensi rekayasa perangkat lunak berskala korporasi (*enterprise level*) melalui penerapan arsitektur *Model-View-Controller* (MVC) menggunakan framework Laravel.
- Memahami implementasi *Business Intelligence* (BI) dan integrasi data antara aplikasi transaksional dengan sistem visualisasi analitik eksekutif.
- Memenuhi syarat kelulusan mata kuliah Praktik Kerja Lapangan (PKL) berbasis *Capstone Project* dengan predikat karya terapan bernilai guna tinggi.

#### C. Manfaat bagi Program Studi / Perguruan Tinggi:
- Memperkuat kemitraan strategis antara institusi perguruan tinggi dengan Badan Usaha Milik Negara (BUMN) dan afiliasinya dalam penyaluran talenta digital.
- Menjadi tolok ukur implementasi kurikulum berbasis luaran (*Outcome-Based Education / OBE*) di mana mahasiswa mampu menghasilkan solusi teknologi inovatif yang diimplementasikan langsung di industri.

---

## 1.4. Deskripsi Produk

Aplikasi yang dikembangkan diberi nama **"Enterprise Project Realization & BASTO Monitoring System"** (Sistem Monitoring Realisasi dan BASTO). Sistem ini dibangun menggunakan arsitektur web modern dengan landasan kerangka kerja Laravel 10, MySQL sebagai *Database Management System* (DBMS), serta antarmuka berbasis template Skydash yang responsif.

Sistem memiliki karakteristik fungsional utama sebagai berikut:
1. **Multi-Role Role-Based Access Control (RBAC)**: Sistem membagi hak akses pengguna secara ketat ke dalam 5 tingkatan peran operasional:
   - *Admin*: Memiliki kewenangan penuh dalam konfigurasi akun, pengelolaan audit log, pengaturan periode (*period lock*), sinkronisasi master data, dan fitur *impersonation* untuk supervisi.
   - *DMO (Delivery Management Office)*: Berwenang mendaftarkan proyek baru, mengimpor lembar kerja realisasi Excel, mengajukan berkas BASTO, dan memperbarui status progres.
   - *OSM Service Manager*: Penanggung jawab portofolio proyek yang melakukan peninjauan akhir (*approval/rejection*) BASTO, pembaruan prognosa bulanan, dan evaluasi *executive summary*.
   - *OSM QC (Quality Control)*: Bertanggung jawab melakukan verifikasi teknis fisik berkas BASTO dan kelengkapan lampiran pekerjaan di lapangan.
   - *Procurement*: Mengelola data vendor rekanan, pesanan pembelian (*Purchase Order*), serta verifikasi tagihan dan invoice vendor masuk.
2. **High-Performance Excel Import & Parsing Engine**: Sistem dilengkapi pustaka `PhpSpreadsheet` kustom yang mampu memproses ribuan baris data Excel secara langsung, melakukan pembersihan karakter mata uang (Rp, USD, tanda baca koma/titik), serta mendeteksi pembaruan data proyek lama atau penyisipan baris baru secara cerdas.
3. **Pilar Monitoring Tiga Dimensi**:
   - *Pilar 1 (Monitoring BASTO)*: Pelacakan status berkas: *Draft*, *Submitted*, *QC Verified*, *Approved*, hingga *Rejected* dengan riwayat alasan revisi.
   - *Pilar 2 (Monitoring Cost Kontrak)*: Analisis komparasi nilai kontrak vs *cost based* vs *actual cost* admin vs persentase penyerapan anggaran dengan indikator batas toleransi (*threshold alert*).
   - *Pilar 3 (Monitoring Invoice Vendor)*: Manajemen penagihan vendor, perbandingan prognosa vs aktual tagihan, serta status verifikasi kelengkapan administrasi.
4. **Financial Period Locking System**: Fitur penguncian bulan buku akuntansi tertentu (Januari hingga Desember) yang dapat diaktifkan oleh Admin, sehingga seluruh formulir transaksi pada periode terkunci beralih menjadi mode baca-saja (*read-only*).
5. **Standby Command Center Kiosk Dashboard**: Antarmuka visual tanpa bilah gulir (*zero scroll layout*) dengan palet warna kontras tinggi yang dirancang khusus untuk layar TV operasional di lobi atau ruang rapat eksekutif, dilengkapi *auto-polling API* data statistik.
6. **Integrasi Platform Business Intelligence (Microsoft Power BI)**: Skema basis data relasional dirancang terstruktur dan bersih (*clean database architecture*) sehingga dapat dihubungkan langsung ke Microsoft Power BI untuk menyajikan visualisasi data analitik interaktif, grafik tren serapan anggaran, dan metrik kinerja proyek sebagai instrumen Sistem Penunjang Keputusan (SPK) bagi jajaran manajemen.

---

## 1.5. Keuntungan Yang Diharapkan

## 1.5. Keuntungan Yang Diharapkan

Implementasi sistem informasi ini dirancang untuk memberikan keuntungan ganda bagi PT PGAS Telekomunikasi Nusantara, baik yang bernilai finansial terukur (*tangible*) maupun penguatan tata kelola institusi (*intangible*).

### 1.5.1. Keuntungan Berwujud (*Tangible Benefits*)
Keuntungan terukur disajikan dalam tabel perbandingan kondisi sebelum dan sesudah implementasi aplikasi pada Tabel 1.1 berikut:

#### Tabel 1.1 Analisis Keuntungan Berwujud (*Tangible Benefits*)
| No | Parameter Operasional | Kondisi Eksisting (Sebelum Aplikasi) | Kondisi Target (Setelah Aplikasi) | Estimasi Nilai Manfaat |
| :---: | :--- | :--- | :--- | :--- |
| 1 | Waktu Konsolidasi Laporan Realisasi Bulanan | Membutuhkan waktu 3 s.d 5 hari kerja untuk mengumpulkan dan menyatukan puluhan file Excel. | Selesai secara instan (*instant calculation*) melalui kalkulasi otomatis di database terpusat. | Penghematan waktu kerja tim operasi sebesar ±85%. |
| 2 | Penggunaan Kertas & Berkas Fisik (Paperless) | Pencetakan draf berkas BASTO berulang kali untuk proses paraf dan koreksi bertahap di tim QC & Manager. | Seluruh peninjauan berkas, catatan revisi, dan lampiran dikelola secara digital (*paperless upload*). | Pengurangan biaya cetak dokumen operasional hingga 70%. |
| 3 | Durasi Siklus Serah Terima Dokumen BASTO | Rata-rata berkas fisik tertahan 7–14 hari kerja karena ketiadaan sistem notifikasi antrean terpadu. | Berkas diverifikasi oleh QC dan disetujui Service Manager dalam 1–2 hari kerja secara daring. | Percepatan penerbitan tagihan (*invoice*) ke klien rata-rata 8 hari lebih cepat. |
| 4 | Pencegahan Pembengkakan Biaya (Over-Budget) | Pembengkakan tagihan vendor seringkali baru terdeteksi saat tutup buku tahunan (*cost overrun*). | Sistem memberikan peringatan otomatis jika nilai tagihan vendor mendekati atau melampaui sisa pagu kontrak. | Menghilangkan potensi kerugian akibat pembayaran klaim vendor berlebih. |
| 5 | Efisiensi Penyusunan Visualisasi Data Eksekutif | Pembuatan grafik dan dashboard presentasi membutuhkan waktu 1–2 hari kerja olah data manual. | Data terintegrasi langsung ke Microsoft Power BI, grafik dan KPI terbarukan secara otomatis (*realtime*). | Menghemat waktu persiapan bahan rapat evaluasi manajemen hingga 90%. |

### 1.5.2. Keuntungan Tidak Berwujud (*Intangible Benefits*)
Keuntungan yang dirasakan dari sisi tata kelola, integrasi kerja, dan keandalan data disajikan pada Tabel 1.2 berikut:

#### Tabel 1.2 Analisis Keuntungan Tidak Berwujud (*Intangible Benefits*)
| No | Aspek Tata Kelola | Dampak Sebelum Aplikasi | Dampak Positif Setelah Implementasi Sistem |
| :---: | :--- | :--- | :--- |
| 1 | Integritas & Akurasi Data (*Data Integrity*) | Terjadi duplikasi data dan ketidaksinkronan angka pagu antara divisi DMO, Operasi, dan Procurement. | Tercipta *Single Source of Truth* di mana seluruh unit mengacu pada basis data tunggal yang konsisten. |
| 2 | Kepatuhan Tata Kelola (*Good Corporate Governance*) | Riwayat perubahan data sulit dilacak, memicu kerentanan pada saat proses audit internal korporasi. | Setiap penambahan data, persetujuan BASTO, dan penguncian periode terekam otomatis pada *Audit Trail*. |
| 3 | Kepuasan Vendor & Mitra Klien | Pembayaran vendor sering tertunda karena lambatnya validasi dokumen penagihan dan berita acara. | Alur verifikasi BASTO transparan mempercepat proses administrasi pencairan tagihan vendor dan klien. |
| 4 | Pengambilan Keputusan Manajerial | Manajemen eksekutif terlambat memperoleh data tren serapan anggaran untuk mengambil keputusan mitigasi. | Integrasi Microsoft Power BI memudahkan pimpinan menganalisis risiko proyek lewat dashboard visual interaktif. |
| 5 | Budaya Kerja Karyawan | Ketergantungan tinggi pada proses manual dan dokumen fisik yang rentan terselip atau rusak. | Mendorong transformasi digital dan peningkatan efisiensi kolaborasi kerja lintas fungsi di lingkungan PGN COM. |

---

## 1.6. Perencanaan Aktivitas Secara Global

Pengembangan sistem informasi ini direncanakan dan dilaksanakan selama 3 (tiga) bulan masa Praktik Kerja Lapangan (setara 12 minggu kerja efektif). Rencana kerja disusun terstruktur berdasarkan *Work Breakdown Structure* (WBS) dan disajikan dalam bentuk matriks jadwal pelaksanaan pada Tabel 1.3 berikut:

#### Tabel 1.3 Matriks Perencanaan Aktivitas Global (WBS & Jadwal Pelaksanaan 3 Bulan)
| No | Tahapan Aktivitas Proyek | Bulan 1 | Bulan 2 | Bulan 3 | Output / Luaran |
| :---: | :--- | :---: | :---: | :---: | :--- |
| 1 | **Inisiasi & Observasi Lapangan**<br>Observasi proses bisnis realisasi proyek, format data Excel lama, dan wawancara unit DMO, QC, & Service Manager. | ✔ | | | Dokumen *Project Charter* & Catatan Observasi |
| 2 | **Analisis Kebutuhan Sistem**<br>Elisitasi kebutuhan fungsional & non-fungsional, serta perumusan matriks otorisasi 5 peran pengguna (*RBAC Matrix*). | ✔ | | | Dokumen Spesifikasi Kebutuhan Perangkat Lunak |
| 3 | **Perancangan Sistem (*System Design*)**<br>Pemodelan UML (*Use Case* & *Activity Diagram*), perancangan skema basis data relasional (ERD), dan desain *wireframe UI*. | | ✔ | | Diagram UML, Skema Database MySQL, & Mockup Antarmuka |
| 4 | **Pengkodean Sistem (*Construction / Coding*)**<br>Pengembangan modul Laravel 10, *engine* impor Excel, *workflow* BASTO bertingkat, monitoring biaya & invoice, serta *kiosk display*. | | ✔ | | Paket *Source Code* & Modul Aplikasi Web |
| 5 | **Integrasi Power BI & Pengujian Sistem**<br>Konfigurasi koneksi basis data ke Microsoft Power BI dan pengujian fungsionalitas sistem menggunakan metode *Black Box Testing*. | | | ✔ | Dashboard Analitik Power BI & Lembar Hasil Uji *Black Box* |
| 6 | **Penerapan & Dokumentasi (*Deployment & Handover*)**<br>*Deployment* sistem ke server lokal perusahaan, pelatihan pengguna (*user training*), dan penyusunan buku laporan akhir PKL. | | | ✔ | Sistem Siap Operasional & Buku Laporan Lengkap PKL |

---

# BAB II
# PROJECT REPORT

## 2.1. Metode Pengembangan Perangkat Lunak

Dalam membangun **Sistem Informasi Monitoring Realisasi Anggaran Proyek dan Dokumen BASTO Terintegrasi Power BI**, metode pengembangan perangkat lunak yang diterapkan adalah **Model Waterfall** (Klasik / Siklus Hidup Klasik) yang diadaptasi dari teori rekayasa perangkat lunak Roger S. Pressman.

Model Waterfall dipilih karena kebutuhan sistem pada lingkungan korporasi PT PGAS Telekomunikasi Nusantara telah terdefinisi dengan sangat matang, regulasi operasional mengenai BASTO dan pembukuan anggaran memiliki tata urutan birokrasi yang pasti, serta menuntut dokumentasi yang terstruktur pada setiap fasenya sebelum melangkah ke fase berikutnya.

Tahapan-tahapan metode Waterfall dalam proyek ini meliputi:
1. **Analisis Kebutuhan Perangkat Lunak (*Requirement Analysis*)**: Menggali seluruh proses bisnis pemantauan realisasi proyek dan pengajuan BASTO di lingkungan DMO PGN COM, mengumpulkan format lembar kerja Excel riil, serta memetakan hak akses aktor yang terlibat.
2. **Desain Sistem (*Design*)**: Menerjemahkan spesifikasi kebutuhan ke dalam representasi grafis rancangan teknis, meliputi pemodelan proses berbasis *Unified Modeling Language* (UML) seperti Use Case Diagram dan Activity Diagram, pemodelan data konseptual berupa *Entity Relationship Diagram* (ERD), serta perancangan tata letak antarmuka pengguna (*User Interface Layout*).
3. **Pengkodean (*Coding / Implementation*)**: Mengimplementasikan desain ke dalam baris-baris instruksi bahasa pemrograman. Pada tahap ini digunakan bahasa pemrograman PHP 8.x dengan kerangka kerja Laravel 10, basis data MySQL, template Blade, Javascript, serta Bootstrap CSS.
4. **Pengujian Perangkat Lunak (*Testing*)**: Melakukan pengujian terhadap fungsionalitas sistem yang telah dibangun menggunakan pendekatan *Black Box Testing*. Fokus pengujian adalah memastikan setiap masukan (*input*) menghasilkan keluaran (*output*) yang sesuai spesifikasi tanpa kegagalan fungsional.
5. **Penerapan dan Pemeliharaan (*Deployment and Maintenance*)**: Memasang sistem pada peladen lokal (*local production environment*) tempat magang, melakukan migrasi data master proyek, melaksanakan sosialisasi penggunaan kepada staf DMO dan QC, serta melakukan penyesuaian minor berdasarkan masukan operasional.

---

## 2.2. Tahap Pengembangan Perangkat Lunak

### 2.2.1. Analisis Kebutuhan Sistem

#### A. Kebutuhan Fungsional (*Functional Requirements*)
Kebutuhan fungsional mendefinisikan kapabilitas dan layanan yang harus disediakan oleh sistem secara langsung kepada pengguna:
1. **Manajemen Akun dan Keamanan Berbasis Peran**:
   - Sistem harus mampu melakukan autentikasi pengguna melalui username/email dan kata sandi terenkripsi.
   - Sistem harus menerapkan pembatasan hak akses halaman berdasarkan 7 peran (Admin, DMO, OSM Service Manager, OSM QC, Procurement, Finance, Sales).
   - Sistem menyediakan fitur *impersonate* khusus bagi Admin untuk mengecek keterbacaan data dari sudut pandang peran lain.
2. **Pengelolaan Data Master Proyek & Kontrak**:
   - Sistem hanya mengizinkan peran DMO untuk menambahkan, memperbarui, dan menghapus entitas data proyek master serta pagu kontrak.
   - Sistem memfasilitasi pencarian cepat (*quick search*) proyek berdasarkan nomor kontrak, nama proyek, klien, dan kode ID proyek.
3. **Mesin Impor Excel dan Kalkulasi Realisasi**:
   - Sistem harus mampu membaca file lembar kerja Excel berformat `.xlsx` sesuai format template standar PGN COM.
   - Sistem dapat mengurai dan mengkonversi angka mata uang berformat teks/simbol ke dalam tipe numerik desimal secara otomatis.
   - Sistem mencatat riwayat impor (*import log*) yang memuat informasi jumlah baris berhasil, tanggal impor, dan nama pengguna penanggung jawab.
4. **Alur Kerja Pengesahan Berkas BASTO (Pilar 1)**:
   - DMO dapat mengunggah berkas BASTO, melengkapi nomor dokumen, memilih jenis layanan, dan mengajukan status ke tahap peninjauan.
   - OSM QC dapat melakukan verifikasi kelayakan teknis berkas dengan mencentang item *QC checklist* dan memberikan status *QC Verified*.
   - OSM Service Manager memiliki wewenang memberikan persetujuan final (*Approved*) atau menolak (*Rejected*) disertai kolom catatan alasan revisi.
   - Sistem menyediakan fasilitas *Batch Approve* untuk menyetujui beberapa dokumen BASTO sekaligus dalam satu tindakan.
5. **Pemantauan Biaya dan Tagihan (Pilar 2 & Pilar 3)**:
   - Sistem menyajikan halaman komparasi *Cost Kontrak* yang menghitung persentase serapan biaya riil terhadap batas *cost based*.
   - Sistem menyediakan modul pelacakan *Invoice Vendor*, perbandingan nilai pengajuan tagihan terhadap prognosa, dan pencatatan bukti potong pajak.
6. **Penguncian Periode Transaksi (*Period Locking*)**:
   - Admin dapat mengunci atau membuka status bulan transaksi tertentu guna mencegah pengubahan data realisasi oleh pengguna lain setelah penutupan buku (*closing*).
7. **Pusat Komando Siaga (*Standby Kiosk Dashboard*)**:
   - Sistem menyediakan tampilan dasbor monitor tanpa navigasi manual (*zero scroll*) yang melakukan *polling* data statistik berkala setiap 10 detik.

#### B. Kebutuhan Non-Fungsional (*Non-Functional Requirements*)
1. **Keamanan (*Security*)**: Seluruh kata sandi dienkripsi menggunakan algoritma *Bcrypt*. Form transaksi dilindungi dari serangan *Cross-Site Request Forgery* (CSRF) dan injeksi SQL menggunakan *Eloquent ORM Parameterized Query*.
2. **Kinerja (*Performance*)**: Sistem mampu memproses impor file Excel hingga 2.000 baris data dalam waktu di bawah 15 detik, serta merespon permintaan kueri dasbor dalam waktu kurang dari 2 detik.
3. **Ketersediaan (*Availability*)**: Sistem dirancang untuk dapat diakses secara terus-menerus selama jam kerja operasional kantor berlangsung (24/7 di jaringan intranet).
4. **Kegunaan (*Usability*)**: Desain visual antarmuka menerapkan standar UI/UX modern berbasis tema Skydash, memiliki tata letak responsif, serta penanda status warna intuitif (*Badge Color* hijau untuk disetujui, kuning dalam proses, merah ditolak).
5. **Portabilitas (*Portability*)**: Aplikasi dapat dijalankan pada berbagai peramban web modern (*Google Chrome, Mozilla Firefox, Microsoft Edge*) tanpa memerlukan instalasi modul tambahan di sisi klien.

---

### 2.2.2. Perancangan Sistem

#### A. Pemodelan Use Case Diagram
Arsitektur fungsionalitas sistem dibagi menjadi modul-modul terdistribusi berdasarkan wewenang masing-masing aktor.

##### 1. Use Case Diagram - Modul Pengawasan, Akun & Hak Akses (Admin)
Menggambarkan interaksi pengguna berwenang Admin yang meliputi:
- Mengelola Akun Pengguna & Hak Akses RBAC (Tambah, Edit, Toggle Status, Reset Password).
- Melakukan Impersonasi User untuk supervisi langsung.
- Memantau Jejak Audit Log sistem.
- Mengatur Penguncian Periode Transaksi (*Period Locking*).

##### 2. Use Case Diagram - Peran Delivery Management Office (DMO)
Menggambarkan interaksi operasional utama staf DMO yang meliputi:
- Melakukan Impor Lembar Kerja Excel Realisasi Proyek.
- Menginput dan Memperbarui Master Data Proyek & Pagu Kontrak.
- Mengajukan Draf Berkas BASTO ke Alur Verifikasi.
- Mengunggah Ulang Dokumen Hasil Revisi jika terjadi penolakan.

##### 3. Use Case Diagram - Peran OSM QC & OSM Service Manager
- **Aktor OSM QC**: Meninjau draf BASTO masuk, melakukan cek fisik berkas, mengisi formulir verifikasi mutu, dan memberikan rekomendasi verifikasi (*QC Verified*).
- **Aktor OSM Service Manager**: Menyetujui pengajuan BASTO (*Approve*), menolak berkas BASTO dengan catatan (*Reject*), melakukan *Batch Approval*, serta mengelola alokasi prognosa anggaran bulanan.

##### 4. Deskripsi Skenario Use Case Utama

###### Tabel 2.2 Skenario Use Case Login Pengguna
- **Nama Use Case**: Login Pengguna
- **Aktor**: Seluruh Pengguna (Admin, DMO, OSM Service Manager, OSM QC, Procurement)
- **Tujuan**: Memverifikasi identitas pengguna dan membuka hak akses menu sesuai peran masing-masing.
- **Kondisi Awal**: Pengguna berada pada halaman utama formulir `/login`.
- **Skenario Normal**:
  1. Pengguna memasukkan username atau email dan kata sandi terdaftar.
  2. Pengguna menekan tombol "Sign In / Masuk".
  3. Sistem memvalidasi kredensial pengguna ke tabel `users`.
  4. Sistem memeriksa apakah status akun aktif (`active`).
  5. Sistem membuat sesi login dan mengarahkan pengguna ke dasbor sesuai peran (misal: Admin ke `/admin/dashboard`, Procurement ke `/procurement/dashboard`, DMO/SM ke `/dashboard`).
- **Skenario Alternatif**:
  - Jika kombinasi username dan kata sandi salah, sistem menampilkan notifikasi kesalahan "Kredensial tidak cocok".
  - Jika akun berstatus non-aktif, sistem menolak login dan menampilkan pesan "Akun Anda dinonaktifkan".

###### Tabel 2.4 Skenario Use Case Verifikasi Dokumen BASTO oleh OSM QC
- **Nama Use Case**: Verifikasi Dokumen BASTO
- **Aktor**: OSM QC
- **Tujuan**: Memeriksa kelayakan dan kesesuaian dokumen bukti serah terima pekerjaan sebelum diserahkan ke Service Manager.
- **Kondisi Awal**: Berkas BASTO telah berstatus `submitted` oleh tim DMO.
- **Skenario Normal**:
  1. Pengguna OSM QC membuka menu "Monitoring BASTO".
  2. Sistem menampilkan daftar BASTO yang berstatus menunggu verifikasi QC.
  3. Pengguna mengklik tombol "Verifikasi / QC Check" pada salah satu nomor BASTO.
  4. Sistem membuka modal verifikasi kelengkapan checklist dokumen.
  5. Pengguna memeriksa lampiran fisik/digital berkas BASTO.
  6. Pengguna mencentang item checklist dan menekan tombol "Submit Verifikasi QC".
  7. Sistem memperbarui status BASTO menjadi `qc_verified`, mencatat identitas verifikator, serta membuka opsi persetujuan bagi Service Manager.
- **Skenario Alternatif**:
  - Jika dokumen tidak lengkap, QC menambahkan catatan kekurangan dan meminta DMO mengunggah dokumen revisi.

###### Tabel 2.5 Skenario Use Case Approval Dokumen BASTO oleh Service Manager
- **Nama Use Case**: Persetujuan Dokumen BASTO (*Approve BASTO*)
- **Aktor**: OSM Service Manager
- **Tujuan**: Memberikan persetujuan manajerial final terhadap dokumen BASTO yang telah lolos uji mutu QC.
- **Kondisi Awal**: Berkas BASTO telah berstatus `qc_verified`.
- **Skenario Normal**:
  1. Service Manager masuk ke menu "Monitoring BASTO" atau "Executive Summary".
  2. Sistem menampilkan daftar BASTO dengan status `qc_verified`.
  3. Service Manager menekan tombol "Approve" (atau memilih beberapa berkas lalu menekan "Batch Approve").
  4. Sistem menampilkan dialog konfirmasi persetujuan.
  5. Pengguna mengonfirmasi persetujuan.
  6. Sistem mengubah status berkas menjadi `approved`, mencatat stempel waktu pengesahan, dan memperbarui rekapitulasi nilai proyek.
- **Skenario Alternatif**:
  - Jika terdapat klausul biaya yang belum sesuai kesepakatan klien, Service Manager menekan tombol "Reject", mengisi alasan penolakan, dan sistem mengubah status menjadi `rejected`.

---

#### B. Pemodelan Activity Diagram
Alur persetujuan dokumen BASTO secara berjenjang antar unit kerja dimodelkan dalam bentuk Activity Diagram sebagai berikut:
- **Swimlane DMO**: Mengunggah berkas BASTO -> Menekan tombol "Submit for Review" -> Menunggu hasil evaluasi -> Apabila ditolak: Mengunggah berkas revisi -> Selesai.
- **Swimlane OSM QC**: Menerima pengajuan BASTO -> Memeriksa kesesuaian item pekerjaan & lampiran -> Pengambilan keputusan (Apakah berkas memenuhi syarat mutu?) -> Jika Tidak: Mengembalikan berkas dengan catatan revisi -> Jika Ya: Mencentang Checklist QC & Mengubah status menjadi `qc_verified`.
- **Swimlane OSM Service Manager**: Meninjau berkas berstatus `qc_verified` -> Mengevaluasi nilai kontrak dan hak tagih -> Keputusan Manajerial -> Jika Setuju: Mengubah status BASTO menjadi `approved` -> Dokumen terarsip sah dan siap ditindaklanjuti untuk proses penagihan -> Selesai.

---

#### C. Perancangan Basis Data (Entity Relationship Diagram & Kamus Data)
Basis data sistem dirancang menggunakan model relasional dengan entitas-entitas utama sebagai berikut:

##### Tabel 2.7 Struktur Kamus Data Tabel Basis Data Utama

1. **Tabel `users`** (Menyimpan data akun dan peran otentikasi):
   - `id` (BigInt, PK, Auto Increment)
   - `name` (Varchar 255)
   - `username` (Varchar 100, Unique)
   - `email` (Varchar 255, Unique)
   - `password` (Varchar 255, Bcrypt Hash)
   - `role` (Varchar 50: 'admin', 'dmo', 'osm_service_manager', 'osm_qc', 'procurement')
   - `status` (Enum: 'active', 'inactive')
   - `timestamps` (`created_at`, `updated_at`)

2. **Tabel `kontrak`** (Menyimpan informasi master proyek dan nilai pagu):
   - `project_id` (Varchar 100, PK)
   - `tahun` (Year)
   - `service_manager` (Varchar 100)
   - `project_client` (Varchar 255)
   - `project_name` (Varchar 255)
   - `contract_number` (Varchar 150)
   - `project_value` (Decimal 18,2) - *Nilai Kontrak Klien*
   - `costbased` (Decimal 18,2) - *Pagu Anggaran Maksimal Biaya*
   - `actual_cost_konfirmasi` (Decimal 18,2) - *Realisasi Lapangan*
   - `actual_cost_admin` (Decimal 18,2) - *Realisasi Administrasi*
   - `persentase` (Decimal 5,2) - *Persentase Serapan Biaya*

3. **Tabel `realisasi`** (Menyimpan rincian transaksi realisasi pengeluaran riil):
   - `id` (BigInt, PK, Auto Increment)
   - `project_id` (Varchar 100, FK -> `kontrak.project_id`)
   - `nomor_transaksi` (Varchar 100)
   - `tanggal` (Date)
   - `deskripsi` (Text)
   - `jumlah_realisasi` (Decimal 18,2)
   - `kategori_biaya` (Varchar 100)
   - `bulan_periode` (TinyInt)
   - `tahun_periode` (Year)
   - `created_by` (Varchar 100)

4. **Tabel `bastos`** (Menyimpan berkas dan status alur kerja BASTO):
   - `id` (BigInt, PK, Auto Increment)
   - `project_id` (Varchar 100, FK -> `kontrak.project_id`)
   - `nomor_basto` (Varchar 150)
   - `tanggal_basto` (Date)
   - `nilai_pekerjaan` (Decimal 18,2)
   - `file_attachment` (Varchar 255)
   - `status` (Enum: 'draft', 'submitted', 'qc_verified', 'approved', 'rejected')
   - `qc_verified_by` (Varchar 100, Nullable)
   - `qc_verified_at` (Timestamp, Nullable)
   - `approved_by` (Varchar 100, Nullable)
   - `approved_at` (Timestamp, Nullable)
   - `rejection_note` (Text, Nullable)

5. **Tabel `period_locks`** (Menyimpan status penguncian periode transaksi):
   - `id` (BigInt, PK, Auto Increment)
   - `tahun` (Year)
   - `bulan` (TinyInt, 1 s.d 12)
   - `is_locked` (Boolean, Default False)
   - `locked_by` (Varchar 100, Nullable)
   - `locked_at` (Timestamp, Nullable)

---

### 2.2.3. Pengkodean Sistem (Konstruksi Perangkat Lunak)

Sistem dibangun dengan pola desain arsitektur *Model-View-Controller* (MVC). Logika pengolahan data dipisahkan secara teratur ke dalam berkas-berkas pengontrol (*Controllers*), model data *Eloquent*, dan antarmuka *Blade Template*.

Contoh implementasi logika penguncian periode transaksi pada *controller*:
```php
// Pemeriksaan Status Kunci Periode Sebelum Memproses Transaksi
$isLocked = PeriodLock::where('tahun', $request->tahun)
    ->where('bulan', $request->bulan)
    ->where('is_locked', true)
    ->exists();

if ($isLocked && !auth()->user()->hasRole('admin')) {
    return redirect()->back()->with('error', 'Transaksi ditolak: Periode bulan transaksi ini telah dikunci (Period Locked).');
}
```

Contoh implementasi logika persetujuan bertingkat (*Approval Logic*) dokumen BASTO:
```php
public function qcVerify(Request $request, Basto $basto)
{
    $this->authorize('verify-qc', $basto);
    $basto->update([
        'status'         => 'qc_verified',
        'qc_verified_by' => auth()->user()->name,
        'qc_verified_at' => now(),
        'qc_notes'       => $request->notes
    ]);
    return response()->json(['success' => true, 'message' => 'Dokumen BASTO berhasil diverifikasi oleh QC.']);
}
```

---

### 2.2.4. Pengujian Sistem (Black Box Testing)

Pengujian sistem dilakukan dengan menggunakan metode *Black Box Testing*, di mana sistem diuji secara fungsional berdasarkan masukan dan spesifikasi tanpa melihat alur baris kode internal.

#### Tabel 2.9 Hasil Pengujian Black Box Modul Autentikasi dan Otorisasi (RBAC)
| Kasus Uji | Skenario Pengujian | Masukan (*Input*) | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| UJI-AUTH-01 | Login pengguna dengan data valid | Username & Password benar | Berhasil masuk ke halaman dashboard sesuai hak akses peran | Sesuai | **Valid** |
| UJI-AUTH-02 | Login dengan kata sandi keliru | Username benar, Password salah | Sistem menolak akses dan memunculkan notifikasi error | Sesuai | **Valid** |
| UJI-AUTH-03 | Pengguna DMO mencoba mengakses menu admin user | Mengetikkan URL `/admin/users` secara manual | Sistem memblokir dan mengembalikan kode respon *403 Forbidden* | Sesuai | **Valid** |
| UJI-AUTH-04 | Fitur Impersonasi oleh Admin | Memilih salah satu user di tabel user management | Admin beralih tampilan dan hak akses menjadi peran yang dipilih | Sesuai | **Valid** |

#### Tabel 2.10 Hasil Pengujian Black Box Modul Engine Import Data Excel
| Kasus Uji | Skenario Pengujian | Masukan (*Input*) | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| UJI-IMP-01 | Mengunggah template Excel realisasi valid | File `.xlsx` sesuai kolom standar PGN COM | Data berhasil dibaca, angka diformat ke numerik, dan disimpan ke tabel basis data | Sesuai | **Valid** |
| UJI-IMP-02 | Mengunggah file non-Excel (PDF/Word) | Mengunggah file berekstensi `.pdf` | Sistem menolak unggahan dengan pesan validasi "Format file harus xlsx/xls" | Sesuai | **Valid** |
| UJI-IMP-03 | Konversi nilai mata uang kompleks | Teks masukan `"Rp. 1.250.000,50"` | Sistem membersihkan string menjadi nilai desimal `1250000.50` di database | Sesuai | **Valid** |

#### Tabel 2.11 Hasil Pengujian Black Box Modul Workflow BASTO Bertingkat
| Kasus Uji | Skenario Pengujian | Masukan (*Input*) | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| UJI-BST-01 | Pengajuan draf BASTO oleh DMO | Mengisi nomor BASTO, memilih proyek, unggah PDF | Berkas tersimpan dengan status `submitted` | Sesuai | **Valid** |
| UJI-BST-02 | Verifikasi kelayakan oleh QC | Membuka berkas, centang checklist QC, klik Verifikasi | Status berkas berubah menjadi `qc_verified` | Sesuai | **Valid** |
| UJI-BST-03 | Service Manager menyetujui BASTO | Mengklik tombol "Approve" pada BASTO `qc_verified` | Status berkas berubah menjadi `approved` dan tercatat stempel waktu pengesahan | Sesuai | **Valid** |
| UJI-BST-04 | Service Manager menolak BASTO | Mengklik "Reject" dan menulis alasan kekurangan | Status berubah menjadi `rejected` dan draf kembali ke antrean revisi DMO | Sesuai | **Valid** |
| UJI-BST-05 | Batch Approval banyak berkas | Mencentang 5 berkas sekaligus lalu klik "Batch Approve" | Kelima berkas sekaligus disahkan menjadi `approved` dalam 1 transaksi | Sesuai | **Valid** |

#### Tabel 2.12 Hasil Pengujian Black Box Modul Kontrol Periode & Layanan Kiosk
| Kasus Uji | Skenario Pengujian | Masukan (*Input*) | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| UJI-CTRL-01 | Mengunci periode buku bulan aktif | Mengaktifkan tombol toggle lock bulan tertentu | Periode bulan tersebut terkunci bagi seluruh pengguna non-admin | Sesuai | **Valid** |
| UJI-CTRL-02 | Mencoba input data pada periode terkunci | Mengirim form realisasi di bulan yang terkunci | Transaksi dibatalkan sistem dengan peringatan *Period Locked* | Sesuai | **Valid** |
| UJI-CTRL-03 | Tampilan Kiosk Command Center | Mengakses URL `/standby` di peramban monitor TV | Tampilan layar penuh tanpa *scroll bar*, data statistik grafik berkedip menyegarkan data mandiri | Sesuai | **Valid** |

---

# BAB III
# PENUTUP

## 3.1. Kesimpulan

Berdasarkan hasil analisis, perancangan, implementasi, serta pengujian yang telah dilaksanakan pada proyek Praktik Kerja Lapangan di PT PGAS Telekomunikasi Nusantara (PGN COM), dapat diambil kesimpulan sebagai berikut:

1. Telah berhasil dibangun dan diimplementasikan **"Sistem Informasi Monitoring Realisasi Anggaran Proyek dan Dokumen BASTO Terintegrasi Power BI"** yang mengintegrasikan seluruh data kontrak, penyerapan anggaran riil, serta alur pengesahan dokumen serah terima pekerjaan dalam satu platform terpusat.
2. Penerapan arsitektur *Role-Based Access Control* (RBAC) pada 5 peran operasional pengguna (Admin, DMO, OSM Service Manager, OSM QC, dan Procurement) mampu menciptakan batas wewenang kerja yang jelas, aman, dan akuntabel di lingkungan kerja lintas unit.
3. Fitur *Excel Import & Parsing Engine* telah terbukti mampu mengatasi permasalahan klasik fragmentasi data, memangkas durasi konsolidasi data laporan bulanan proyek sebesar ±85% dibandingkan metode manual sebelumnya.
4. Digitalisasi alur pengesahan dokumen BASTO yang dilengkapi fitur verifikasi berjenjang oleh Quality Control (QC) dan Service Manager mampu meminimalisir waktu penundaan berkas fisik, mempercepat penerbitan penagihan termin proyek (*invoice*), serta mengurangi risiko selisih pembebanan biaya vendor.
5. Mekanisme *Period Locking* dan modul *Command Center Kiosk Display* berhasil memberikan jaminan kepatuhan terhadap prosedur audit pembukuan internal serta menyediakan sarana pemantauan kinerja portofolio proyek secara waktu-nyata bagi jajaran manajemen eksekutif.

## 3.2. Saran

Guna pengembangan sistem yang lebih berdaya guna di masa yang akan datang, penulis mengajukan beberapa saran strategis:

1. **Integrasi Sistem ERP Terpusat Korporasi**: Disarankan untuk mengembangkan antarmuka pemrograman aplikasi (*RESTful API*) yang menghubungkan sistem monitoring ini secara langsung dengan sistem ERP inti induk perusahaan (seperti SAP / Oracle Finansial di lingkungan holding PT Perusahaan Gas Negara Tbk) agar pertukaran data jurnal pengeluaran berlangsung dua arah tanpa perlu impor berkas perantara.
2. **Penerapan Tanda Tangan Elektronik Tersertifikasi**: Mengintegrasikan sistem dengan sertifikat digital resmi (misalnya bekerja sama dengan Balai Sertifikasi Elektronik / BSrE atau penyedia tanda tangan digital tersertifikasi) sehingga berkas BASTO yang disetujui memiliki kekuatan hukum legal pembuktian digital yang sah secara nasional.
3. **Pemberitahuan Otomatis Berbasis Pesan Instan / Email**: Menambahkan modul notifikasi bot (melalui WhatsApp Business API atau integrasi e-mail perusahaan `@pgncom.co.id`) yang secara otomatis mengirimkan pemberitahuan kepada Service Manager dan tim QC saat terdapat berkas BASTO atau tagihan vendor baru yang membutuhkan tindakan segera.
4. **Penerapan Algoritma Kecerdasan Buatan untuk Prediksi Biaya**: Mengembangkan fitur analitik prediktif (*Machine Learning*) untuk memproyeksikan potensi risiko pembengkakan biaya proyek (*cost overrun forecasting*) berdasarkan histori penyerapan anggaran proyek sejenis pada tahun-tahun sebelumnya.

---

# DAFTAR PUSTAKA

1. Pressman, R. S., & Maxim, B. R. (2020). *Software Engineering: A Practitioner's Approach*. 9th Edition. New York: McGraw-Hill Education.
2. Sommerville, I. (2016). *Software Engineering*. 10th Edition. Boston: Pearson.
3. Dennis, A., Wixom, B. H., & Tegarden, D. (2015). *Systems Analysis and Design: An Object-Oriented Approach with UML*. 5th Edition. Hoboken: John Wiley & Sons.
4. Connolly, T. M., & Begg, C. E. (2015). *Database Systems: A Practical Approach to Design, Implementation, and Management*. 6th Edition. Boston: Pearson.
5. Stauffer, M. (2019). *Laravel: Up & Running: A Framework for Building Modern PHP Apps*. 2nd Edition. Sebastopol: O'Reilly Media.
6. Ladjamudin, A.-B. B. (2013). *Analisis dan Desain Sistem Informasi*. Yogyakarta: Graha Ilmu.
7. Rosa, A. S., & Shalahuddin, M. (2018). *Rekayasa Perangkat Lunak Terstruktur dan Berorientasi Objek*. Bandung: Informatika.
8. Kendall, K. E., & Kendall, J. E. (2014). *Systems Analysis and Design*. 9th Edition. Upper Saddle River: Pearson.
9. Dokumentasi Resmi Laravel. (2024). *Laravel Documentation: Routing, Controllers, Eloquent ORM, and Middleware*. Diakses dari https://laravel.com/docs.
10. PT PGAS Telekomunikasi Nusantara. (2026). *Profil Perusahaan dan Pedoman Tata Kelola Proyek Infrastruktur Jaringan*. Jakarta: PGN COM.

---

# DAFTAR RIWAYAT HIDUP

### A. Data Pribadi
- **Nama Lengkap** : Dimas Wijanarko
- **NIM** : [Isi Nomor Induk Mahasiswa Anda]
- **Tempat, Tanggal Lahir** : [Isi Kota Lahir], [Isi Tanggal Lahir]
- **Jenis Kelamin** : Laki-laki
- **Agama** : [Isi Agama]
- **Kewarganegaraan** : Indonesia
- **Alamat Tinggal** : [Isi Alamat Lengkap Tempat Tinggal]
- **Nomor Telepon/HP** : [Isi Nomor Handphone/WhatsApp]
- **Alamat E-mail** : [Isi Alamat Email Anda]

### B. Riwayat Pendidikan Formal
1. **Pendidikan Tinggi** : [Nama Universitas Anda], Program Studi [Sistem Informasi / Teknik Informatika], Tahun [Tahun Masuk] s.d Sekarang.
2. **Sekolah Menengah Kejuruan/Atas** : [Nama SMA/SMK Anda], Jurusan [IPA/IPS/RPL/TKJ], Lulus Tahun [Tahun Kelulusan].
3. **Sekolah Menengah Pertama** : [Nama SMP Anda], Lulus Tahun [Tahun Kelulusan].
4. **Sekolah Dasar** : [Nama SD Anda], Lulus Tahun [Tahun Kelulusan].

### C. Pengalaman Organisasi & Keahlian
- **Keahlian Teknis (*Hard Skills*)** : PHP, Laravel Framework, MySQL Database, HTML5/CSS3, JavaScript, Git Version Control, Unified Modeling Language (UML).
- **Keahlian Interpersonal (*Soft Skills*)** : Kemampuan Analisis Masalah Bisnis, Komunikasi Kerja Kolaboratif, Manajemen Waktu, Dokumentasi Teknis.

---

# TEMPLATE LAMPIRAN-LAMPIRAN KHUSUS OUTLINE 3

### LAMPIRAN 3: SURAT TANDA TERIMA UNTUK APLIKASI YANG DIIMPLEMENTASIKAN

```
                     SURAT TANDA TERIMA IMPLEMENTASI APLIKASI
                              No: 042/PGNCOM-DMO/STT/VIII/2026

Yang bertanda tangan di bawah ini:
Nama        : [Nama Pembimbing Lapangan / Manager DMO]
Jabatan     : Manager Delivery Management Office (DMO) / Head of Operation
Instansi    : PT PGAS Telekomunikasi Nusantara (PGN COM)
Alamat      : Kompleks Perkantoran PGN, Jl. KH. Zainul Arifin No. 20, Jakarta Barat

Dengan ini menerangkan bahwa mahasiswa Praktik Kerja Lapangan:
Nama        : Dimas Wijanarko
NIM         : [Isi NIM]
Program Studi: [Sistem Informasi / Teknik Informatika]
Perguruan Tinggi: [Nama Universitas]

Telah berhasil merancang, mengembangkan, dan mengimplementasikan aplikasi perangkat lunak:
Nama Aplikasi : Sistem Informasi Monitoring Realisasi Anggaran Proyek dan Dokumen BASTO Terintegrasi Power BI
Basis Teknologi: Web Application (Laravel 10, MySQL, Skydash Admin & Microsoft Power BI)
Lingkungan    : Local Server & Intranet PT PGAS Telekomunikasi Nusantara

Aplikasi tersebut telah diuji coba fungsionalnya dan dinyatakan DITERIMA serta TELAH DIGUNAKAN
dalam menunjang aktivitas operasional pemantauan realisasi proyek dan workflow BASTO pada unit kerja kami.

Demikian Surat Tanda Terima ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.

                                                          Jakarta, 28 Agustus 2026
                                                          Yang Menerima,
                                                          PT PGAS Telekomunikasi Nusantara


                                                          (...............................................)
                                                          Manager Delivery Management Office
```

---

### LAMPIRAN 4: BERITA ACARA SERAH TERIMA APLIKASI

```
                        BERITA ACARA SERAH TERIMA APLIKASI
                          No: 088/BAST-TI/PGNCOM/VIII/2026

Pada hari ini, Jumat tanggal Dua Puluh Delapan bulan Agustus tahun Dua Ribu Dua Puluh Enam (28-08-2026), bertempat di Kantor PT PGAS Telekomunikasi Nusantara, kami yang bertanda tangan di bawah ini:

I.  Nama    : Dimas Wijanarko
    NIM     : [Isi NIM]
    Status  : Mahasiswa Praktik Kerja Lapangan (PKL) [Nama Universitas]
    Selanjutnya disebut sebagai PIHAK PERTAMA (Yang Menyerahkan).

II. Nama    : [Nama Pejabat / Manager DMO PGN COM]
    Jabatan : Manager Delivery Management Office
    Instansi: PT PGAS Telekomunikasi Nusantara
    Selanjutnya disebut sebagai PIHAK KEDUA (Yang Menerima).

PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA menerima dari PIHAK PERTAMA berupa:
1. Paket Kode Sumber (*Source Code*) Aplikasi Sistem Informasi Monitoring Realisasi Anggaran Proyek dan Dokumen BASTO Terintegrasi Power BI.
2. Basis Data Master Proyek dan Skema Database MySQL (`db_input`).
3. Buku Panduan Penggunaan Sistem (*User Manual Guide*).

Dengan ketentuan bahwa pemeliharaan dan pemanfaatan sistem selanjutnya menjadi kewenangan operasional PT PGAS Telekomunikasi Nusantara.

Demikian Berita Acara Serah Terima Aplikasi ini dibuat dalam rangkap 2 (dua) bermeterai cukup dan memiliki kekuatan hukum yang sama.

     PIHAK PERTAMA,                                            PIHAK KEDUA,
(Yang Menyerahkan)                                          (Yang Menerima)



   Dimas Wijanarko                                     (.......................................)
Mahasiswa PKL / Pengembang                              Manager Delivery Management Office
```

---

### LAMPIRAN 5: PANDUAN DOKUMENTASI PRESENTASI & SOSIALISASI APLIKASI
*(Beri tempat untuk melampirkan foto dokumentasi saat melakukan presentasi aplikasi di depan mentor, tim DMO, atau Service Manager, serta daftar hadir peserta sosialisasi)*
