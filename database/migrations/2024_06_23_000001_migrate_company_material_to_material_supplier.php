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
                // Check if the supplier exists in the suppliers table
                $supplierExists = DB::table('suppliers')
                    ->where('id', $row->company_id)
                    ->exists();
                
                // Check if the material exists in the materials table
                $materialExists = DB::table('materials')
                    ->where('id', $row->material_id)
                    ->exists();
                
                // Only proceed if both supplier and material exist
                if (!$supplierExists || !$materialExists) {
                    // Log the skipped record for debugging
                    \Log::warning("Skipping company_material record: material_id={$row->material_id}, company_id={$row->company_id} - " . 
                        (!$supplierExists ? "Supplier not found" : "Material not found"));
                    continue;
                }
                
                // Check if the relationship already exists
                $exists = DB::table('material_supplier')
                    ->where('material_id', $row->material_id)
                    ->where('supplier_id', $row->company_id)
                    ->exists();
                
                if (!$exists) {
                    try {
                        DB::table('material_supplier')->insert([
                            'material_id' => $row->material_id,
                            'supplier_id' => $row->company_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        // Log the error but continue with other records
                        \Log::error("Failed to migrate company_material record: material_id={$row->material_id}, company_id={$row->company_id} - " . $e->getMessage());
                    }
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