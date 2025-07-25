<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('payment_plan')->nullable()->after('payment_method');
        });
    }
    public function down() {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('payment_plan');
        });
    }
}; 