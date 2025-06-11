<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add fields to materials table
        Schema::table('materials', function (Blueprint $table) {
            $table->boolean('is_per_area')->default(false);
            $table->decimal('coverage_rate', 10, 2)->nullable()->comment('How many square meters one unit covers');
            $table->decimal('waste_factor', 5, 2)->default(1.10)->comment('Default 10% waste factor');
            $table->decimal('minimum_quantity', 10, 2)->default(1);
            $table->json('bulk_pricing')->nullable()->comment('JSON array of {min_quantity, price} objects');
        });

        // Add fields to scope_types table
        Schema::table('scope_types', function (Blueprint $table) {
            $table->string('labor_type')->default('per_area')->comment('per_area, fixed, or per_unit');
            $table->decimal('minimum_labor_cost', 10, 2)->default(0);
            $table->decimal('complexity_factor', 5, 2)->default(1.00);
            $table->decimal('labor_hours_per_unit', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'is_per_area',
                'coverage_rate',
                'waste_factor',
                'minimum_quantity',
                'bulk_pricing'
            ]);
        });

        Schema::table('scope_types', function (Blueprint $table) {
            $table->dropColumn([
                'labor_type',
                'minimum_labor_cost',
                'complexity_factor',
                'labor_hours_per_unit'
            ]);
        });
    }
}; 