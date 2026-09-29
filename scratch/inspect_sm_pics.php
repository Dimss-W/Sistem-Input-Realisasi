<?php
use App\Models\Kontrak;
use App\Models\Realisasi;

echo "=== DISTINCT SERVICE MANAGERS IN KONTRAK ===\n";
$sms = Kontrak::distinct()->orderBy('service_manager')->pluck('service_manager');
foreach ($sms as $sm) {
    echo "- '$sm'\n";
}

echo "\n=== DISTINCT PICS IN REALISASI ===\n";
$pics = Realisasi::distinct()->orderBy('pic')->pluck('pic');
foreach ($pics as $pic) {
    echo "- '$pic'\n";
}
