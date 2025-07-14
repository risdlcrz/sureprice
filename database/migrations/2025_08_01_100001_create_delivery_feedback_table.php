<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('delivery_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('users');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->tinyInteger('rating')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('delivery_feedback');
    }
}; 