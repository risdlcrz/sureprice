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
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('requester_id')->constrained('users');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->date('required_date');
            $table->timestamps();
        });

        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained();
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_request_items');
        Schema::dropIfExists('material_requests');
    }
}; 