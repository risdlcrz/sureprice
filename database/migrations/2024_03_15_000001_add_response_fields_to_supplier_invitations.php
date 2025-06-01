<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            $table->text('response_notes')->nullable()->after('status');
            $table->timestamp('responded_at')->nullable()->after('response_notes');
        });
    }

    public function down()
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            $table->dropColumn(['response_notes', 'responded_at']);
        });
    }
}; 