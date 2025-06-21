<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('material_id')->constrained();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('stocks');
    }
}; 