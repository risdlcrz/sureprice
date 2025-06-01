<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->enum('business_type', ['corporation', 'partnership', 'sole_proprietorship', 'other']);
            $table->string('contact_person');
            $table->string('position');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('category_supplier_invitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invitation_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_supplier_invitation');
        Schema::dropIfExists('supplier_invitations');
    }
}; 