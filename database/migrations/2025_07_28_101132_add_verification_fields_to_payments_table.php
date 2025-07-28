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
        Schema::table('payments', function (Blueprint $table) {
            // Rejection fields
            $table->string('rejection_reason')->nullable();
            $table->text('rejection_details')->nullable();
            $table->string('action_required')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Information request fields
            $table->string('info_request_type')->nullable();
            $table->text('specific_request')->nullable();
            $table->date('response_deadline')->nullable();
            $table->string('priority_level')->default('medium');
            $table->unsignedBigInteger('info_requested_by')->nullable();
            $table->timestamp('info_requested_at')->nullable();
            
            // Foreign key constraints
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('info_requested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['info_requested_by']);
            
            $table->dropColumn([
                'rejection_reason',
                'rejection_details',
                'action_required',
                'rejected_by',
                'rejected_at',
                'info_request_type',
                'specific_request',
                'response_deadline',
                'priority_level',
                'info_requested_by',
                'info_requested_at',
            ]);
        });
    }
};
