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
        // 1. Tambah field TOP dan kalkulasi jatuh tempo pada tabel purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'term_of_payment')) {
                $table->integer('term_of_payment')->default(30)->after('po_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_due_date')) {
                $table->date('payment_due_date')->nullable()->after('term_of_payment');
            }
            if (!Schema::hasColumn('purchase_orders', 'prognosa_periode')) {
                $table->string('prognosa_periode', 50)->nullable()->after('payment_due_date');
            }
            if (!Schema::hasColumn('purchase_orders', 'prognosa_tahun')) {
                $table->integer('prognosa_tahun')->nullable()->after('prognosa_periode');
            }
        });

        // 2. Tambah field sumber data & integrasi PO pada tabel prognosa
        Schema::table('prognosa', function (Blueprint $table) {
            if (!Schema::hasColumn('prognosa', 'data_source')) {
                $table->string('data_source', 50)->default('sm_manual')->after('keterangan');
            }
            if (!Schema::hasColumn('prognosa', 'po_id')) {
                $table->unsignedBigInteger('po_id')->nullable()->index()->after('data_source');
            }
            if (!Schema::hasColumn('prognosa', 'is_overridden')) {
                $table->boolean('is_overridden')->default(false)->after('po_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $cols = ['term_of_payment', 'payment_due_date', 'prognosa_periode', 'prognosa_tahun'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('purchase_orders', $c)) {
                    $table->dropColumn($c);
                }
            }
        });

        Schema::table('prognosa', function (Blueprint $table) {
            $cols = ['data_source', 'po_id', 'is_overridden'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('prognosa', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
