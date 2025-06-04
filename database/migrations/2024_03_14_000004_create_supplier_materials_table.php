<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('material_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_materials');
    }
}; 