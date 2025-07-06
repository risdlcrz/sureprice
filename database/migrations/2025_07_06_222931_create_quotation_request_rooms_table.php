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
        Schema::create('quotation_request_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_request_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('length', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->decimal('area', 10, 2); // calculated field
            $table->decimal('volume', 10, 2); // calculated field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_request_rooms');
    }
};
