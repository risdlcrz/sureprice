<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('status');
                $table->foreign('purchase_order_id')
                      ->references('id')
                      ->on('purchase_orders')
                      ->onDelete('set null');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'contract_id')) {
                $table->unsignedBigInteger('contract_id')->nullable()->after('status');
                $table->foreign('contract_id')
                      ->references('id')
                      ->on('contracts')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
    }
}; 