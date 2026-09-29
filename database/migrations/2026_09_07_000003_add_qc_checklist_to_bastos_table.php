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
        Schema::table('bastos', function (Blueprint $table) {
            if (!Schema::hasColumn('bastos', 'qc_checklist')) {
                $table->json('qc_checklist')->nullable()->after('qc_notes');
            }
            if (!Schema::hasColumn('bastos', 'qc_verification_code')) {
                $table->string('qc_verification_code', 100)->nullable()->unique()->after('qc_checklist');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bastos', function (Blueprint $table) {
            if (Schema::hasColumn('bastos', 'qc_verification_code')) {
                $table->dropColumn('qc_verification_code');
            }
            if (Schema::hasColumn('bastos', 'qc_checklist')) {
                $table->dropColumn('qc_checklist');
            }
        });
    }
};
