<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add foreign key for contracts.purchase_order_id
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('purchase_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('set null');
        });

        // Add foreign key for purchase_requests.contract_id
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreign('contract_id')
                  ->references('id')
                  ->on('contracts')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
        });
    }
}; 