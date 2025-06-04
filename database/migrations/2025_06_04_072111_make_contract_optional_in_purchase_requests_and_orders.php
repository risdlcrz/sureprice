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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['contract_id']);
            
            // Make contract_id nullable and add the foreign key back as optional
            $table->foreignId('contract_id')->nullable()->change();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            // $table->dropForeign(['contract_id']); // Removed to prevent error if FK does not exist
            // Make contract_id nullable and add the foreign key back as optional
            $table->foreignId('contract_id')->nullable()->change();
            // $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['contract_id']);
            
            // Make contract_id required again
            $table->foreignId('contract_id')->nullable(false)->change();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['contract_id']);
            
            // Make contract_id required again
            $table->foreignId('contract_id')->nullable(false)->change();
            // $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }
};
