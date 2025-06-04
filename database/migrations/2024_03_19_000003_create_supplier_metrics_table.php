<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->integer('total_deliveries')->default(0);
            $table->integer('ontime_deliveries')->default(0);
            $table->integer('total_units')->default(0);
            $table->integer('defective_units')->default(0);
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_metrics');
    }
}; 