<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\StandbyController();
$res = $controller->getData();
$data = $res->getData(true);

echo "STATUS: " . $data['status'] . "\n";
echo "PAGU: Rp " . number_format($data['data']['summary']['total_pagu'], 0, ',', '.') . "\n";
echo "REALISASI: Rp " . number_format($data['data']['summary']['total_realisasi'], 0, ',', '.') . "\n";
echo "TOP PROJECTS: " . count($data['data']['top_projects']) . "\n";
foreach ($data['data']['top_projects'] as $tp) {
    echo " - " . $tp['project_id'] . " (" . $tp['project_name'] . "): " . $tp['serapan_pct'] . "% [" . $tp['status_label'] . "]\n";
}
echo "RECENT ACTIVITIES: " . count($data['data']['recent_activities']) . "\n";
echo "OK!\n";
