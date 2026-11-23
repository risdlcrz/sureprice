<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Changes to verified company details (e.g. a client's registered company name) that need an admin's approval and proof.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('field', 50);                 // e.g. company_name
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->text('reason')->nullable();
            $table->string('proof_path');                // private ("local") disk
            $table->string('proof_name');
            $table->string('proof_mime', 100)->nullable();
            $table->unsignedInteger('proof_size')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, cancelled
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_change_requests');
    }
};
