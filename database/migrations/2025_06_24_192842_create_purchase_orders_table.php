<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number')->unique();
                $table->foreignId('purchase_request_id')->constrained();
                $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('supplier_id')->constrained();
                $table->decimal('total_amount', 12, 2);
                $table->string('status')->default('draft');
                $table->date('delivery_date');
                $table->string('payment_terms');
                $table->string('shipping_terms');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        // Create purchase_order_items table
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
                $table->foreignId('material_id')->constrained();
                $table->decimal('quantity', 10, 2);
                $table->string('unit');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('total_amount', 12, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
}; 