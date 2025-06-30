<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('contract_id')->nullable()->constrained('contracts');
            $table->decimal('quantity', 10, 2);
            $table->string('type'); // contract_deduction, manual_adjustment, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
}; 