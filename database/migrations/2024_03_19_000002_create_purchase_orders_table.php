<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('purchase_request_id')->constrained();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->foreignId('supplier_id')->constrained();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('draft');
            $table->date('delivery_date');
            $table->string('payment_terms');
            $table->string('shipping_terms');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
}; 