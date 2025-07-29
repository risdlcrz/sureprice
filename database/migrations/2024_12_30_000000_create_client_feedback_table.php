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
        Schema::create('client_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('parties')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Rating fields (1-5 scale)
            $table->integer('overall_rating')->nullable();
            $table->integer('communication_rating')->nullable();
            $table->integer('quality_rating')->nullable();
            $table->integer('timeliness_rating')->nullable();
            $table->integer('professionalism_rating')->nullable();
            $table->integer('value_rating')->nullable();
            
            // Additional feedback
            $table->text('comments')->nullable();
            $table->integer('recommendation_likelihood')->nullable(); // 1-10 scale
            
            // Submission tracking
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['contract_id', 'client_id']);
            $table->index('submitted_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_feedback');
    }
}; 