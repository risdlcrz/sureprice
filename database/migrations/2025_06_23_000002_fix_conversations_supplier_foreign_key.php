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
        Schema::table('conversations', function (Blueprint $table) {
            // Drop the old foreign key constraint if it exists
            if (DB::getSchemaBuilder()->hasColumn('conversations', 'supplier_id')) {
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Exception $e) {
                    // Ignore if the foreign key doesn't exist
                }
            }
            
            // Add the new foreign key constraint to the companies table
            $table->foreign('supplier_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['supplier_id']);

            // Re-add the old foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }
}; 