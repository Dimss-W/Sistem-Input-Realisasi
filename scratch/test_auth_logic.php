<?php

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING AUTH & RBAC TEST ===" . PHP_EOL;

// 1. Test Login Operator
$credentials = ['username' => 'operator', 'password' => 'password'];
if (Auth::attempt($credentials)) {
    $user = Auth::user();
    echo "✔ Success: Logged in as {$user->name}." . PHP_EOL;
    echo "✔ Role check: {$user->role} (Expected: operator)" . PHP_EOL;
    
    // Log activity
    ActivityLog::log('LOGIN', 'user', $user->id, null, null, 'User operator logged in via test script');
    echo "✔ Log created in activity_logs." . PHP_EOL;
    
    Auth::logout();
    echo "✔ Logged out." . PHP_EOL;
} else {
    echo "❌ Error: Failed to login as operator." . PHP_EOL;
}

// 2. Test Login Service Manager
$credentials = ['username' => 'gilang', 'password' => 'password'];
if (Auth::attempt($credentials)) {
    $user = Auth::user();
    echo "✔ Success: Logged in as {$user->name}." . PHP_EOL;
    echo "✔ Role check: {$user->role} (Expected: osm_service_manager)" . PHP_EOL;
    
    // Check resolve filter SM
    $filterSM = $user->name;
    $isSM = \App\Models\Kontrak::where('service_manager', $filterSM)->exists();
    echo "✔ SM check in database: " . ($isSM ? "Yes (GILANG found in kontrak)" : "No") . PHP_EOL;
    
    Auth::logout();
    echo "✔ Logged out." . PHP_EOL;
} else {
    echo "❌ Error: Failed to login as gilang." . PHP_EOL;
}

// 3. Test Activity Log count
$count = ActivityLog::count();
echo "✔ Total activity logs in database: {$count}" . PHP_EOL;

echo "=== AUTH & RBAC TEST COMPLETED ===" . PHP_EOL;
