<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->date('delivery_date');
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'received', 'rejected'])->default('pending');
            $table->decimal('total_units', 15, 2)->default(0);
            $table->decimal('defective_units', 15, 2)->default(0);
            $table->decimal('wastage_units', 15, 2)->default(0);
            $table->text('quality_check_notes')->nullable();
            $table->boolean('is_on_time')->default(true);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->foreignId('supplier_evaluation_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}; 