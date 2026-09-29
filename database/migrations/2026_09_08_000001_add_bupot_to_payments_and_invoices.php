<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'bupot_number')) {
                $table->string('bupot_number', 100)->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('payments', 'pph23_amount')) {
                $table->decimal('pph23_amount', 20, 2)->default(0)->after('payment_amount');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'bupot_number')) {
                $table->string('bupot_number', 100)->nullable()->after('finance_user_id');
            }
            if (!Schema::hasColumn('invoices', 'pph23_deducted')) {
                $table->decimal('pph23_deducted', 20, 2)->default(0)->after('payment_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'bupot_number')) {
                $table->dropColumn('bupot_number');
            }
            if (Schema::hasColumn('payments', 'pph23_amount')) {
                $table->dropColumn('pph23_amount');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'bupot_number')) {
                $table->dropColumn('bupot_number');
            }
            if (Schema::hasColumn('invoices', 'pph23_deducted')) {
                $table->dropColumn('pph23_deducted');
            }
        });
    }
};
