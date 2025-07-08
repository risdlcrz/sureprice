<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('order_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->integer('ontime_deliveries')->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->integer('defective_units')->default(0);
            $table->integer('total_units')->default(0);
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->date('order_date');
            $table->decimal('quality_rating', 4, 2)->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('order_evaluations');
    }
}; 