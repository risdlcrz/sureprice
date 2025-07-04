<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->bigIncrements('id')->first()->change();
        });
    }

    public function down()
    {
        // Optional: revert to previous state if needed
        // $table->dropColumn('id');
    }
}; 