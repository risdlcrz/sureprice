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
        Schema::table('material_supplier', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['supplier_id']);

            // Add the new foreign key constraint referencing the companies table
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_supplier', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['supplier_id']);

            // Re-add the old foreign key constraint
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('cascade');
        });
    }
};
