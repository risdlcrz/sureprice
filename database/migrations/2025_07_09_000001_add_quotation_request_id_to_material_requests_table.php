<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('quotation_request_id')->nullable()->after('id');
            $table->foreign('quotation_request_id')->references('id')->on('quotation_requests')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign(['quotation_request_id']);
            $table->dropColumn('quotation_request_id');
        });
    }
}; 