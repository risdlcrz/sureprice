<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('material_supplier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unique(['material_id', 'supplier_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('material_supplier');
    }
}; 