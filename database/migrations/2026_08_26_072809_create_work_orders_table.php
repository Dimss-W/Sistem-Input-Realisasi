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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 50)->unique()->comment('Nomor WO unik, format: WO-YYYYMM-XXXX');
            $table->string('project_id', 50)->index();
            $table->string('project_name', 255);
            $table->string('title', 255)->comment('Judul/deskripsi singkat pekerjaan');
            $table->text('description')->nullable()->comment('Deskripsi detail pekerjaan');
            $table->string('category', 100)->nullable()->comment('Kategori: Maintenance, Project, Service, dll');
            $table->string('priority', 20)->default('normal')->comment('low, normal, high, critical');
            $table->string('status', 30)->default('open')->comment('open, in_progress, on_hold, closed, cancelled');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable()->comment('User PIC yang ditugaskan');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->comment('User yang membuat WO');
            $table->string('vendor', 255)->nullable()->comment('Vendor/mitra yang mengerjakan');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->decimal('estimated_cost', 20, 2)->nullable();
            $table->decimal('actual_cost', 20, 2)->nullable();
            $table->text('notes')->nullable()->comment('Catatan tambahan atau progress update');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
