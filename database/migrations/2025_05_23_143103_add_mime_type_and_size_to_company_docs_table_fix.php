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
        Schema::table('company_docs', function (Blueprint $table) {
            if (!Schema::hasColumn('company_docs', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
            if (!Schema::hasColumn('company_docs', 'size')) {
                $table->bigInteger('size')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_docs', function (Blueprint $table) {
            $table->dropColumn(['mime_type', 'size']);
        });
    }
};
