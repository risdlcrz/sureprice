<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_id')->unique();
            $table->foreignId('contractor_id')->constrained('parties')->onDelete('restrict');
            $table->foreignId('client_id')->constrained('parties')->onDelete('restrict');
            $table->foreignId('property_id')->constrained()->onDelete('restrict');
            $table->text('scope_of_work');
            $table->text('scope_description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 10, 2);
            $table->string('jurisdiction');
            $table->text('contract_terms');
            $table->string('client_signature')->nullable();
            $table->string('contractor_signature')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}; 