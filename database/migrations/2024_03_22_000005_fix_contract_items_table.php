<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            // First drop the material_unit column if it exists
            if (Schema::hasColumn('contract_items', 'material_unit')) {
                $table->dropColumn('material_unit');
            }
            
            // Add the unit column if it doesn't exist
            if (!Schema::hasColumn('contract_items', 'unit')) {
                $table->string('unit')->after('material_name');
            }
        });
    }

    public function down()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            if (Schema::hasColumn('contract_items', 'unit')) {
                $table->dropColumn('unit');
            }
            
            if (!Schema::hasColumn('contract_items', 'material_unit')) {
                $table->string('material_unit')->after('material_name');
            }
        });
    }
}; 