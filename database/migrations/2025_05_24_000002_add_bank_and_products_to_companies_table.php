<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('primary_products_services', 500)->nullable()->after('service_areas');
            $table->string('bank_name')->nullable()->after('primary_products_services');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('primary_products_services');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_account_name');
            $table->dropColumn('bank_account_number');
        });
    }
}; 