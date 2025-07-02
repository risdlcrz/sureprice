<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->boolean('banned')->default(false)->after('name');
            $table->string('ban_reason')->nullable()->after('banned');
        });
    }

    public function down()
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn(['banned', 'ban_reason']);
        });
    }
}; 