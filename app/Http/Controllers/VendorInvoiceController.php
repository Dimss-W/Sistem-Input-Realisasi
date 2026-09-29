<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Realisasi;
use App\Models\Prognosa;
use App\Models\Kontrak;
use Illuminate\Support\Facades\DB;

class VendorInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,dmo,procurement');
    }

    /**
     * Tampilkan Halaman Monitoring Invoice Vendor (Prognosa vs Actual).
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'vendor', 'periode', 'status', 'project_id', 'tahun', 'risk_aging']);

        // Tanggal batas ambang 90 hari (3 bulan)
        $thresholdDate = now()->subDays(90);
        $currYear = $thresholdDate->year;
        $currMonth = $thresholdDate->month;
        $monthsBefore = [];
        foreach (Realisasi::$periodeOrder as $mName => $mNum) {
            if ($mNum <= $currMonth) {
                $monthsBefore[] = $mName;
            }
        }

        // Query Tagihan Vendor (Realisasi)
        $realisasiQuery = Realisasi::query();

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $realisasiQuery->where(function($q) use ($s) {
                $q->where('vendor', 'like', "%{$s}%")
                  ->orWhere('item_biaya', 'like', "%{$s}%")
                  ->orWhere('project_id', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['vendor'])) {
            $realisasiQuery->where('vendor', $filters['vendor']);
        }

        if (!empty($filters['periode'])) {
            $realisasiQuery->where('periode', $filters['periode']);
        }

        if (!empty($filters['status'])) {
            $realisasiQuery->where('status', $filters['status']);
        }

        if (!empty($filters['project_id'])) {
            $realisasiQuery->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['tahun'])) {
            $realisasiQuery->where('tahun', $filters['tahun']);
        }

        // Filter Khusus: Tagihan Risiko Hangus (> 90 Hari)
        if (!empty($filters['risk_aging']) && $filters['risk_aging'] == '1') {
            $realisasiQuery->where('status', '!=', 'PAID')
                ->where(function($q) use ($currYear, $monthsBefore) {
                    $q->where('tahun', '<', $currYear);
                    if (!empty($monthsBefore)) {
                        $q->orWhere(function($sub) use ($currYear, $monthsBefore) {
                            $sub->where('tahun', $currYear)
                                ->whereIn('periode', $monthsBefore);
                        });
                    }
                });
        }

        // Ambil data tagihan vendor aktual dengan pagination praktis (atau seluruh data jika sedang cetak)
        $perPage = ($request->boolean('print') || $request->get('per_page') === 'all') ? 10000 : 20;
        $vendorInvoices = (clone $realisasiQuery)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER') DESC")
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 1. Ringkasan Finansial Tagihan Vendor
        $totalActualSpend = (clone $realisasiQuery)->sum('realisasi_biaya_final');
        $totalPaidSpend   = (clone $realisasiQuery)->where('status', 'PAID')->sum('realisasi_biaya_final');
        $totalUnpaidSpend = (clone $realisasiQuery)->whereIn('status', ['UNPAID', 'WAIT INV', 'PROSES', 'PENDING'])->sum('realisasi_biaya_final');
        $totalWaitInvCount= (clone $realisasiQuery)->whereIn('status', ['WAIT INV', 'PROSES'])->count();

        // Metrik Peringatan Risiko Tagihan Hangus (> 90 Hari)
        $overdueQuery = Realisasi::where('status', '!=', 'PAID')
            ->where(function($q) use ($currYear, $monthsBefore) {
                $q->where('tahun', '<', $currYear);
                if (!empty($monthsBefore)) {
                    $q->orWhere(function($sub) use ($currYear, $monthsBefore) {
                        $sub->where('tahun', $currYear)
                            ->whereIn('periode', $monthsBefore);
                    });
                }
            });
        $totalOverdueRiskCount  = (clone $overdueQuery)->count();
        $totalOverdueRiskAmount = (float) (clone $overdueQuery)->sum('realisasi_biaya_final');

        // 2. Query Prognosa Terkait
        $prognosaQuery = Prognosa::query();
        if (!empty($filters['vendor'])) {
            $prognosaQuery->where('partner', $filters['vendor']);
        }
        if (!empty($filters['periode'])) {
            $prognosaQuery->where('periode', $filters['periode']);
        }
        if (!empty($filters['project_id'])) {
            $prognosaQuery->where('project_id', $filters['project_id']);
        }
        $totalPrognosaBudget = (clone $prognosaQuery)->sum('prognosa_biaya');

        // Deviasi (Prognosa - Actual): Positif = Hemat, Negatif = Over-budget
        $variance = $totalPrognosaBudget - $totalActualSpend;
        $variancePct = $totalPrognosaBudget > 0 ? round(($variance / $totalPrognosaBudget) * 100, 1) : 0;

        // 3. Matriks Rekap per Vendor (Top 8 Vendor Terbesar)
        $vendorSummaries = Realisasi::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->select('vendor', DB::raw('SUM(realisasi_biaya_final) as total_actual'), DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('vendor')
            ->orderBy('total_actual', 'desc')
            ->limit(8)
            ->get();

        // Dropdown data
        $vendorList = Realisasi::distinct()->whereNotNull('vendor')->where('vendor', '!=', '')->orderBy('vendor')->pluck('vendor');
        $projectList = Realisasi::distinct()->whereNotNull('project_id')->where('project_id', '!=', '')->orderBy('project_id')->pluck('project_id');
        $periodeList = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
        $statusList  = ['PAID', 'UNPAID', 'WAIT INV', 'PROSES', 'PENDING', 'CANCEL'];
        $tahunList   = Realisasi::distinct()->whereNotNull('tahun')->orderBy('tahun', 'desc')->pluck('tahun');

        return view('monitoring.invoice_vendor', compact(
            'vendorInvoices', 'filters', 'totalActualSpend', 'totalPaidSpend', 'totalUnpaidSpend',
            'totalWaitInvCount', 'totalOverdueRiskCount', 'totalOverdueRiskAmount',
            'totalPrognosaBudget', 'variance', 'variancePct',
            'vendorSummaries', 'vendorList', 'projectList', 'periodeList', 'statusList', 'tahunList'
        ));
    }

    /**
     * Aksi Cepat Update Status Pembayaran Tagihan Vendor (Khusus Admin/DMO).
     * Mencegah re-upload Excel dan mencegah potensi duplikasi data.
     */
    public function quickUpdateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:PAID,UNPAID,WAIT INV,PROSES,PENDING,CANCEL',
        ]);

        $realisasi = Realisasi::findOrFail($id);
        $oldStatus = $realisasi->status;
        $newStatus = strtoupper($validated['status']);

        $realisasi->update(['status' => $newStatus]);

        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'UPDATE',
                'module'      => 'realisasi',
                'record_id'   => (string) $realisasi->id,
                'description' => "Admin (" . auth()->user()->name . ") mengubah status tagihan [{$realisasi->vendor} - {$realisasi->item_biaya}] dari {$oldStatus} ke {$newStatus}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status tagihan berhasil diperbarui menjadi {$newStatus}.",
                'status'  => $newStatus,
            ]);
        }

        return redirect()->back()->with('success', "Status tagihan vendor [{$realisasi->vendor}] berhasil diperbarui menjadi {$newStatus}.");
    }
}
