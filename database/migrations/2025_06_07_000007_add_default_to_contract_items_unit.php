<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            // First make the column nullable to avoid any issues
            $table->string('unit')->nullable()->change();
            
            // Then update any null values to 'pcs'
            DB::table('contract_items')->whereNull('unit')->update(['unit' => 'pcs']);
            
            // Finally make it non-nullable with a default
            $table->string('unit')->nullable(false)->default('pcs')->change();
        });
    }

    public function down()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            $table->string('unit')->nullable(false)->default(null)->change();
        });
    }
}; 