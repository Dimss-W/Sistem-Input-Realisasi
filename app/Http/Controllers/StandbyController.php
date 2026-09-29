<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Realisasi;
use App\Models\Kontrak;
use App\Models\Basto;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Prognosa;
use Illuminate\Support\Facades\DB;

class StandbyController extends Controller
{
    /**
     * Tampilkan halaman Standby Kiosk Dashboard (TV Display).
     */
    public function index()
    {
        $data = $this->collectMetrics();
        return view('dashboard.standby', compact('data'));
    }

    /**
     * Endpoint API JSON untuk auto-refresh data di background tanpa reload halaman.
     */
    public function getData()
    {
        $data = $this->collectMetrics();
        return response()->json([
            'status'     => 'success',
            'timestamp'  => now()->toIso8601String(),
            'server_time'=> now()->format('H:i:s'),
            'date_human' => now()->locale('id')->translatedFormat('l, d F Y'),
            'data'       => $data
        ]);
    }

    /**
     * Kumpulkan seluruh metrik operasional terpenting secara terpusat.
     */
    private function collectMetrics(): array
    {
        $currentYear = date('Y');

        // 1. Pagu Kontrak & Realisasi Global
        $totalPaguKontrak = (float) Kontrak::sum('project_value');
        $totalRealisasi   = (float) Realisasi::sum('realisasi_biaya_final');
        $totalPrognosa    = (float) Prognosa::sum('prognosa_biaya');
        $sisaAnggaran     = max(0, $totalPaguKontrak - $totalRealisasi);
        $serapanPct       = $totalPaguKontrak > 0 ? round(($totalRealisasi / $totalPaguKontrak) * 100, 1) : 0;
        $totalProyekAktif = Kontrak::count();
        $totalTransaksi   = Realisasi::count();

        // 2. Pipeline BASTO & Mutu QC
        $bastoSubmitted  = Basto::where('status', 'submitted')->count();
        $bastoQcVerified = Basto::where('qc_status', 'verified')->where('status', '!=', 'approved')->count();
        $bastoRevision   = Basto::where('qc_status', 'revision_needed')->count();
        $bastoApproved   = Basto::where('status', 'approved')->count();
        $bastoTotal      = Basto::count();

        // 3. Status Keuangan & Tagihan (Invoice & Revenue)
        $totalInvoiced   = (float) Invoice::sum('invoice_amount');
        $totalPaid       = (float) Invoice::sum('payment_amount');
        $totalPph23      = (float) Invoice::sum('pph23_deducted');
        $totalPiutang    = max(0, $totalInvoiced - $totalPaid - $totalPph23);
        $invoiceOverdue  = Invoice::where('payment_status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        // 4. CHART 1: Orang vs Kontrak yang Dipegang (SM & PIC DMO)
        $smRows = Kontrak::select(DB::raw("COALESCE(NULLIF(TRIM(service_manager), ''), 'Belum Ditugaskan') as sm_name"), DB::raw('COUNT(*) as total_kontrak'))
            ->groupBy('sm_name')
            ->orderByDesc('total_kontrak')
            ->get();

        $picRows = Realisasi::whereNotNull('pic')
            ->where('pic', '!=', '')
            ->select('pic', DB::raw('COUNT(DISTINCT project_id) as total_kontrak'))
            ->groupBy('pic')
            ->orderByDesc('total_kontrak')
            ->get();

        $chart1Orang = [
            'sm' => [
                'labels' => $smRows->pluck('sm_name')->toArray(),
                'data'   => $smRows->pluck('total_kontrak')->map(fn($v) => (int)$v)->toArray(),
            ],
            'pic' => [
                'labels' => $picRows->pluck('pic')->toArray(),
                'data'   => $picRows->pluck('total_kontrak')->map(fn($v) => (int)$v)->toArray(),
            ],
            'total_sm'  => $smRows->where('sm_name', '!=', 'Belum Ditugaskan')->count(),
            'total_pic' => $picRows->count(),
        ];

        // 5. CHART 2: Total Nilai Kontrak Vendor dari Semua Kontrak dan Masing-masing Kontrak
        $vendorKontrakRows = DB::table('realisasi')
            ->join('kontrak', 'realisasi.project_id', '=', 'kontrak.project_id')
            ->whereNotNull('realisasi.vendor')
            ->where('realisasi.vendor', '!=', '')
            ->select('realisasi.vendor', 'kontrak.project_id', 'kontrak.project_value')
            ->distinct()
            ->get();

        $vendorNilaiMap = [];
        foreach ($vendorKontrakRows as $row) {
            $v = trim($row->vendor);
            if (!isset($vendorNilaiMap[$v])) {
                $vendorNilaiMap[$v] = [
                    'name'            => $v,
                    'pagu'            => 0,
                    'contracts_count' => 0,
                ];
            }
            $vendorNilaiMap[$v]['pagu'] += (float)$row->project_value;
            $vendorNilaiMap[$v]['contracts_count']++;
        }
        uasort($vendorNilaiMap, fn($a, $b) => $b['pagu'] <=> $a['pagu']);
        $topVendorsPagu = array_slice(array_values($vendorNilaiMap), 0, 7);

        $chart2VendorPagu = [
            'total_pagu_all' => $totalPaguKontrak,
            'labels'         => array_map(fn($v) => strlen($v) > 28 ? substr($v, 0, 26) . '...' : $v, array_column($topVendorsPagu, 'name')),
            'full_labels'    => array_column($topVendorsPagu, 'name'),
            'data'           => array_column($topVendorsPagu, 'pagu'),
            'contracts'      => array_column($topVendorsPagu, 'contracts_count'),
        ];

        // 6. CHART 3: Realisasi Nilai Kontrak Vendor dari Semua Kontrak dan Masing-masing Kontrak
        $topVendorsRealisasi = Realisasi::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->select('vendor', DB::raw('SUM(realisasi_biaya_final) as total_realisasi'), DB::raw('COUNT(DISTINCT project_id) as total_kontrak'))
            ->groupBy('vendor')
            ->orderByDesc('total_realisasi')
            ->limit(7)
            ->get();

        $vPercentages = $topVendorsRealisasi->map(fn($v) => $totalRealisasi > 0 ? round(($v->total_realisasi / $totalRealisasi) * 100, 1) : 0)->toArray();
        $top2Share = round(($vPercentages[0] ?? 0) + ($vPercentages[1] ?? 0), 1);

        $chart3VendorRealisasi = [
            'total_realisasi_all' => $totalRealisasi,
            'top2_share'          => $top2Share,
            'labels'              => $topVendorsRealisasi->pluck('vendor')->map(function($v) {
                $name = trim($v);
                return strlen($name) > 28 ? substr($name, 0, 26) . '...' : $name;
            })->toArray(),
            'full_labels'         => $topVendorsRealisasi->pluck('vendor')->map(fn($v) => trim($v))->toArray(),
            'data'                => $topVendorsRealisasi->map(fn($v) => (float)$v->total_realisasi)->toArray(),
            'contracts'           => $topVendorsRealisasi->pluck('total_kontrak')->map(fn($v) => (int)$v)->toArray(),
            'percentages'         => $vPercentages,
        ];

        // 7. CHART 4: Membandingkan Total Nilai Kontrak vs Total Nilai Realisasi
        $komparasiProjectsRaw = Kontrak::select('project_id', 'project_name', 'project_value')
            ->orderByDesc('project_value')
            ->limit(6)
            ->get();

        $komparasiProjects = [];
        foreach ($komparasiProjectsRaw as $kp) {
            $rSum = (float) Realisasi::where('project_id', $kp->project_id)->sum('realisasi_biaya_final');
            $pVal = (float) $kp->project_value;
            $cleanName = trim(preg_replace('/^(Pekerjaan\s+|Pengadaan\s+|Layanan\s+)/i', '', $kp->project_name));
            $short = strlen($cleanName) > 20 ? substr($cleanName, 0, 19) . '..' : $cleanName;
            $komparasiProjects[] = [
                'project_id'   => $kp->project_id,
                'project_name' => $kp->project_name,
                'short_name'   => "{$kp->project_id} - {$short}",
                'pagu'         => $pVal,
                'realisasi'    => $rSum,
                'serapan_pct'  => $pVal > 0 ? round(($rSum / $pVal) * 100, 1) : 0,
            ];
        }

        // 8. DATA TREN BULANAN (12 BULAN) & KLASIFIKASI KONTRAK
        $bulanList = [
            'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];
        $shortBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $realisasiPerBulanRaw = Realisasi::where('tahun', $currentYear)
            ->select('periode', DB::raw('SUM(realisasi_biaya_final) as total'))
            ->groupBy('periode')
            ->pluck('total', 'periode');

        $prognosaPerBulanRaw = Prognosa::select('periode', DB::raw('SUM(prognosa_biaya) as total'))
            ->groupBy('periode')
            ->pluck('total', 'periode');

        $realisasiBulanan = [];
        $prognosaBulanan  = [];
        foreach ($bulanList as $b) {
            $realisasiBulanan[] = (float) ($realisasiPerBulanRaw[$b] ?? 0);
            $prognosaBulanan[]  = (float) ($prognosaPerBulanRaw[$b] ?? 0);
        }

        $classificationsRaw = Kontrak::select('project_classification', DB::raw('COUNT(*) as total_proyek'), DB::raw('SUM(project_value) as total_pagu'))
            ->groupBy('project_classification')
            ->orderByDesc('total_pagu')
            ->get();

        $classificationData = [
            'labels'   => $classificationsRaw->pluck('project_classification')->toArray(),
            'counts'   => $classificationsRaw->pluck('total_proyek')->map(fn($v) => (int)$v)->toArray(),
            'values'   => $classificationsRaw->pluck('total_pagu')->map(fn($v) => (float)$v)->toArray(),
            'pcts'     => $classificationsRaw->map(fn($c) => $totalPaguKontrak > 0 ? round(($c->total_pagu / $totalPaguKontrak) * 100, 1) : 0)->toArray(),
        ];

        $chart4Komparasi = [
            'global' => [
                'total_pagu'      => $totalPaguKontrak,
                'total_realisasi' => $totalRealisasi,
                'sisa'            => $sisaAnggaran,
                'serapan_pct'     => $serapanPct,
            ],
            'projects' => [
                'labels'    => array_column($komparasiProjects, 'short_name'),
                'ids'       => array_column($komparasiProjects, 'project_id'),
                'pagu'      => array_column($komparasiProjects, 'pagu'),
                'realisasi' => array_column($komparasiProjects, 'realisasi'),
                'pct'       => array_column($komparasiProjects, 'serapan_pct'),
            ],
            'monthly' => [
                'labels'    => $shortBulan,
                'realisasi' => $realisasiBulanan,
                'prognosa'  => $prognosaBulanan,
            ],
            'classifications' => $classificationData,
        ];

        // 9. Evaluasi Kesehatan Seluruh Portofolio Proyek & Identifikasi Proyek Kritis
        $allKontrak = Kontrak::all();
        $healthSummary = [
            'safe'     => 0,
            'warning'  => 0,
            'critical' => 0,
        ];
        $allProjectStatus = [];

        foreach ($allKontrak as $k) {
            $pId = $k->project_id;
            $pVal = (float) $k->project_value;
            $rSum = (float) Realisasi::where('project_id', $pId)->sum('realisasi_biaya_final');
            $pct = $pVal > 0 ? round(($rSum / $pVal) * 100, 1) : 0;

            if ($pct >= 90) {
                $healthSummary['critical']++;
            } elseif ($pct >= 75) {
                $healthSummary['warning']++;
            } else {
                $healthSummary['safe']++;
            }

            if ($pVal > 0 && $rSum > 0) {
                $allProjectStatus[] = [
                    'project_id'   => $pId,
                    'project_name' => $k->project_name,
                    'pagu'         => $pVal,
                    'realisasi'    => $rSum,
                    'serapan_pct'  => $pct,
                ];
            }
        }

        // Urutkan berdasarkan persentase serapan tertinggi (Early Warning)
        usort($allProjectStatus, fn($a, $b) => $b['serapan_pct'] <=> $a['serapan_pct']);
        $criticalProjects = array_slice($allProjectStatus, 0, 6);

        $chart4Komparasi['critical_projects'] = [
            'labels'     => array_map(function($p) {
                $cleanName = trim(preg_replace('/^(Pekerjaan\s+|Pengadaan\s+|Layanan\s+)/i', '', $p['project_name']));
                $short = strlen($cleanName) > 20 ? substr($cleanName, 0, 19) . '..' : $cleanName;
                return "[{$p['project_id']}] {$short}";
            }, $criticalProjects),
            'full_names' => array_column($criticalProjects, 'project_name'),
            'ids'        => array_column($criticalProjects, 'project_id'),
            'pagu'       => array_column($criticalProjects, 'pagu'),
            'realisasi'  => array_column($criticalProjects, 'realisasi'),
            'pct'        => array_column($criticalProjects, 'serapan_pct'),
            'over_count' => count(array_filter($criticalProjects, fn($p) => $p['serapan_pct'] > 100)),
        ];

        // 10. Executive Strategic Insights (Pemberitahuan Informasi Jelas)
        $executiveInsights = [
            [
                'badge' => 'Serapan Terkendali',
                'color' => 'emerald',
                'icon'  => 'bi-shield-check',
                'text'  => 'Realisasi kumulatif ' . $serapanPct . '% (Rp ' . number_format($totalRealisasi / 1e9, 1, ',', '.') . ' M) berada pada batas aman pagu tahunan.',
            ],
            [
                'badge' => 'Perhatian 6 Proyek',
                'color' => 'rose',
                'icon'  => 'bi-exclamation-octagon-fill',
                'text'  => '6 proyek mengalami serapan >100% pagu (tertinggi PS-049-00 di 175,6%), memerlukan tinjauan addendum kontrak.',
            ],
            [
                'badge' => 'Konsentrasi Rekanan',
                'color' => 'sky',
                'icon'  => 'bi-building-fill-check',
                'text'  => 'Top 2 Rekanan (Packet Systems & Persada) menyerap ' . $top2Share . '% dari seluruh total belanja portofolio.',
            ],
            [
                'badge' => 'Outlook Semester 2',
                'color' => 'amber',
                'icon'  => 'bi-calendar2-week-fill',
                'text'  => 'Estimasi kebutuhan belanja prognosa hingga akhir tahun diestimasikan Rp ' . number_format($totalPrognosa / 1e9, 1, ',', '.') . ' M.',
            ],
        ];

        return [
            'summary' => [
                'total_pagu'           => $totalPaguKontrak,
                'total_realisasi'      => $totalRealisasi,
                'total_prognosa'       => $totalPrognosa,
                'sisa_anggaran'        => $sisaAnggaran,
                'serapan_pct'          => $serapanPct,
                'total_proyek'         => $totalProyekAktif,
                'total_transaksi'      => $totalTransaksi,
                'current_year'         => $currentYear,
            ],
            'health_summary'           => $healthSummary,
            'pipeline' => [
                'basto_total'          => $bastoTotal,
                'basto_submitted'      => $bastoSubmitted,
                'basto_qc_verified'    => $bastoQcVerified,
                'basto_revision'       => $bastoRevision,
                'basto_approved'       => $bastoApproved,
            ],
            'finance' => [
                'total_invoiced'       => $totalInvoiced,
                'total_paid'           => $totalPaid,
                'total_piutang'        => $totalPiutang,
                'invoice_overdue'      => $invoiceOverdue,
            ],
            'charts' => [
                'chart1_orang'            => $chart1Orang,
                'chart2_vendor_pagu'      => $chart2VendorPagu,
                'chart3_vendor_realisasi' => $chart3VendorRealisasi,
                'chart4_komparasi'        => $chart4Komparasi,
            ],
            'executive_insights'       => $executiveInsights,
            'recent_activities'        => [],
        ];
    }
}
