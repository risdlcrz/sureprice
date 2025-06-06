<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('materials', 'srp_price')) {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('srp_price', 10, 2)->default(0)->after('base_price');
        });

        // Copy base_price to srp_price for existing materials
        DB::statement('UPDATE materials SET srp_price = base_price WHERE srp_price = 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('materials', 'srp_price')) {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('srp_price');
        });
        }
    }
};
