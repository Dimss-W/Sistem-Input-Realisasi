<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Users Table Enhancements ──────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // ── 2. Invoices Table Enhancements ───────────────────────────────────
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('invoices', 'tax_ppn_percent')) {
                $table->decimal('tax_ppn_percent', 5, 2)->default(11.00)->after('subtotal');
            }
            if (!Schema::hasColumn('invoices', 'tax_ppn_amount')) {
                $table->decimal('tax_ppn_amount', 15, 2)->default(0)->after('tax_ppn_percent');
            }
            if (!Schema::hasColumn('invoices', 'tax_pph_percent')) {
                $table->decimal('tax_pph_percent', 5, 2)->default(0.00)->after('tax_ppn_amount');
            }
            if (!Schema::hasColumn('invoices', 'tax_pph_amount')) {
                $table->decimal('tax_pph_amount', 15, 2)->default(0)->after('tax_pph_percent');
            }
            if (!Schema::hasColumn('invoices', 'total_after_tax')) {
                $table->decimal('total_after_tax', 15, 2)->nullable()->after('tax_pph_amount');
            }
            if (!Schema::hasColumn('invoices', 'sales_followup_notes')) {
                $table->text('sales_followup_notes')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('invoices', 'last_followup_at')) {
                $table->timestamp('last_followup_at')->nullable()->after('sales_followup_notes');
            }
        });

        // ── 3. Bastos Table Enhancements ─────────────────────────────────────
        Schema::table('bastos', function (Blueprint $table) {
            if (!Schema::hasColumn('bastos', 'attachment_file')) {
                $table->string('attachment_file', 255)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('bastos', 'qc_user_id')) {
                $table->unsignedBigInteger('qc_user_id')->nullable()->index()->after('attachment_file');
            }
            if (!Schema::hasColumn('bastos', 'qc_status')) {
                $table->enum('qc_status', ['pending', 'verified', 'revision_needed'])->default('pending')->index()->after('qc_user_id');
            }
            if (!Schema::hasColumn('bastos', 'qc_notes')) {
                $table->text('qc_notes')->nullable()->after('qc_status');
            }
            if (!Schema::hasColumn('bastos', 'qc_verified_at')) {
                $table->timestamp('qc_verified_at')->nullable()->after('qc_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bastos', function (Blueprint $table) {
            $table->dropColumn(['attachment_file', 'qc_user_id', 'qc_status', 'qc_notes', 'qc_verified_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 'tax_ppn_percent', 'tax_ppn_amount',
                'tax_pph_percent', 'tax_pph_amount', 'total_after_tax',
                'sales_followup_notes', 'last_followup_at'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
