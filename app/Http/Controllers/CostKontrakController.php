<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontrak;
use App\Models\Realisasi;
use App\Models\Prognosa;
use Illuminate\Support\Facades\DB;

class CostKontrakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,dmo,osm_service_manager,procurement');
    }

    /**
     * Tampilkan Halaman Monitoring Cost Kontrak (Cost vs Realisasi).
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'service_manager', 'client', 'tahun', 'health_status']);

        $user = auth()->user();
        $isSM = $user->hasRole('osm_service_manager');

        // Query Kontrak
        $query = Kontrak::query();

        // Jika Service Manager, batasi ke proyek supervisinya
        if ($isSM) {
            $smNames = $user->getSupervisedServiceManagers();
            $query->whereIn('service_manager', $smNames);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function($q) use ($s) {
                $q->where('project_id', 'like', "%{$s}%")
                  ->orWhere('project_name', 'like', "%{$s}%")
                  ->orWhere('project_client', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['service_manager'])) {
            $query->where('service_manager', $filters['service_manager']);
        }

        if (!empty($filters['client'])) {
            $query->where('project_client', $filters['client']);
        }

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        $allProjects = $query->orderBy('project_id')->get();

        // Hitung realisasi per project_id
        $realisasiSums = Realisasi::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total_realisasi'))
            ->pluck('total_realisasi', 'project_id');

        $invoiceSums = \App\Models\Invoice::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(subtotal) as total_inv'))
            ->pluck('total_inv', 'project_id');

        $prognosaSums = Prognosa::groupBy('project_id')
            ->select('project_id', DB::raw('SUM(prognosa_biaya) as total_prognosa'))
            ->pluck('total_prognosa', 'project_id');

        // Kalkulasi baris data & status kesehatan
        $projectRows = [];
        $totalPaguAll = 0;
        $totalRealAll = 0;
        $healthCounts = [
            'safe'     => 0,
            'warning'  => 0,
            'critical' => 0,
        ];

        foreach ($allProjects as $p) {
            $pagu = (float) $p->project_value;
            $real = (float) ($realisasiSums[$p->project_id] ?? 0);
            if ($real <= 0 && isset($invoiceSums[$p->project_id])) {
                $real = (float) $invoiceSums[$p->project_id];
            }
            $prog = (float) ($prognosaSums[$p->project_id] ?? 0);
            $sisa = max(0, $pagu - $real);
            $pct  = $pagu > 0 ? round(($real / $pagu) * 100, 1) : 0;

            if ($pct >= 90) {
                $status = 'critical';
                $statusLabel = 'Kritis (>90%)';
            } elseif ($pct >= 75) {
                $status = 'warning';
                $statusLabel = 'Waspada (75-90%)';
            } else {
                $status = 'safe';
                $statusLabel = 'Sehat (<75%)';
            }

            // Filter status kesehatan jika dipilih
            if (!empty($filters['health_status']) && $filters['health_status'] !== $status) {
                continue;
            }

            $healthCounts[$status]++;
            $totalPaguAll += $pagu;
            $totalRealAll += $real;

            $projectRows[] = [
                'project_id'       => $p->project_id,
                'project_name'     => $p->project_name,
                'client'           => $p->project_client ?: '-',
                'service_manager'  => $p->service_manager ?: '-',
                'tahun'            => $p->tahun,
                'pagu'             => $pagu,
                'realisasi'        => $real,
                'prognosa'         => $prog,
                'sisa'             => $sisa,
                'serapan_pct'      => $pct,
                'status'           => $status,
                'status_label'     => $statusLabel,
            ];
        }

        // Urutkan default: Kritis dulu, lalu Waspada, lalu Sehat
        usort($projectRows, function($a, $b) {
            $priority = ['critical' => 1, 'warning' => 2, 'safe' => 3];
            return ($priority[$a['status']] <=> $priority[$b['status']]) ?: ($b['serapan_pct'] <=> $a['serapan_pct']);
        });

        $totalSisaAll = max(0, $totalPaguAll - $totalRealAll);
        $totalSerapanPct = $totalPaguAll > 0 ? round(($totalRealAll / $totalPaguAll) * 100, 1) : 0;

        // Dropdown data
        $smList = Kontrak::distinct()->whereNotNull('service_manager')->where('service_manager', '!=', '')->orderBy('service_manager')->pluck('service_manager');
        $clientList = Kontrak::distinct()->whereNotNull('project_client')->where('project_client', '!=', '')->orderBy('project_client')->pluck('project_client');
        $tahunList = Kontrak::distinct()->whereNotNull('tahun')->orderBy('tahun', 'desc')->pluck('tahun');

        return view('monitoring.cost_kontrak', compact(
            'projectRows', 'filters', 'totalPaguAll', 'totalRealAll', 'totalSisaAll', 'totalSerapanPct',
            'healthCounts', 'smList', 'clientList', 'tahunList'
        ));
    }
}
