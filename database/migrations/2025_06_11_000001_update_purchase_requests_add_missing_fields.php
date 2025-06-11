<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Add missing fields from the duplicate migration
            if (!Schema::hasColumn('purchase_requests', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('contract_id');
            }
            if (!Schema::hasColumn('purchase_requests', 'requested_by')) {
                $table->foreignId('requested_by')->after('project_id');
            }
            if (!Schema::hasColumn('purchase_requests', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('requested_by');
            }
            if (!Schema::hasColumn('purchase_requests', 'is_project_related')) {
                $table->boolean('is_project_related')->default(false)->after('status');
            }
            if (!Schema::hasColumn('purchase_requests', 'deleted_at')) {
                $table->softDeletes();
            }

            // Update status enum if needed
            $table->string('status')->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn([
                'project_id',
                'requested_by',
                'total_amount',
                'is_project_related',
                'deleted_at'
            ]);
        });
    }
}; 