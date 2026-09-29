<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== MEMPERBARUI USER & ROLE SISTEM ===\n";

// 1. Hapus role Sales dan Finance
$deleted = User::whereIn('role', ['sales', 'finance'])->delete();
echo "Berhasil menghapus {$deleted} akun role Sales & Finance.\n";

// 2. Buat atau update akun QC 2
$qc2 = User::where('username', 'qc2')->first();
if (!$qc2) {
    $qc2 = User::create([
        'name'     => 'QC Inspector 2',
        'username' => 'qc2',
        'email'    => 'qc2@pgncom.co.id',
        'password' => Hash::make('password123'),
        'role'     => 'osm_qc',
        'status'   => 'active',
    ]);
    echo "Berhasil membuat akun baru: QC Inspector 2 (username: qc2, role: osm_qc)\n";
} else {
    $qc2->update([
        'role'   => 'osm_qc',
        'status' => 'active',
    ]);
    echo "Akun qc2 sudah ada dan dipastikan aktif.\n";
}

// 3. Buat atau update akun Procurement
$procurement = User::where('username', 'procurement')->first();
if (!$procurement) {
    $procurement = User::create([
        'name'     => 'Procurement Officer',
        'username' => 'procurement',
        'email'    => 'procurement@pgncom.co.id',
        'password' => Hash::make('password123'),
        'role'     => 'procurement',
        'status'   => 'active',
    ]);
    echo "Berhasil membuat akun baru: Procurement Officer (username: procurement, role: procurement)\n";
} else {
    $procurement->update([
        'role'   => 'procurement',
        'status' => 'active',
    ]);
    echo "Akun procurement sudah ada dan dipastikan aktif.\n";
}

// 4. Tampilkan seluruh user aktif saat ini
echo "\nDAFTAR USER AKTIF SAAT INI:\n";
$users = User::select('id', 'name', 'username', 'role', 'status')->get();
foreach ($users as $u) {
    echo " - [ID {$u->id}] {$u->name} | Username: {$u->username} | Role: {$u->role}\n";
}
echo "OK!\n";
