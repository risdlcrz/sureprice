<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('awarded_supplier_id')->nullable()->after('validity_period');
            $table->decimal('awarded_amount', 15, 2)->nullable()->after('awarded_supplier_id');
            $table->foreign('awarded_supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['awarded_supplier_id']);
            $table->dropColumn(['awarded_supplier_id', 'awarded_amount']);
        });
    }
}; 