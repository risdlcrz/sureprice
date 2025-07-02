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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'due_date')) {
                $table->date('due_date')->nullable()->after('delivery_date');
            }
            $table->decimal('penalty_rate', 5, 2)->default(0)->after('due_date'); // e.g., 2.00 for 2%
            $table->string('penalty_type')->default('percentage')->after('penalty_rate'); // percentage, fixed, compound
            $table->decimal('penalty_accrued', 15, 2)->default(0)->after('penalty_type');
            $table->date('last_penalty_calculation')->nullable()->after('penalty_accrued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'penalty_rate',
                'penalty_type',
                'penalty_accrued',
                'last_penalty_calculation',
            ]);
            if (Schema::hasColumn('purchase_orders', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
