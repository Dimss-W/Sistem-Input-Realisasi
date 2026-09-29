<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodLock;
use App\Models\ActivityLog;

class PeriodLockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $selectedYear = (int) $request->input('tahun', date('Y'));
        $years = range(date('Y') + 1, date('Y') - 3);

        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $semesters = ['SEMESTER 1', 'SEMESTER 2'];
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $locks = PeriodLock::where('tahun', $selectedYear)
            ->get()
            ->keyBy('periode');

        $totalLocked = PeriodLock::where('tahun', $selectedYear)
            ->where('is_locked', true)
            ->count();

        return view('admin.period_locks.index', compact(
            'selectedYear',
            'years',
            'quarters',
            'semesters',
            'months',
            'locks',
            'totalLocked'
        ));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'tahun'   => 'required|integer',
            'periode' => 'required|string',
            'notes'   => 'nullable|string|max:255',
        ]);

        $tahun = (int) $request->input('tahun');
        $periode = trim($request->input('periode'));

        $lock = PeriodLock::firstOrNew([
            'tahun'   => $tahun,
            'periode' => $periode,
        ]);

        $newStatus = !$lock->is_locked;
        $lock->is_locked = $newStatus;
        $lock->locked_by = auth()->id();
        $lock->locked_at = $newStatus ? now() : null;
        if ($request->filled('notes')) {
            $lock->notes = $request->input('notes');
        }
        $lock->save();

        // Audit Log
        ActivityLog::log(
            $newStatus ? 'LOCK_PERIOD' : 'UNLOCK_PERIOD',
            'PeriodLock',
            (string) $lock->id,
            ['is_locked' => !$newStatus],
            ['is_locked' => $newStatus],
            "Admin " . auth()->user()->name . ($newStatus ? " mengunci" : " membuka kunci") . " periode {$periode} {$tahun}"
        );

        $msg = $newStatus 
            ? "Periode {$periode} {$tahun} berhasil DIKUNCI. Non-admin tidak dapat menambah/mengedit transaksi pada periode ini."
            : "Periode {$periode} {$tahun} berhasil DIBUKA. Transaksi dapat diubah kembali.";

        if ($request->ajax()) {
            return response()->json([
                'success'   => true,
                'is_locked' => $newStatus,
                'message'   => $msg
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
