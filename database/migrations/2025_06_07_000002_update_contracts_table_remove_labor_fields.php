<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Drop labor-related columns if they exist
            if (Schema::hasColumn('contracts', 'base_labor_rate')) {
                $table->dropColumn('base_labor_rate');
            }
            if (Schema::hasColumn('contracts', 'labor_cost')) {
                $table->dropColumn('labor_cost');
            }
            if (Schema::hasColumn('contracts', 'budget_allocation')) {
                $table->dropColumn('budget_allocation');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Add back the columns if needed to rollback
            if (!Schema::hasColumn('contracts', 'base_labor_rate')) {
                $table->decimal('base_labor_rate', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('contracts', 'labor_cost')) {
                $table->decimal('labor_cost', 10, 2)->default(0)->after('base_labor_rate');
            }
            if (!Schema::hasColumn('contracts', 'budget_allocation')) {
                $table->decimal('budget_allocation', 10, 2)->default(0)->after('labor_cost');
            }
        });
    }
}; 