<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Purchase Requests table
        if (!Schema::hasTable('purchase_requests')) {
            Schema::create('purchase_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_number')->unique();
                $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('requested_by')->constrained('users');
                $table->boolean('is_project_related')->default(false);
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->boolean('admin_approved')->default(false);
                $table->timestamp('admin_approved_at')->nullable();
                $table->foreignId('admin_approved_by')->nullable()->constrained('users');
                $table->boolean('supplier_approved')->default(false);
                $table->timestamp('supplier_approved_at')->nullable();
                $table->foreignId('supplier_approved_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }

        // 2. Purchase Orders table
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

        // 3. Purchase Order Items table
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

        // 4. Payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->foreignId('contract_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('purchase_order_id')->nullable()->constrained()->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->string('payment_method');
                $table->string('status')->default('pending');
                $table->date('payment_date');
                $table->string('reference_number')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requests');
    }
}; 