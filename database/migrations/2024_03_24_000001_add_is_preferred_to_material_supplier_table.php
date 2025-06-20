<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('material_supplier', function (Blueprint $table) {
            $table->boolean('is_preferred')->default(false)->after('lead_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_supplier', function (Blueprint $table) {
            $table->dropColumn('is_preferred');
        });
    }
}; 