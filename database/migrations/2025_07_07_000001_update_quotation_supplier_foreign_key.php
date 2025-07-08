<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('quotation_supplier', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['supplier_id']);
            // Add the new foreign key constraint referencing companies
            $table->foreign('supplier_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('quotation_supplier', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['supplier_id']);
            // Re-add the old foreign key constraint referencing suppliers
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');
        });
    }
}; 