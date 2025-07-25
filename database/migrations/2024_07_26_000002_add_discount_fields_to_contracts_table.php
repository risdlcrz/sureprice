<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('payment_plan');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('discount_percentage');
            $table->decimal('final_amount', 12, 2)->nullable()->after('discount_amount');
        });
    }
    public function down() {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_percentage', 'discount_amount', 'final_amount']);
        });
    }
}; 