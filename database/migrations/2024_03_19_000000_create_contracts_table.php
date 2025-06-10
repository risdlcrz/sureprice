<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contractor_id');
            $table->foreign('contractor_id')->references('id')->on('parties');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('parties');
            $table->string('contract_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->foreign('property_id')->references('id')->on('properties');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}; 