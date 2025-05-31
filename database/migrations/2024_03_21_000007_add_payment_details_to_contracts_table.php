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
            $table->decimal('budget_allocation', 10, 2)->after('total_amount');
            $table->string('payment_method')->after('budget_allocation');
            $table->text('payment_terms')->after('payment_method');
            $table->string('bank_name')->nullable()->after('payment_terms');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'budget_allocation',
                'payment_method',
                'payment_terms',
                'bank_name',
                'bank_account_name',
                'bank_account_number'
            ]);
        });
    }
}; 