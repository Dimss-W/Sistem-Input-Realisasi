<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom role menjadi VARCHAR(50) agar mendukung role baru 'procurement' tanpa batasan enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'dmo'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'osm_service_manager', 'osm_qc', 'dmo', 'procurement', 'sales', 'finance') NOT NULL DEFAULT 'dmo'");
    }
};
