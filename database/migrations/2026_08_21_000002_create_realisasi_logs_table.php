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
        Schema::create('realisasi_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('realisasi_id')->nullable()->index();
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE', 'IMPORT'])->index();
            $table->json('old_data')->nullable()->comment('Data sebelum perubahan');
            $table->json('new_data')->nullable()->comment('Data setelah perubahan');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            // FK opsional — tidak cascaded agar log tetap ada meski data dihapus
            // $table->foreign('realisasi_id')->references('id')->on('realisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_logs');
    }
};
