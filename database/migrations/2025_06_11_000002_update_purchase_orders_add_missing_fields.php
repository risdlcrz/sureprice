<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'contract_id')) {
                $table->foreignId('contract_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('purchase_orders', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('contract_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'purchase_request_id')) {
                $table->foreignId('purchase_request_id')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'po_number')) {
                $table->string('po_number')->unique()->after('purchase_request_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->foreignId('supplier_id')->constrained()->after('po_number');
            }
            if (!Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('supplier_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'status')) {
                $table->string('status')->default('pending')->after('total_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('purchase_orders', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'contract_id',
                'project_id',
                'purchase_request_id',
                'po_number',
                'supplier_id',
                'total_amount',
                'status',
                'notes',
                'deleted_at'
            ]);
        });
    }
}; 