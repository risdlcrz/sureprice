<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('company_material')) {
            // Migrate data from company_material to material_supplier
            $companyMaterials = DB::table('company_material')->get();
            foreach ($companyMaterials as $row) {
                // Only insert if not already present
                $exists = DB::table('material_supplier')
                    ->where('material_id', $row->material_id)
                    ->where('supplier_id', $row->company_id)
                    ->exists();
                if (!$exists) {
                    DB::table('material_supplier')->insert([
                        'material_id' => $row->material_id,
                        'supplier_id' => $row->company_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
    public function down()
    {
        // Optionally, remove migrated data (not recommended unless rollback is needed)
        // DB::table('material_supplier')->truncate();
    }
}; 