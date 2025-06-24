<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('status');
            $table->foreignId('admin_payment_validator_id')->nullable()->constrained('users');
            $table->timestamp('admin_payment_validated_at')->nullable();
            $table->foreignId('supplier_payment_validator_id')->nullable()->constrained('users');
            $table->timestamp('supplier_payment_validated_at')->nullable();
            $table->text('payment_notes')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->string('payment_method')->nullable();
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['admin_payment_validator_id']);
            $table->dropForeign(['supplier_payment_validator_id']);
            $table->dropColumn([
                'payment_status',
                'admin_payment_validator_id',
                'admin_payment_validated_at',
                'supplier_payment_validator_id',
                'supplier_payment_validated_at',
                'payment_notes',
                'payment_reference',
                'payment_amount',
                'payment_method'
            ]);
        });
    }
}; 