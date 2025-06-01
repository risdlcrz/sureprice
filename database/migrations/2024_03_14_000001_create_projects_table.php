<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('client_name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->decimal('budget', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}; 