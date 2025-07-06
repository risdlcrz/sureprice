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
        Schema::create('quotation_request_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_request_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('scope_type_id')->constrained()->onDelete('cascade');
            $table->string('scope_name');
            $table->string('scope_category');
            $table->json('selected_materials'); // Store selected materials as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_request_scopes');
    }
};
