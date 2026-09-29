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
            if (!Schema::hasColumn('bastos', 'cost_no')) {
                $table->string('cost_no', 100)->nullable()->after('project_name');
            }
            if (!Schema::hasColumn('bastos', 'cost_value')) {
                $table->decimal('cost_value', 18, 2)->nullable()->after('cost_no');
            }
            if (!Schema::hasColumn('bastos', 'cost_date_start')) {
                $table->date('cost_date_start')->nullable()->after('cost_value');
            }
            if (!Schema::hasColumn('bastos', 'cost_date_end')) {
                $table->date('cost_date_end')->nullable()->after('cost_date_start');
            }
            if (!Schema::hasColumn('bastos', 'cost_based')) {
                $table->decimal('cost_based', 18, 2)->nullable()->after('cost_date_end');
            }
            if (!Schema::hasColumn('bastos', 'supply_chain')) {
                $table->string('supply_chain', 255)->nullable()->after('cost_based');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bastos', function (Blueprint $table) {
            $columns = ['cost_no', 'cost_value', 'cost_date_start', 'cost_date_end', 'cost_based', 'supply_chain'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('bastos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
