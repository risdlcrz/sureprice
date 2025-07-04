<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixContractItemsMaterialId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-contract-items-material-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        $created = 0;
        $items = DB::table('contract_items')->whereNull('material_id')->get();

        foreach ($items as $item) {
            $material = DB::table('materials')->where('name', $item->material_name)->first();
            if (!$material) {
                // Create new material with sensible defaults
                $materialId = DB::table('materials')->insertGetId([
                    'name' => $item->material_name,
                    'unit' => $item->unit ?? 'pcs',
                    'base_price' => 0,
                    'srp_price' => 0,
                    'description' => '',
                    'category_id' => null,
                    'custom_category' => null,
                    'specifications' => null,
                    'minimum_stock' => 0,
                    'current_stock' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("Created new material '{$item->material_name}' with id $materialId");
                $created++;
                $material = DB::table('materials')->where('id', $materialId)->first();
            }
            if ($material) {
                DB::table('contract_items')
                    ->where('id', $item->id)
                    ->update(['material_id' => $material->id]);
                $this->info("Updated contract_item #{$item->id} with material_id {$material->id} ({$material->name})");
                $count++;
            }
        }

        $this->info("Done. Updated $count contract items. Created $created new materials.");
    }
}
