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
        // Check if the table exists and has the old schema
        if (Schema::hasTable('purchase_requests')) {
            // Check if we need to rename columns
            if (Schema::hasColumn('purchase_requests', 'requester_id') && !Schema::hasColumn('purchase_requests', 'requested_by')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('requester_id', 'requested_by');
                });
            }

            if (Schema::hasColumn('purchase_requests', 'pr_number') && !Schema::hasColumn('purchase_requests', 'request_number')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('pr_number', 'request_number');
                });
            }

            // Add missing columns if they don't exist
            if (!Schema::hasColumn('purchase_requests', 'is_project_related')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->boolean('is_project_related')->default(false);
                });
            }

            if (!Schema::hasColumn('purchase_requests', 'project_id')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->unsignedBigInteger('project_id')->nullable();
                    $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                });
            }

            if (!Schema::hasColumn('purchase_requests', 'total_amount')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->decimal('total_amount', 15, 2)->default(0);
                });
            }

            // Remove old columns that are no longer needed
            if (Schema::hasColumn('purchase_requests', 'department')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->dropColumn('department');
                });
            }

            if (Schema::hasColumn('purchase_requests', 'required_date')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->dropColumn('required_date');
                });
            }

            if (Schema::hasColumn('purchase_requests', 'purpose')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->dropColumn('purpose');
                });
            }
        }

        // Check if purchase_request_items table needs updates
        if (Schema::hasTable('purchase_request_items')) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('purchase_request_items', 'preferred_brand')) {
                Schema::table('purchase_request_items', function (Blueprint $table) {
                    $table->string('preferred_brand')->nullable();
                });
            }

            if (!Schema::hasColumn('purchase_request_items', 'preferred_supplier_id')) {
                Schema::table('purchase_request_items', function (Blueprint $table) {
                    $table->unsignedBigInteger('preferred_supplier_id')->nullable();
                    $table->foreign('preferred_supplier_id')->references('id')->on('suppliers')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is mostly additive, so we'll only revert the column renames
        if (Schema::hasTable('purchase_requests')) {
            if (Schema::hasColumn('purchase_requests', 'requested_by') && !Schema::hasColumn('purchase_requests', 'requester_id')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('requested_by', 'requester_id');
                });
            }

            if (Schema::hasColumn('purchase_requests', 'request_number') && !Schema::hasColumn('purchase_requests', 'pr_number')) {
                Schema::table('purchase_requests', function (Blueprint $table) {
                    $table->renameColumn('request_number', 'pr_number');
                });
            }
        }
    }
};
