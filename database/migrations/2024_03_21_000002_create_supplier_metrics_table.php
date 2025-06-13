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
            $table->decimal('average_defect_rate', 5, 2)->default(0);
            $table->decimal('average_cost_variance', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_metrics');
    }
}; 