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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Add admin approval workflow fields
            $table->boolean('admin_approved')->default(false);
            $table->timestamp('admin_approved_at')->nullable();
            $table->unsignedBigInteger('admin_approved_by')->nullable();
            $table->text('admin_approval_notes')->nullable();
            
            // Add supplier approval workflow fields
            $table->boolean('supplier_approved')->default(false);
            $table->timestamp('supplier_approved_at')->nullable();
            $table->unsignedBigInteger('supplier_approved_by')->nullable();
            $table->text('supplier_approval_notes')->nullable();
            
            // Add foreign key constraints
            $table->foreign('admin_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('supplier_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_approved_by']);
            $table->dropForeign(['supplier_approved_by']);
            $table->dropColumn([
                'admin_approved',
                'admin_approved_at',
                'admin_approved_by',
                'admin_approval_notes',
                'supplier_approved',
                'supplier_approved_at',
                'supplier_approved_by',
                'supplier_approval_notes'
            ]);
        });
    }
}; 