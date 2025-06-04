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
        Schema::table('quotations', function (Blueprint $table) {
            // Drop the project_id foreign key
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
            
            // Add purchase_request_id
            $table->foreignId('purchase_request_id')->after('id')->constrained()->onDelete('cascade');
            
            // Add new fields
            $table->decimal('total_amount', 15, 2)->nullable()->after('status');
            $table->string('payment_terms')->nullable()->after('total_amount');
            $table->string('delivery_terms')->nullable()->after('payment_terms');
            $table->string('validity_period')->nullable()->after('delivery_terms');
        });

        // Create table for supplier responses
        Schema::create('quotation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_terms')->nullable();
            $table->string('delivery_terms')->nullable();
            $table->string('validity_period')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, submitted, approved, rejected
            $table->timestamps();

            $table->unique(['quotation_id', 'supplier_id']);
        });

        // Create table for response items
        Schema::create('quotation_response_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_response_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('specifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Create table for response attachments
        Schema::create('quotation_response_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_response_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_response_attachments');
        Schema::dropIfExists('quotation_response_items');
        Schema::dropIfExists('quotation_responses');
        
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'payment_terms', 'delivery_terms', 'validity_period']);
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn('purchase_request_id');
            $table->foreignId('project_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
