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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add shipping tracking fields
            $table->boolean('shipped')->default(false);
            $table->timestamp('shipped_at')->nullable();
            $table->unsignedBigInteger('shipped_by')->nullable();
            $table->text('shipping_notes')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Add delivery tracking fields
            $table->boolean('delivered')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('delivered_by')->nullable();
            $table->text('delivery_notes')->nullable();
            
            // Add payment status tracking
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            
            // Add foreign key constraints
            $table->foreign('shipped_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('delivered_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['shipped_by']);
            $table->dropForeign(['delivered_by']);
            $table->dropForeign(['paid_by']);
            $table->dropColumn([
                'shipped',
                'shipped_at',
                'shipped_by',
                'shipping_notes',
                'tracking_number',
                'delivered',
                'delivered_at',
                'delivered_by',
                'delivery_notes',
                'payment_status',
                'paid_at',
                'paid_by'
            ]);
        });
    }
}; 