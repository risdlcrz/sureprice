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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // contractor or client
            $table->string('entity_type'); // person or company
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('street');
            $table->string('unit')->nullable();
            $table->string('barangay');
            $table->string('city');
            $table->string('state');
            $table->string('postal');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
}; 