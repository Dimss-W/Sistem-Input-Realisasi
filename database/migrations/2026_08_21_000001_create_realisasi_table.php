<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();

            // Import tracking
            $table->bigInteger('source_row')->nullable()->comment('Nomor baris dari file Excel sumber');

            // Unique identifier untuk UPSERT (bukan unique index, hanya referensi)
            $table->string('record_key', 64)->nullable()->index()->comment('SHA-256 hash dari kombinasi field transaksi');

            // Identitas Project
            $table->string('project_id', 100)->index()->comment('ID Project, BUKAN unique — satu project punya banyak transaksi');
            $table->text('project_name')->comment('Nama lengkap project');

            // Detail Transaksi
            $table->string('item_biaya', 255)->nullable();
            $table->string('satuan_kerja', 100)->nullable();
            $table->string('pic', 150)->nullable()->index();
            $table->string('periode', 30)->nullable()->index()->comment('Nama bulan: JANUARI, FEBRUARI, dst');

            // Data Keuangan
            $table->decimal('realisasi_biaya_original', 20, 2)->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->decimal('realisasi_biaya_idr', 20, 2)->nullable();

            // Status & Identifikasi
            $table->string('status', 50)->nullable()->index();
            $table->string('vendor', 255)->nullable()->index();
            $table->string('sifat', 50)->nullable();

            // Evidence & Flag
            $table->text('link_evidence')->nullable();
            $table->year('tahun')->nullable()->index();
            $table->string('data_flag', 100)->nullable();

            // Nilai Final
            $table->decimal('realisasi_biaya_final', 20, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk performa filter
            $table->index(['project_id', 'periode', 'tahun'], 'idx_project_periode_tahun');
            $table->index(['status', 'tahun'], 'idx_status_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi');
    }
};
