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
            // Only add contract_payment_terms if it doesn't exist
            if (!Schema::hasColumn('contracts', 'contract_payment_terms')) {
                $table->text('contract_payment_terms')->nullable()->after('contract_terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'contract_payment_terms')) {
                $table->dropColumn('contract_payment_terms');
            }
        });
    }
};
