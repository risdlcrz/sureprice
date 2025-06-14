<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('additional_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('work_type');
            $table->text('description');
            $table->decimal('estimated_hours', 8, 2);
            $table->string('required_skills')->nullable();
            $table->text('labor_notes')->nullable();
            $table->date('preferred_start_date');
            $table->date('preferred_end_date');
            $table->text('timeline_notes')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('additional_work_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('additional_work_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('additional_work_materials');
        Schema::dropIfExists('additional_works');
    }
}; 