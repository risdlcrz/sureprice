<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('material_id')->constrained();
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['in', 'out']);
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
}; 