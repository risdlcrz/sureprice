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
        Schema::table('quotation_responses', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('total_amount');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
            $table->decimal('final_amount', 15, 2)->default(0)->after('discount_amount');
            $table->text('discount_reason')->nullable()->after('final_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_responses', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_percentage',
                'discount_amount', 
                'final_amount',
                'discount_reason'
            ]);
        });
    }
};
