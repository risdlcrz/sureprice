<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supplier invitations are created against an optional contract (see the admin form and
 * SupplierInvitationController), but the table required a project_id and had no contract_id,
 * so every insert failed. Make project_id optional and add the contract link.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->foreignId('contract_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_id');
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};
