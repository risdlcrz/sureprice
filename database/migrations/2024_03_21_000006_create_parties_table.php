<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['contractor', 'client']);
            $table->enum('entity_type', ['company', 'person']);
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('street');
            $table->string('city');
            $table->string('state');
            $table->string('postal');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parties');
    }
}; 