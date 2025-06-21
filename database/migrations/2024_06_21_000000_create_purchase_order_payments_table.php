<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->string('admin_proof')->nullable();
            $table->string('admin_reference_number')->nullable();
            $table->date('admin_paid_date')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('supplier_verified')->default(false);
            $table->timestamp('supplier_verified_at')->nullable();
            $table->text('supplier_notes')->nullable();
            $table->enum('status', ['pending', 'for_verification', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('purchase_order_payments');
    }
}; 