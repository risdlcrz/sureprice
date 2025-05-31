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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('check_number')->nullable()->after('bank_account_number');
            $table->date('check_date')->nullable()->after('check_number');
            $table->string('check_image')->nullable()->after('check_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['check_number', 'check_date', 'check_image']);
        });
    }
}; 