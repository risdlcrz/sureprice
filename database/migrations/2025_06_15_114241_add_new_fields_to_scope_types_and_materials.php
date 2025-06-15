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
        Schema::table('scope_types', function (Blueprint $table) {
            if (!Schema::hasColumn('scope_types', 'is_wall_work')) {
                $table->boolean('is_wall_work')->default(false)->after('category');
            }
            if (!Schema::hasColumn('scope_types', 'tasks')) {
                $table->json('tasks')->nullable()->after('complexity_factor');
            }
        });

        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'coverage_rate')) {
                $table->decimal('coverage_rate', 10, 2)->nullable()->after('base_price');
            }
            if (!Schema::hasColumn('materials', 'waste_factor')) {
                $table->decimal('waste_factor', 8, 2)->default(1.1)->after('coverage_rate');
            }
            if (!Schema::hasColumn('materials', 'is_wall_material')) {
                $table->boolean('is_wall_material')->default(false)->after('is_per_area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scope_types', function (Blueprint $table) {
            $table->dropColumn(['is_wall_work', 'tasks']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['coverage_rate', 'waste_factor', 'is_wall_material']);
        });
    }
};
