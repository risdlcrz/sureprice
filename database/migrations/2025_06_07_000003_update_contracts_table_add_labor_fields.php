<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Add labor cost field if it doesn't exist
            if (!Schema::hasColumn('contracts', 'labor_cost')) {
                $table->decimal('labor_cost', 10, 2)->default(0)->after('total_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'labor_cost')) {
                $table->dropColumn('labor_cost');
            }
        });
    }
}; 