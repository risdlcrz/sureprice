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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('supplier_type', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Reverting may be tricky if data exceeds the old enum length.
            // For simplicity, we'll revert to a string, but a more robust rollback
            // might require data cleanup or a different approach.
            $table->string('supplier_type', 50)->change();
        });
    }
};
