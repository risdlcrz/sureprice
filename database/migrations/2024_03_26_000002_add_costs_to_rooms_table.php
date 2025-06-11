<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('materials_cost', 15, 2)->default(0)->after('area');
            $table->decimal('labor_cost', 15, 2)->default(0)->after('materials_cost');
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['materials_cost', 'labor_cost']);
        });
    }
}; 