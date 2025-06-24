<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->boolean('admin_approved')->default(false);
            $table->timestamp('admin_approved_at')->nullable();
            $table->unsignedBigInteger('admin_approved_by')->nullable();
            $table->boolean('supplier_approved')->default(false);
            $table->timestamp('supplier_approved_at')->nullable();
            $table->unsignedBigInteger('supplier_approved_by')->nullable();
            $table->unsignedBigInteger('material_request_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            
            $table->foreign('admin_approved_by')->references('id')->on('users');
            $table->foreign('supplier_approved_by')->references('id')->on('users');
            $table->foreign('material_request_id')->references('id')->on('material_requests');
            $table->foreign('contract_id')->references('id')->on('contracts');
        });
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_approved_by']);
            $table->dropForeign(['supplier_approved_by']);
            $table->dropForeign(['material_request_id']);
            $table->dropForeign(['contract_id']);
            $table->dropColumn([
                'admin_approved',
                'admin_approved_at',
                'admin_approved_by',
                'supplier_approved',
                'supplier_approved_at',
                'supplier_approved_by',
                'material_request_id',
                'contract_id'
            ]);
        });
    }
}; 