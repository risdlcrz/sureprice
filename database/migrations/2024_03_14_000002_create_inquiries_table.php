<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->date('required_date');
            $table->string('department');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        // Ensure materials table exists
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('unit');
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->decimal('minimum_stock', 10, 2)->default(0);
                $table->decimal('current_stock', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        Schema::create('inquiry_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inquiry_material');
        if (Schema::hasTable('materials')) {
            Schema::dropIfExists('materials');
        }
        Schema::dropIfExists('inquiries');
    }
}; 