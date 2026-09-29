<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prognosa', function (Blueprint $table) {
            $table->id();
            $table->integer('source_row')->nullable();
            $table->string('project_id', 100)->index();
            $table->string('project_name', 1000)->nullable();
            $table->string('activity', 255)->nullable();
            $table->string('satuan_kerja', 100)->nullable();
            $table->string('pic', 150)->nullable();
            $table->decimal('prognosa_biaya', 20, 2)->nullable();
            $table->string('periode', 50)->nullable()->index();
            $table->string('partner', 500)->nullable();
            $table->text('keterangan')->nullable();
            $table->year('tahun_original')->nullable();
            $table->year('tahun_normalized')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prognosa');
    }
};
