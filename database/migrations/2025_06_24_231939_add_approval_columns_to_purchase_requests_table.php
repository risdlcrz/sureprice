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
            $table->boolean('admin_approved')->default(false)->after('status');
            $table->timestamp('admin_approved_at')->nullable()->after('admin_approved');
            $table->unsignedBigInteger('admin_approved_by')->nullable()->after('admin_approved_at');
            $table->boolean('supplier_approved')->default(false)->after('admin_approved_by');
            $table->timestamp('supplier_approved_at')->nullable()->after('supplier_approved');
            $table->unsignedBigInteger('supplier_approved_by')->nullable()->after('supplier_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn([
                'admin_approved',
                'admin_approved_at',
                'admin_approved_by',
                'supplier_approved',
                'supplier_approved_at',
                'supplier_approved_by'
            ]);
        });
    }
};
