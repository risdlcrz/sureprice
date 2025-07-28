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
            // Add signature date fields if they don't exist
            if (!Schema::hasColumn('contracts', 'contractor_date_signed')) {
                $table->timestamp('contractor_date_signed')->nullable();
            }
            if (!Schema::hasColumn('contracts', 'client_date_signed')) {
                $table->timestamp('client_date_signed')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'contractor_date_signed',
                'client_date_signed',
            ]);
        });
    }
}; 