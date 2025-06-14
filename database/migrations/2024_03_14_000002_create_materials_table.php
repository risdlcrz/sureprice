<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('unit');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('srp_price', 10, 2)->default(0);
            $table->text('specifications')->nullable();
            $table->decimal('minimum_stock', 10, 2)->default(0);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('custom_category')->nullable();
            $table->timestamps();
        });

        Schema::create('material_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_images');
        Schema::dropIfExists('materials');
    }
}; 