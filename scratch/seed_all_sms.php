<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$sms = [
    ['name' => 'GILANG', 'username' => 'gilang'],
    ['name' => 'ISPD-JIHAD', 'username' => 'jihad'],
    ['name' => 'JODY', 'username' => 'jody'],
    ['name' => 'RIKI', 'username' => 'riki'],
    ['name' => 'RIZKY', 'username' => 'rizky'],
    ['name' => 'SANDI', 'username' => 'sandi'],
    ['name' => 'SUKMA', 'username' => 'sukma'],
    ['name' => 'TAUFAN', 'username' => 'taufan'],
    ['name' => 'TITA', 'username' => 'tita'],
    ['name' => 'YIYIP', 'username' => 'yiyip'],
    ['name' => 'AHMAD', 'username' => 'ahmad'],
];

echo "=== SEEDING ALL SERVICE MANAGERS ===\n";

foreach ($sms as $sm) {
    $existing = User::where('username', $sm['username'])->first();
    if ($existing) {
        echo "User '{$sm['username']}' already exists. Skipping.\n";
    } else {
        User::create([
            'name' => $sm['name'],
            'username' => $sm['username'],
            'email' => $sm['username'] . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'osm_service_manager',
            'status' => 'active',
        ]);
        echo "Created User '{$sm['username']}' with name '{$sm['name']}'.\n";
    }
}

echo "=== SEEDING COMPLETED ===\n";
