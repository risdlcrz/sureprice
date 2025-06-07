<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            // First check if we need to rename the column
            if (Schema::hasColumn('contract_items', 'material_unit') && !Schema::hasColumn('contract_items', 'unit')) {
                $table->renameColumn('material_unit', 'unit');
            }
            // If neither column exists, create unit
            else if (!Schema::hasColumn('contract_items', 'material_unit') && !Schema::hasColumn('contract_items', 'unit')) {
                $table->string('unit')->after('material_name');
            }
        });
    }

    public function down()
    {
        Schema::table('contract_items', function (Blueprint $table) {
            if (Schema::hasColumn('contract_items', 'unit') && !Schema::hasColumn('contract_items', 'material_unit')) {
                $table->renameColumn('unit', 'material_unit');
            }
        });
    }
}; 