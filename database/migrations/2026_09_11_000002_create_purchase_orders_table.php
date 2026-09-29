<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number', 100)->unique();
                $table->string('project_id', 50)->index();
                $table->string('project_name', 255)->nullable();
                $table->string('vendor_name', 255)->index();
                $table->string('po_title', 255);
                $table->text('description')->nullable();
                $table->decimal('po_amount', 18, 2)->default(0);
                $table->date('order_date');
                $table->date('delivery_deadline')->nullable();
                $table->string('status', 50)->default('issued'); // issued, in_progress, basto_verified, completed, cancelled
                $table->string('contract_file', 255)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
