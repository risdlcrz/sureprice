<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['purchase_order_id']);
            // Then drop the column
            $table->dropColumn('purchase_order_id');
        });

        // Make sure contract_id in purchase_orders is properly set up
        Schema::table('purchase_orders', function (Blueprint $table) {
            // In case the foreign key doesn't exist
            // $table->foreign('contract_id')
            //       ->references('id')
            //       ->on('contracts')
            //       ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->foreign('purchase_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('set null');
        });
    }
}; 