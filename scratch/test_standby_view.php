<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\StandbyController();
$view = $controller->index();
$html = $view->render();

echo "VIEW RENDERED! LENGTH: " . strlen($html) . " bytes\n";
if (str_contains($html, 'PGN MONITORING REALISASI & ANGGARAN')) {
    echo "Header verified!\n";
}
if (str_contains($html, 'liveClockDisplay')) {
    echo "Live clock element verified!\n";
}
if (str_contains($html, 'btnToggleFullscreen')) {
    echo "Fullscreen button verified!\n";
}
if (str_contains($html, 'monthlyTrendChart')) {
    echo "Chart container verified!\n";
}
