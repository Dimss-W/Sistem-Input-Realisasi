<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── invoices ─────────────────────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100)->unique();
            $table->string('project_id', 100)->index()->comment('FK soft ke kontrak.project_id');
            $table->text('project_name')->nullable();
            $table->string('customer', 255)->nullable();
            $table->unsignedBigInteger('sales_user_id')->nullable()->index();
            $table->unsignedBigInteger('finance_user_id')->nullable()->index();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('invoice_amount', 20, 2)->default(0);
            $table->decimal('payment_amount', 20, 2)->default(0);
            $table->decimal('outstanding', 20, 2)->storedAs('invoice_amount - payment_amount');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── payments ──────────────────────────────────────────────────────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->date('payment_date');
            $table->decimal('payment_amount', 20, 2);
            $table->string('payment_reference', 255)->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->unsignedBigInteger('processed_by_user_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        // ── bastos ────────────────────────────────────────────────────────────
        Schema::create('bastos', function (Blueprint $table) {
            $table->id();
            $table->string('basto_number', 100)->nullable()->unique();
            $table->string('project_id', 100)->index();
            $table->text('project_name')->nullable();
            $table->unsignedBigInteger('dmo_user_id')->nullable()->index();
            $table->unsignedBigInteger('sm_user_id')->nullable()->index();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── import_logs ───────────────────────────────────────────────────────
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('file_name', 255);
            $table->string('file_path', 500)->nullable();
            $table->enum('type', ['realisasi', 'payment', 'prognosa'])->default('realisasi');
            $table->integer('total_rows')->default(0);
            $table->integer('success_rows')->default(0);
            $table->integer('error_rows')->default(0);
            $table->json('errors_json')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });

        // ── activity_logs ─────────────────────────────────────────────────────
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_role', 50)->nullable();
            $table->string('action', 50)->index()
                  ->comment('CREATE,UPDATE,DELETE,LOGIN,LOGOUT,APPROVE,REJECT,IMPORT,EXPORT');
            $table->string('module', 100)->index()
                  ->comment('realisasi,invoice,payment,basto,user,kontrak');
            $table->string('record_id', 100)->nullable()->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['module', 'action']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('bastos');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
