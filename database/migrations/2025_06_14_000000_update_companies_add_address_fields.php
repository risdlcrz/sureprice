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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('companies', 'barangay')) {
                $table->string('barangay', 100)->nullable()->after('street');
            }
            if (!Schema::hasColumn('companies', 'unit')) {
                $table->string('unit', 255)->nullable()->after('street');
            }

            // Rename columns if they exist and are named differently
            if (Schema::hasColumn('companies', 'province') && !Schema::hasColumn('companies', 'state')) {
                $table->renameColumn('province', 'state');
            }
            if (Schema::hasColumn('companies', 'zip_code') && !Schema::hasColumn('companies', 'postal')) {
                $table->renameColumn('zip_code', 'postal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Reverse adding missing columns
            if (Schema::hasColumn('companies', 'barangay')) {
                $table->dropColumn('barangay');
            }
            if (Schema::hasColumn('companies', 'unit')) {
                $table->dropColumn('unit');
            }

            // Reverse renaming columns
            if (Schema::hasColumn('companies', 'state') && !Schema::hasColumn('companies', 'province')) {
                $table->renameColumn('state', 'province');
            }
            if (Schema::hasColumn('companies', 'postal') && !Schema::hasColumn('companies', 'zip_code')) {
                $table->renameColumn('postal', 'zip_code');
            }
        });
    }
}; 