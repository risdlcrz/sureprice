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
            $table->string('company_name', 100)->nullable()->change();
            $table->string('business_reg_no', 100)->nullable()->change();
            $table->string('supplier_type', 100)->nullable()->change();
            $table->string('contact_person', 100)->nullable()->change();
            $table->string('designation', 100)->nullable()->change();
            $table->string('mobile_number', 20)->nullable()->change();
            $table->string('telephone_number', 20)->nullable()->change();
            $table->string('street', 255)->nullable()->change();
            $table->string('city', 100)->nullable()->change();
            $table->string('state', 100)->nullable()->change();
            $table->string('postal', 10)->nullable()->change();
            $table->integer('years_operation')->nullable()->change();
            $table->string('business_size', 100)->nullable()->change();
            $table->text('service_areas')->nullable()->change();
            $table->tinyInteger('vat_registered')->nullable()->change();
            $table->tinyInteger('use_sureprice')->nullable()->change();
            $table->string('payment_terms', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_name', 100)->nullable(false)->change();
            $table->string('business_reg_no', 100)->nullable(false)->change();
            $table->string('supplier_type', 100)->nullable(false)->change();
            $table->string('contact_person', 100)->nullable(false)->change();
            $table->string('designation', 100)->nullable(false)->change();
            $table->string('mobile_number', 20)->nullable(false)->change();
            $table->string('telephone_number', 20)->nullable(false)->change();
            $table->string('street', 255)->nullable(false)->change();
            $table->string('city', 100)->nullable(false)->change();
            $table->string('state', 100)->nullable(false)->change();
            $table->string('postal', 10)->nullable(false)->change();
            $table->integer('years_operation')->nullable(false)->change();
            $table->string('business_size', 100)->nullable(false)->change();
            $table->text('service_areas')->nullable(false)->change();
            $table->tinyInteger('vat_registered')->nullable(false)->change();
            $table->tinyInteger('use_sureprice')->nullable(false)->change();
            $table->string('payment_terms', 100)->nullable(false)->change();
        });
    }
};
