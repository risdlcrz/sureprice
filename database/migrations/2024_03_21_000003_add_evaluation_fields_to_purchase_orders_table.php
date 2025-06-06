<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'is_delivered')) {
                $table->boolean('is_delivered')->default(false);
            }
            if (!Schema::hasColumn('purchase_orders', 'is_on_time')) {
                $table->boolean('is_on_time')->default(false);
            }
            if (!Schema::hasColumn('purchase_orders', 'total_units')) {
                $table->integer('total_units')->default(0);
            }
            if (!Schema::hasColumn('purchase_orders', 'defective_units')) {
                $table->integer('defective_units')->default(0);
            }
            if (!Schema::hasColumn('purchase_orders', 'estimated_cost')) {
                $table->decimal('estimated_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_orders', 'actual_cost')) {
                $table->decimal('actual_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_orders', 'quality_notes')) {
                $table->text('quality_notes')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'is_completed')) {
                $table->boolean('is_completed')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_delivered',
                'is_on_time',
                'total_units',
                'defective_units',
                'estimated_cost',
                'actual_cost',
                'quality_notes',
                'is_completed'
            ]);
        });
    }
}; 