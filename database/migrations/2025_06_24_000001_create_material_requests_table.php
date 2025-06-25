<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('requested_by');
            $table->string('status')->default('pending'); // pending, fulfilled, partial, requested, completed
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_request_id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->decimal('fulfilled_quantity', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('material_request_id')->references('id')->on('material_requests')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_request_items');
        Schema::dropIfExists('material_requests');
    }
}; 