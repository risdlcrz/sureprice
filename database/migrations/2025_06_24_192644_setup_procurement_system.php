<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create purchase requests table first
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

        // Now create material requests
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Create material request items
        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained();
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->decimal('fulfilled_quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_items');
        Schema::dropIfExists('material_requests');
        Schema::dropIfExists('purchase_requests');
    }
};
