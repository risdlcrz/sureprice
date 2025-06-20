<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() {
        $warehouseA = DB::table('warehouses')->where('name', 'Warehouse A')->first();
        if ($warehouseA) {
            $materials = DB::table('materials')->get();
            foreach ($materials as $material) {
                DB::table('stocks')->insert([
                    'warehouse_id' => $warehouseA->id,
                    'material_id' => $material->id,
                    'current_stock' => $material->current_stock,
                    'minimum_stock' => $material->minimum_stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    public function down() {
        $warehouseA = DB::table('warehouses')->where('name', 'Warehouse A')->first();
        if ($warehouseA) {
            DB::table('stocks')->where('warehouse_id', $warehouseA->id)->delete();
        }
    }
}; 