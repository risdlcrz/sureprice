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
        Schema::table('contract_items', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable()->after('contract_id');
            $table->unsignedBigInteger('scope_id')->nullable()->after('room_id');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('scope_id')->references('id')->on('contract_scopes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_items', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['scope_id']);
            $table->dropColumn(['room_id', 'scope_id']);
        });
    }
};
