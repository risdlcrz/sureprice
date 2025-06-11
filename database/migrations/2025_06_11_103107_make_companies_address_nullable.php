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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->change();
            $table->string('zip_code', 10)->nullable()->change();
            $table->string('street')->nullable()->change();
            $table->string('city')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province', 100)->nullable(false)->change();
            $table->string('zip_code', 10)->nullable(false)->change();
            $table->string('street')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });
    }
};
