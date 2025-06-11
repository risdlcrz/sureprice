<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('role');
            $table->string('street')->nullable()->after('company_name');
            $table->string('barangay')->nullable()->after('street');
            $table->string('city')->nullable()->after('barangay');
            $table->string('state')->nullable()->after('city');
            $table->string('postal')->nullable()->after('state');
            $table->string('phone')->nullable()->after('postal');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'street', 'barangay', 'city', 'state', 'postal', 'phone']);
        });
    }
}; 