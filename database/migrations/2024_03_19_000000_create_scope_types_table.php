<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scope_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category');
            $table->integer('estimated_days');
            $table->decimal('labor_rate', 10, 2);
            $table->json('materials')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scope_types');
    }
};

class CreateScopeTypeMaterialTable extends Migration
{
    public function up()
    {
        Schema::create('scope_type_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scope_type_id');
            $table->unsignedBigInteger('material_id');
            $table->foreign('scope_type_id')->references('id')->on('scope_types')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->unique(['scope_type_id', 'material_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scope_type_material');
    }
} 