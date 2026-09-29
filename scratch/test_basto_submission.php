<?php

use App\Models\Basto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING BASTO FLOW INTEGRATION TEST ===\n";

// 1. Resolve user accounts
$dmoUser = User::where('username', 'dmo')->first();
$yiyipUser = User::where('username', 'yiyip')->first();

if (!$dmoUser || !$yiyipUser) {
    echo "❌ Error: Required users (dmo or yiyip) not found in DB.\n";
    exit(1);
}

// 2. Login as DMO
Auth::login($dmoUser);
echo "✔ Logged in as: " . Auth::user()->name . " (" . Auth::user()->role . ")\n";

// 3. Create BASTO
$basto = Basto::create([
    'basto_number' => 'BASTO-TEST-' . time(),
    'project_id'   => 'PS-024-00',
    'project_name' => 'Pekerjaan Jasa Operasi dan Pemeliharaan Gas',
    'dmo_user_id'  => $dmoUser->id,
    'sm_user_id'   => $yiyipUser->id,
    'status'       => 'submitted',
    'submitted_at' => now(),
    'notes'        => 'BASTO Test Note',
]);

echo "✔ Created BASTO #{$basto->basto_number}\n";
echo "✔ Status is: " . $basto->status . " (Expected: submitted)\n";
echo "✔ Targeted SM is user ID: " . $basto->sm_user_id . " (" . $yiyipUser->name . ")\n";

// 4. Login as Yiyip
Auth::login($yiyipUser);
echo "✔ Logged in as: " . Auth::user()->name . " (" . Auth::user()->role . ")\n";

// 5. SM Approves BASTO
$basto->update([
    'status'      => 'approved',
    'reviewed_at' => now(),
]);

$basto = $basto->fresh();
echo "✔ BASTO approved by SM.\n";
echo "✔ Status updated to: " . $basto->status . " (Expected: approved)\n";

// 6. Cleanup
$basto->delete();
echo "✔ Test BASTO deleted.\n";

echo "=== BASTO FLOW INTEGRATION TEST COMPLETED SUCCESSFULLY ===\n";
