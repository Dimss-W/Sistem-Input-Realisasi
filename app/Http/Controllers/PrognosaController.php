<?php

namespace App\Http\Controllers;

use App\Models\Prognosa;
use App\Models\Realisasi;
use App\Models\Kontrak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrognosaController extends Controller
{
    public function __construct()
    {
        // SM, DMO, QC, Procurement, Admin bisa melihat monitoring prognosa
        $this->middleware('role:dmo,osm_service_manager,osm_qc,procurement,admin');
    }

    // =========================================================================
    // INDEX — Dashboard Monitoring Prognosa & Budget Alert
    // =========================================================================
    public function index(Request $request)
    {
        $tahun      = $request->get('tahun', 2026);
        $project_id = $request->get('project_id');

        $periodeOrder = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
                         'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];

        $isSM = auth()->check() && auth()->user()->hasRole('osm_service_manager');
        $smProjects = [];
        if ($isSM) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $smProjects = \App\Models\Kontrak::whereIn('service_manager', $smNames)->pluck('project_id')->toArray();
        }

        // ---- Realisasi per project ----
        $realisasiQuery = Realisasi::select('project_id', 'project_name')
            ->selectRaw('SUM(realisasi_biaya_final) as total_realisasi')
            ->selectRaw('COUNT(*) as cnt')
            ->where('tahun', $tahun)
            ->groupBy('project_id', 'project_name');

        if ($project_id) {
            $realisasiQuery->where('project_id', $project_id);
        }

        if ($isSM) {
            $realisasiQuery->whereIn('project_id', $smProjects);
        }

        $realisasiByProject = $realisasiQuery->orderByDesc('total_realisasi')->get();

        // ---- Prognosa per project ----
        $prognosaQuery = Prognosa::select('project_id', 'project_name')
            ->selectRaw('SUM(prognosa_biaya) as total_prognosa')
            ->where(function ($q) use ($tahun) {
                $q->where('tahun_original', $tahun)
                  ->orWhere('tahun_normalized', $tahun);
            })
            ->groupBy('project_id', 'project_name');

        if ($project_id) {
            $prognosaQuery->where('project_id', $project_id);
        }

        if ($isSM) {
            $prognosaQuery->whereIn('project_id', $smProjects);
        }

        $prognosaByProject = $prognosaQuery->get()->keyBy('project_id');

        // ---- Kontrak (budget = project_value) ----
        $kontrakBudgetsQuery = Kontrak::where('tahun', $tahun);
        if ($project_id) {
            $kontrakBudgetsQuery->where('project_id', $project_id);
        }
        if ($isSM) {
            $kontrakBudgetsQuery->whereIn('service_manager', $smNames);
        }
        $kontrakBudgets = $kontrakBudgetsQuery->get(['project_id', 'project_name', 'project_value', 'actual_cost_konfirmasi'])
            ->keyBy('project_id');

        // ---- Build comparison table ----
        $comparisonData = [];
        $alertProjects  = [];

        foreach ($realisasiByProject as $r) {
            $pid      = $r->project_id;
            $budget   = (float) ($kontrakBudgets[$pid]->project_value ?? 0);
            $realisasi = (float) $r->total_realisasi;
            $prognosa  = (float) ($prognosaByProject[$pid]->total_prognosa ?? 0);

            // Serapan % (vs budget)
            $serapan = $budget > 0 ? ($realisasi / $budget) * 100 : 0;
            // Sisa budget
            $sisaBudget = $budget - $realisasi;

            $comparisonData[] = [
                'project_id'    => $pid,
                'project_name'  => $r->project_name,
                'budget'        => $budget,
                'prognosa'      => $prognosa,
                'realisasi'     => $realisasi,
                'sisa_budget'   => $sisaBudget,
                'serapan_pct'   => round($serapan, 1),
                'variance_rp'   => $realisasi - $prognosa,
                'variance_pct'  => $prognosa > 0 ? round((($realisasi - $prognosa) / $prognosa) * 100, 1) : 0,
                'variance_status'=> $realisasi > $prognosa ? 'over' : ($realisasi < $prognosa ? 'under' : 'exact'),
                'is_over_budget'=> $realisasi > $budget && $budget > 0,
                'is_near_budget'=> $serapan >= 85 && $serapan < 100,
            ];

            if ($realisasi > $budget && $budget > 0) {
                $alertProjects[] = $r->project_name . " (over budget " . number_format($realisasi - $budget, 0, ',', '.') . ")";
            } elseif ($serapan >= 85 && $budget > 0) {
                $alertProjects[] = $r->project_name . " (serapan {$serapan}%)";
            }
        }

        // Sort by serapan descending
        usort($comparisonData, fn($a, $b) => $b['serapan_pct'] <=> $a['serapan_pct']);

        // ---- Realisasi per periode (monthly trend) ----
        $trendQuery = Realisasi::select('periode')
            ->selectRaw('SUM(realisasi_biaya_final) as total')
            ->where('tahun', $tahun)
            ->when($project_id, fn($q) => $q->where('project_id', $project_id))
            ->when($isSM, fn($q) => $q->whereIn('project_id', $smProjects))
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        $prognosaMonthly = Prognosa::select('periode')
            ->selectRaw('SUM(prognosa_biaya) as total')
            ->where(function ($q) use ($tahun) {
                $q->where('tahun_original', $tahun)->orWhere('tahun_normalized', $tahun);
            })
            ->when($project_id, fn($q) => $q->where('project_id', $project_id))
            ->when($isSM, fn($q) => $q->whereIn('project_id', $smProjects))
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        $monthlyTrend = [];
        foreach ($periodeOrder as $p) {
            $monthlyTrend[] = [
                'periode'   => $p,
                'realisasi' => (float) ($trendQuery[$p]->total ?? 0),
                'prognosa'  => (float) ($prognosaMonthly[$p]->total ?? 0),
            ];
        }

        // ---- Summary cards & Variance ----
        $totalRealisasi = array_sum(array_column($comparisonData, 'realisasi'));
        $totalPrognosa  = array_sum(array_column($comparisonData, 'prognosa'));
        $totalBudget    = array_sum(array_column($comparisonData, 'budget'));
        $overBudgetCnt  = count(array_filter($comparisonData, fn($d) => $d['is_over_budget']));
        $totalVarianceRp = $totalRealisasi - $totalPrognosa;
        $totalVariancePct = $totalPrognosa > 0 ? round(($totalVarianceRp / $totalPrognosa) * 100, 1) : 0;

        // Dropdown
        $tahunList  = Realisasi::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($isSM) {
            $projectIds = Realisasi::whereIn('project_id', $smProjects)->distinct()->orderBy('project_id')->pluck('project_id');
        } else {
            $projectIds = Realisasi::distinct()->orderBy('project_id')->pluck('project_id');
        }

        return view('prognosa.index', compact(
            'comparisonData', 'monthlyTrend', 'alertProjects',
            'totalRealisasi', 'totalPrognosa', 'totalBudget', 'overBudgetCnt',
            'totalVarianceRp', 'totalVariancePct',
            'tahun', 'project_id', 'tahunList', 'projectIds'
        ));
    }

    // =========================================================================
    // DETAIL — Detail monitoring per project (breakdown per item/periode)
    // =========================================================================
    public function show(Request $request, string $projectId)
    {
        $tahun = $request->get('tahun', 2026);

        $isSM = auth()->check() && auth()->user()->hasRole('osm_service_manager');

        if ($isSM) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $hasAccess = Kontrak::where('project_id', $projectId)->whereIn('service_manager', $smNames)->exists();
            if (!$hasAccess) {
                abort(403, 'Unauthorized access to project.');
            }
        }

        $periodeOrder = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
                         'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];

        // Kontrak info
        $kontrak = Kontrak::where('project_id', $projectId)->first();

        // Realisasi breakdown per item & periode
        $realisasiItems = Realisasi::select('item_biaya', 'periode')
            ->selectRaw('SUM(realisasi_biaya_final) as total')
            ->where('project_id', $projectId)
            ->where('tahun', $tahun)
            ->groupBy('item_biaya', 'periode')
            ->get();

        // Prognosa breakdown per activity & periode
        $prognosaItems = Prognosa::select('activity', 'periode')
            ->selectRaw('SUM(prognosa_biaya) as total')
            ->where('project_id', $projectId)
            ->where(function ($q) use ($tahun) {
                $q->where('tahun_original', $tahun)->orWhere('tahun_normalized', $tahun);
            })
            ->groupBy('activity', 'periode')
            ->get();

        // Monthly comparison (prognosa vs realisasi)
        $realisasiByPeriode = $realisasiItems->groupBy('periode')
            ->map(fn($items) => $items->sum('total'));

        $prognosaByPeriode = $prognosaItems->groupBy('periode')
            ->map(fn($items) => $items->sum('total'));

        $monthlyComparison = [];
        foreach ($periodeOrder as $p) {
            $monthlyComparison[] = [
                'periode'   => $p,
                'realisasi' => (float) ($realisasiByPeriode[$p] ?? 0),
                'prognosa'  => (float) ($prognosaByPeriode[$p] ?? 0),
            ];
        }

        $totalRealisasi = $realisasiItems->sum('total');
        $totalPrognosa  = $prognosaItems->sum('total');
        $budget         = (float) ($kontrak->project_value ?? 0);
        $serapanPct     = $budget > 0 ? round(($totalRealisasi / $budget) * 100, 1) : 0;

        $prognosaRecords = Prognosa::with('purchaseOrder')
            ->where('project_id', $projectId)
            ->where(function ($q) use ($tahun) {
                $q->where('tahun_original', $tahun)->orWhere('tahun_normalized', $tahun);
            })
            ->orderByRaw("FIELD(periode, 'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER') ASC")
            ->get();

        $availablePOs = \App\Models\PurchaseOrder::where('project_id', $projectId)
            ->where('prognosa_tahun', $tahun)
            ->get();

        return view('prognosa.show', compact(
            'kontrak', 'projectId', 'tahun',
            'monthlyComparison', 'realisasiItems', 'prognosaItems', 'prognosaRecords', 'availablePOs',
            'totalRealisasi', 'totalPrognosa', 'budget', 'serapanPct'
        ));
    }

    /**
     * Update/Create estimasi Prognosa bulanan (Service Manager & Admin).
     */
    public function updateMonthlyPrognosa(Request $request)
    {
        if (!auth()->user()->hasRole(['osm_service_manager', 'admin'])) {
            abort(403, 'Hanya Service Manager dan Admin yang dapat memperbarui estimasi Prognosa.');
        }

        $validated = $request->validate([
            'project_id'     => 'required|string',
            'periode'        => 'required|string',
            'tahun'          => 'required|integer',
            'prognosa_biaya' => 'required|numeric|min:0',
            'activity'       => 'nullable|string|max:255',
        ]);

        $prognosa = Prognosa::updateOrCreate(
            [
                'project_id'       => $validated['project_id'],
                'periode'          => strtoupper($validated['periode']),
                'tahun_normalized' => $validated['tahun'],
            ],
            [
                'prognosa_biaya'   => $validated['prognosa_biaya'],
                'activity'         => $validated['activity'] ?? 'Estimasi Biaya Operasional',
                'tahun_original'   => $validated['tahun'],
                'project_name'     => $request->input('project_name', $validated['project_id']),
                'data_source'      => 'sm_manual',
            ]
        );

        \App\Models\ActivityLog::log(
            'UPDATE_PROGNOSA_SM',
            'prognosa',
            (string) $prognosa->id,
            null,
            null,
            "Memperbarui estimasi Prognosa {$prognosa->project_id} periode {$prognosa->periode} {$validated['tahun']} menjadi Rp " . number_format($prognosa->prognosa_biaya, 0, ',', '.')
        );

        return redirect()->back()->with('success', 'Estimasi Prognosa berhasil diperbarui.');
    }

    /**
     * Rekonsiliasi & Override Konflik Data Prognosa (SM vs PO Pengadaan).
     */
    public function reconcileOverride(Request $request, $id)
    {
        if (!auth()->user()->hasRole(['osm_service_manager', 'admin'])) {
            abort(403, 'Hanya Service Manager dan Admin yang berwenang merekonsiliasi data Prognosa.');
        }

        $prognosa = Prognosa::findOrFail($id);
        $action = $request->input('action', 'use_po');
        $poId = $request->input('po_id', $prognosa->po_id);

        if ($action === 'use_po' && $poId) {
            $po = \App\Models\PurchaseOrder::find($poId);
            if ($po) {
                $prognosa->update([
                    'prognosa_biaya' => $po->po_amount,
                    'po_id'          => $po->id,
                    'data_source'    => 'procurement_po',
                    'is_overridden'  => true,
                    'activity'       => 'Rekonsiliasi Real PO #' . $po->po_number . ' (' . ($po->vendor_name ?: 'Procurement') . ')',
                ]);

                \App\Models\ActivityLog::log(
                    'RECONCILE_PROGNOSA_PO',
                    'prognosa',
                    (string) $prognosa->id,
                    null,
                    null,
                    "Rekonsiliasi Prognosa ID #{$prognosa->id} ({$prognosa->project_id} - {$prognosa->periode} {$prognosa->tahun_normalized}) menggunakan data riil PO #{$po->po_number} Rp " . number_format($po->po_amount, 0, ',', '.')
                );
            }
        } elseif ($action === 'reset_sm') {
            $prognosa->update([
                'is_overridden' => false,
                'data_source'   => 'sm_manual',
            ]);

            \App\Models\ActivityLog::log(
                'RESET_PROGNOSA_SM',
                'prognosa',
                (string) $prognosa->id,
                null,
                null,
                "Mengembalikan status Prognosa ID #{$prognosa->id} ({$prognosa->project_id} - {$prognosa->periode}) ke Estimasi SM"
            );
        }

        return redirect()->back()->with('success', 'Data Prognosa berhasil direkonsiliasi.');
    }
}
