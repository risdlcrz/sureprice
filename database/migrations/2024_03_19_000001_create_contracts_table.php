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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_id')->unique();
            $table->foreignId('contractor_id')->constrained('parties');
            $table->foreignId('client_id')->constrained('parties');
            $table->foreignId('property_id')->constrained();
            $table->string('scope_of_work');
            $table->text('scope_description');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 10, 2);
            $table->string('jurisdiction');
            $table->text('contract_terms');
            $table->string('client_signature')->nullable();
            $table->string('contractor_signature')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
}; 