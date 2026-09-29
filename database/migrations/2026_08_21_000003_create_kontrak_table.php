<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontrak', function (Blueprint $table) {
            $table->id();
            $table->string('project_id', 100)->index();
            $table->year('tahun')->nullable()->index();
            $table->string('status', 50)->nullable()->index();
            $table->string('service_manager', 150)->nullable()->index();
            $table->string('project_client', 100)->nullable()->index();
            $table->string('project_classification', 50)->nullable();
            $table->string('project_name', 1000)->nullable();
            $table->string('contract_number', 255)->nullable();
            $table->string('category_contract', 100)->nullable();
            $table->string('skema', 100)->nullable();
            $table->date('date_of_contract')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('project_value', 20, 2)->nullable();
            $table->decimal('costbased', 20, 2)->nullable();
            $table->decimal('actual_cost_konfirmasi', 20, 2)->nullable();
            $table->decimal('actual_cost_admin', 20, 2)->nullable();
            $table->decimal('persentase', 10, 6)->nullable();
            $table->text('amandemen')->nullable();
            $table->decimal('tkdn', 10, 6)->nullable();
            $table->text('description')->nullable();
            $table->text('resource_management')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
