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
            $table->decimal('advance_payment_percentage', 5, 2)->default(0)->after('payment_terms');
            $table->decimal('retention_percentage', 5, 2)->default(0)->after('advance_payment_percentage');
            $table->integer('payment_due_days')->default(0)->after('retention_percentage');
            $table->text('warranty_terms')->nullable()->after('payment_due_days');
            $table->text('cancellation_terms')->nullable()->after('warranty_terms');
            $table->text('additional_terms')->nullable()->after('cancellation_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'advance_payment_percentage',
                'retention_percentage',
                'payment_due_days',
                'warranty_terms',
                'cancellation_terms',
                'additional_terms'
            ]);
        });
    }
}; 