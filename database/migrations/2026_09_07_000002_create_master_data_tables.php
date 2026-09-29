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
        // Master Vendors
        Schema::create('master_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('kode_vendor', 50)->nullable()->unique();
            $table->string('nama_vendor', 191)->unique();
            $table->string('pic_vendor', 100)->nullable();
            $table->string('kontak', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master Clients
        Schema::create('master_clients', function (Blueprint $table) {
            $table->id();
            $table->string('kode_client', 50)->nullable()->unique();
            $table->string('nama_client', 191)->unique();
            $table->string('kategori', 100)->nullable(); // e.g. Subholding Gas, Eksternal
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_clients');
        Schema::dropIfExists('master_vendors');
    }
};
