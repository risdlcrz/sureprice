<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Only add if it doesn't exist
            if (!Schema::hasColumn('contracts', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'purchase_order_id')) {
                $table->dropColumn('purchase_order_id');
            }
        });
    }
};