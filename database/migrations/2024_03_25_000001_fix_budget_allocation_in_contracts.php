<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First make the column nullable
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('budget_allocation', 10, 2)->nullable()->change();
        });

        // Then update existing records where budget_allocation is 0 or null
        DB::statement('UPDATE contracts SET budget_allocation = total_amount WHERE budget_allocation IS NULL OR budget_allocation = 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('budget_allocation', 10, 2)->change();
        });
    }
}; 