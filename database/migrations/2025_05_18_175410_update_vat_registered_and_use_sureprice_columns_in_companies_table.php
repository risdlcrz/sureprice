<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->tinyInteger('vat_registered')->default(0)->change();
            $table->tinyInteger('use_sureprice')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Revert to previous type if needed
            $table->string('vat_registered')->change();
            $table->string('use_sureprice')->change();
        });
    }
};
