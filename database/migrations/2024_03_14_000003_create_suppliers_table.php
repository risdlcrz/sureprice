<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('supplier_type');
            $table->string('business_reg_no')->nullable();
            $table->string('contact_person');
            $table->string('designation')->nullable();
            $table->string('email');
            $table->string('mobile_number');
            $table->string('telephone_number')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('payment_terms')->nullable();
            $table->boolean('vat_registered')->default(false);
            $table->boolean('use_sureprice')->default(false);
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('dti_sec_registration_path')->nullable();
            $table->string('accreditation_docs_path')->nullable();
            $table->string('mayors_permit_path')->nullable();
            $table->string('valid_id_path')->nullable();
            $table->string('company_profile_path')->nullable();
            $table->string('price_list_path')->nullable();
            $table->timestamps();
        });

        // Create a pivot table for supplier materials
        Schema::create('material_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('material_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_supplier');
        Schema::dropIfExists('suppliers');
    }
}; 