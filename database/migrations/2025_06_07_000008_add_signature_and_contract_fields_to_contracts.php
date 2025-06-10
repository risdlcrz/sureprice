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
            $table->longText('contractor_signature')->nullable()->after('status');
            $table->longText('client_signature')->nullable()->after('contractor_signature');
            $table->text('contract_terms')->nullable()->after('client_signature');
            $table->string('jurisdiction')->nullable()->after('contract_terms');
            $table->text('scope_of_work')->nullable()->after('jurisdiction');
            $table->text('scope_description')->nullable()->after('scope_of_work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'contractor_signature',
                'client_signature',
                'contract_terms',
                'jurisdiction',
                'scope_of_work',
                'scope_description',
            ]);
        });
    }
}; 