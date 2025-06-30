<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Material Request Status
            $table->string('material_request_status')->nullable();
            $table->timestamp('material_request_created_at')->nullable();
            
            // Stock Check
            $table->json('stock_check_results')->nullable();
            $table->timestamp('stock_checked_at')->nullable();
            
            // Approval Workflow
            $table->string('admin_approval_status')->default('pending');
            $table->timestamp('admin_approved_at')->nullable();
            $table->foreignId('admin_approved_by')->nullable()->constrained('users');
            
            $table->string('supplier_approval_status')->default('pending');
            $table->timestamp('supplier_approved_at')->nullable();
            $table->foreignId('supplier_approved_by')->nullable()->constrained('users');
            
            // Payment Validation
            $table->string('payment_validation_status')->default('pending');
            $table->timestamp('admin_payment_validated_at')->nullable();
            $table->foreignId('admin_payment_validator_id')->nullable()->constrained('users');
            $table->timestamp('supplier_payment_validated_at')->nullable();
            $table->foreignId('supplier_payment_validator_id')->nullable()->constrained('users');
            
            // Delivery
            $table->string('delivery_status')->nullable();
            $table->timestamp('delivery_created_at')->nullable();
            $table->timestamp('delivery_confirmed_at')->nullable();
            $table->foreignId('delivery_confirmed_by')->nullable()->constrained('users');
            
            // Stock Update
            $table->boolean('stock_updated')->default(false);
            $table->timestamp('stock_updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'material_request_status',
                'material_request_created_at',
                'stock_check_results',
                'stock_checked_at',
                'admin_approval_status',
                'admin_approved_at',
                'admin_approved_by',
                'supplier_approval_status',
                'supplier_approved_at',
                'supplier_approved_by',
                'payment_validation_status',
                'admin_payment_validated_at',
                'admin_payment_validator_id',
                'supplier_payment_validated_at',
                'supplier_payment_validator_id',
                'delivery_status',
                'delivery_created_at',
                'delivery_confirmed_at',
                'delivery_confirmed_by',
                'stock_updated',
                'stock_updated_at'
            ]);
        });
    }
}; 