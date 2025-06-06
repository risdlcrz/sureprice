<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('base_labor_rate', 10, 2)->default(0)->after('total_amount');
            $table->decimal('labor_cost', 10, 2)->default(0)->after('base_labor_rate');
            $table->decimal('materials_cost', 10, 2)->default(0)->after('labor_cost');
            
            // Remove any existing columns that might conflict with the new structure
            if (Schema::hasColumn('contracts', 'budget_allocation')) {
                $table->dropColumn('budget_allocation');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['base_labor_rate', 'labor_cost', 'materials_cost']);
            
            // Restore the budget_allocation column if it was removed
            if (!Schema::hasColumn('contracts', 'budget_allocation')) {
                $table->decimal('budget_allocation', 10, 2)->nullable()->after('total_amount');
            }
        });
    }
}; 