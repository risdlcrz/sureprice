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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('purchase_requests', 'requester_id')) {
                // Drop the foreign key constraint
                $table->dropForeign(['requester_id']);
                $table->dropColumn('requester_id');
            }
            
            // Drop the old pr_number column
            if (Schema::hasColumn('purchase_requests', 'pr_number')) {
                $table->dropColumn('pr_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Re-add the old columns if needed to rollback
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('pr_number')->nullable();
        });
    }
};
