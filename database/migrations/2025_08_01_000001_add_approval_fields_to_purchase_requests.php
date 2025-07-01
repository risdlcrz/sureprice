<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // $table->foreignId('admin_id')->nullable()->after('requester_id')->constrained('users');
            if (!Schema::hasColumn('purchase_requests', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('requested_by')->constrained('users');
            }
            // $table->timestamp('admin_approved_at')->nullable();
            // $table->foreignId('supplier_id')->nullable()->after('admin_id')->constrained('users');
            if (!Schema::hasColumn('purchase_requests', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('admin_id')->constrained('users');
            }
            // $table->timestamp('supplier_approved_at')->nullable();
            if (!Schema::hasColumn('purchase_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            // $table->string('status')->default('pending_admin_approval')->change();
        });
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn([
                'admin_id',
                'admin_approved_at',
                'supplier_id',
                'supplier_approved_at',
                'rejection_reason'
            ]);
            $table->string('status')->default('draft')->change();
        });
    }
}; 