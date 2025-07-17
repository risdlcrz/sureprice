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
        Schema::table('contracts', function (Blueprint $table) {
            // Add editable property address fields
            $table->string('property_address')->nullable();
            $table->string('property_city')->nullable();
            $table->string('property_state')->nullable();
            $table->string('property_zip_code')->nullable();
            $table->string('property_country')->nullable();
            $table->boolean('same_as_client_address')->default(false);
            
            // Add scope and materials table fields
            $table->json('scope_materials_data')->nullable(); // Store scope and materials table data
            $table->json('chosen_suppliers_data')->nullable(); // Store chosen suppliers for scope of work
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'property_address',
                'property_city',
                'property_state',
                'property_zip_code',
                'property_country',
                'same_as_client_address',
                'scope_materials_data',
                'chosen_suppliers_data'
            ]);
        });
    }
}; 