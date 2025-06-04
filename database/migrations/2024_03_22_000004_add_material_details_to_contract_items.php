<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only try to copy data if there are any existing records
        if (DB::table('contract_items')->count() > 0) {
            // Copy material details
            DB::table('contract_items')
                ->join('materials', 'contract_items.material_id', '=', 'materials.id')
                ->update([
                    'contract_items.material_name' => DB::raw('materials.name'),
                    'contract_items.material_unit' => DB::raw('materials.unit')
                ]);

            // Copy supplier names
            DB::table('contract_items')
                ->join('suppliers', 'contract_items.supplier_id', '=', 'suppliers.id')
                ->whereNotNull('contract_items.supplier_id')
                ->update([
                    'contract_items.supplier_name' => DB::raw('suppliers.name')
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No columns to drop since none were added in this migration
    }
}; 