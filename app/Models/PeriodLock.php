<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodLock extends Model
{
    protected $table = 'period_locks';

    protected $fillable = [
        'tahun',
        'periode',
        'is_locked',
        'locked_by',
        'locked_at',
        'notes',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Cek apakah suatu periode pada tahun tertentu sedang dikunci.
     */
    public static function isPeriodLocked($tahun, $periode): bool
    {
        if (empty($tahun) || empty($periode)) {
            return false;
        }

        // Cek lock spesifik periode (e.g. Q1, Q2, atau Bulan)
        $lock = self::where('tahun', $tahun)
            ->where(function ($q) use ($periode) {
                $q->where('periode', $periode)
                  ->orWhere('periode', strtoupper($periode));
            })
            ->where('is_locked', true)
            ->first();

        if ($lock) {
            return true;
        }

        // Jika periode adalah bulan, cek juga apakah Kuartal atau Semesternya dikunci
        $bulanMap = [
            'Januari' => ['Q1', 'SEMESTER 1'],
            'Februari' => ['Q1', 'SEMESTER 1'],
            'Maret' => ['Q1', 'SEMESTER 1'],
            'April' => ['Q2', 'SEMESTER 1'],
            'Mei' => ['Q2', 'SEMESTER 1'],
            'Juni' => ['Q2', 'SEMESTER 1'],
            'Juli' => ['Q3', 'SEMESTER 2'],
            'Agustus' => ['Q3', 'SEMESTER 2'],
            'September' => ['Q3', 'SEMESTER 2'],
            'Oktober' => ['Q4', 'SEMESTER 2'],
            'November' => ['Q4', 'SEMESTER 2'],
            'Desember' => ['Q4', 'SEMESTER 2'],
        ];

        $capitalBulan = ucfirst(strtolower(trim($periode)));
        if (isset($bulanMap[$capitalBulan])) {
            $parentPeriods = $bulanMap[$capitalBulan];
            $parentLock = self::where('tahun', $tahun)
                ->whereIn('periode', $parentPeriods)
                ->where('is_locked', true)
                ->exists();

            if ($parentLock) {
                return true;
            }
        }

        return false;
    }
}
