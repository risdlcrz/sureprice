<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('engagement_score', 3, 1)->default(0);
            $table->decimal('delivery_speed_score', 3, 1)->default(0);
            $table->decimal('performance_score', 3, 1)->default(0);
            $table->decimal('quality_score', 3, 1)->default(0);
            $table->decimal('cost_variance_score', 3, 1)->default(0);
            $table->decimal('sustainability_score', 3, 1)->default(0);
            $table->decimal('final_score', 3, 1)->default(0);
            $table->decimal('delivery_ontime_ratio', 5, 2)->default(0);
            $table->decimal('defect_ratio', 5, 2)->default(0);
            $table->decimal('cost_variance_ratio', 5, 2)->default(0);
            $table->timestamp('evaluation_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_evaluations');
    }
}; 