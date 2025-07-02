<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('material_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->date('date');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('material_price_histories');
    }
}; 