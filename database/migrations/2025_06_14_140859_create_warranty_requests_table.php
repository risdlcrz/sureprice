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
        Schema::create('warranty_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->string('serial_number');
            $table->date('purchase_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('model_number')->nullable();
            $table->text('issue_description');
            $table->string('proof_of_purchase_path');
            $table->json('issue_photos_paths')->nullable();
            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_requests');
    }
};
