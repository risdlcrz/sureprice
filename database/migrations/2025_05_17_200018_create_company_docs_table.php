<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            // Document type matching your form fields
            $table->enum('type', [
                'DTI_SEC_REGISTRATION',              // From dti_sec_registration
                'BUSINESS_PERMIT_MAYOR_PERMIT',      // From mayors_permit
                'VALID_ID_OWNER_REP',               // From valid_id
                'ACCREDITATIONS_CERTIFICATIONS',     // From accreditation_docs
                'COMPANY_PROFILE_PORTFOLIO',         // From company_profile
                'SAMPLE_PRICE_LIST'                  // From price_list
            ]);
            
            // File storage
            $table->string('path');          // Storage path (e.g.: 'company_docs/abc123.pdf')
            $table->string('original_name'); // Original filename (e.g.: 'business_permit.pdf')
            
            $table->timestamps();
            
            // Optional: Index for faster queries
            $table->index('company_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_docs');
    }
};