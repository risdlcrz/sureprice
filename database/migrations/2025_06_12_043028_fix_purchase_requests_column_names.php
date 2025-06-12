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
        // Check if the table exists and has the old column name
        if (Schema::hasTable('purchase_requests')) {
            // If pr_number exists but request_number doesn't, rename it
            if (Schema::hasColumn('purchase_requests', 'pr_number') && !Schema::hasColumn('purchase_requests', 'request_number')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('pr_number', 'request_number');
                });
            }
            
            // If requester_id exists but requested_by doesn't, rename it
            if (Schema::hasColumn('purchase_requests', 'requester_id') && !Schema::hasColumn('purchase_requests', 'requested_by')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('requester_id', 'requested_by');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column renames
        if (Schema::hasTable('purchase_requests')) {
            if (Schema::hasColumn('purchase_requests', 'request_number') && !Schema::hasColumn('purchase_requests', 'pr_number')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('request_number', 'pr_number');
                });
            }
            
            if (Schema::hasColumn('purchase_requests', 'requested_by') && !Schema::hasColumn('purchase_requests', 'requester_id')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('requested_by', 'requester_id');
                });
            }
        }
    }
};
