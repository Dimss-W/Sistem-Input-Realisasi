<?php

use App\Models\Realisasi;
use App\Models\Kontrak;
use App\Models\Prognosa;

echo "=== KONTRAK COLUMNS ===" . PHP_EOL;
$k = Kontrak::first();
if ($k) {
    $cols = array_keys($k->toArray());
    foreach ($cols as $col) {
        echo "  - {$col}: " . $k->$col . PHP_EOL;
    }
}

echo PHP_EOL . "=== REALISASI STATS ===" . PHP_EOL;
echo "Count: " . Realisasi::count() . PHP_EOL;
echo "Total: Rp " . number_format(Realisasi::sum('realisasi_biaya_final'), 0, ',', '.') . PHP_EOL;

echo PHP_EOL . "=== SAMPLE REALISASI COLUMNS ===" . PHP_EOL;
$r = Realisasi::first();
if ($r) {
    foreach (array_keys($r->toArray()) as $col) {
        echo "  - {$col}: " . $r->$col . PHP_EOL;
    }
}

echo PHP_EOL . "=== PROGNOSA COLUMNS ===" . PHP_EOL;
try {
    $p = Prognosa::first();
    if ($p) {
        foreach (array_keys($p->toArray()) as $col) {
            echo "  - {$col}: " . $p->$col . PHP_EOL;
        }
    } else {
        echo "  No prognosa data yet" . PHP_EOL;
        $pm = new Prognosa();
        echo "  Fillable: " . implode(', ', $pm->getFillable()) . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "  Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== TOP PROJECTS BY REALISASI ===" . PHP_EOL;
$byProject = Realisasi::select('project_id', 'project_name')
    ->selectRaw('SUM(realisasi_biaya_final) as total_realisasi')
    ->selectRaw('COUNT(*) as cnt')
    ->groupBy('project_id', 'project_name')
    ->orderByDesc('total_realisasi')
    ->limit(5)
    ->get();

foreach ($byProject as $p) {
    echo "  {$p->project_id}: Rp " . number_format($p->total_realisasi, 0, ',', '.') . " ({$p->cnt} rows)" . PHP_EOL;
}

echo PHP_EOL . "=== REALISASI BY PERIODE (2026) ===" . PHP_EOL;
$byPeriode = Realisasi::select('periode')
    ->selectRaw('SUM(realisasi_biaya_final) as total')
    ->where('tahun', 2026)
    ->groupBy('periode')
    ->get();
foreach ($byPeriode as $p) {
    echo "  {$p->periode}: Rp " . number_format($p->total, 0, ',', '.') . PHP_EOL;
}

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
