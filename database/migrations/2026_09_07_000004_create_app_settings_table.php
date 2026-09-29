<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Seed default official signers & enterprise identity
        $defaults = [
            [
                'key'         => 'signer_vp_name',
                'value'       => 'Dedi Suherman, S.T., M.M.',
                'group'       => 'signers',
                'description' => 'Nama Pejabat VP / Management Penandatangan Akhir',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'signer_vp_title',
                'value'       => 'VP Information Technology & Project Management',
                'group'       => 'signers',
                'description' => 'Jabatan Resmi VP Penandatangan Akhir',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'signer_vp_nip',
                'value'       => 'Pekerja: 78912044',
                'group'       => 'signers',
                'description' => 'NIP / No. Pekerja VP',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'signer_qc_lead_name',
                'value'       => 'Ahmad Fauzi, S.T.',
                'group'       => 'signers',
                'description' => 'Nama Lead Quality Control (QC Lead)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'signer_qc_lead_title',
                'value'       => 'Lead Quality Control & Technical Assurance',
                'group'       => 'signers',
                'description' => 'Jabatan Resmi Lead QC',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'signer_qc_lead_nip',
                'value'       => 'Pekerja: 89014522',
                'group'       => 'signers',
                'description' => 'NIP / No. Pekerja Lead QC',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'company_name',
                'value'       => 'PT Pertamina Gas Negara Tbk',
                'group'       => 'organization',
                'description' => 'Nama Entitas Perusahaan Resmi',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'division_name',
                'value'       => 'Operation & Service Management (OSM)',
                'group'       => 'organization',
                'description' => 'Nama Divisi / Satuan Kerja Utama',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('app_settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
