<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Inventory;
use App\Models\Warehouse;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = Material::all();
        $warehouses = Warehouse::all();

        if ($materials->isEmpty()) {
            $this->command->info('No materials found, skipping InventorySeeder.');
            return;
        }

        foreach ($materials as $material) {
            // Create inventory records for each warehouse
            foreach ($warehouses as $warehouse) {
                // Generate realistic initial stock based on material type
                $initialStock = $this->generateInitialStock($material);
                $minimumThreshold = $this->generateMinimumThreshold($material);

                Inventory::updateOrCreate(
                    [
                        'material_id' => $material->id,
                        'location' => $warehouse->name,
                    ],
                    [
                        'quantity' => $initialStock,
                        'unit' => $material->unit,
                        'location' => $warehouse->name,
                        'batch_number' => 'BATCH-' . strtoupper(substr($material->name, 0, 3)) . '-' . rand(1000, 9999),
                        'expiry_date' => $this->generateExpiryDate($material),
                        'status' => 'active',
                        'last_restock_date' => now()->subDays(rand(1, 30)),
                        'last_restock_quantity' => $initialStock,
                        'minimum_threshold' => $minimumThreshold,
                        'notes' => 'Initial stock from seeder'
                    ]
                );
            }
        }

        $this->command->info('Inventory records created successfully!');
    }

    /**
     * Generate realistic initial stock based on material type
     */
    private function generateInitialStock(Material $material): float
    {
        $materialName = strtolower($material->name);
        
        // Different stock levels based on material type
        if (str_contains($materialName, 'paint') || str_contains($materialName, 'primer')) {
            return rand(50, 200); // Liters
        } elseif (str_contains($materialName, 'cement') || str_contains($materialName, 'concrete')) {
            return rand(100, 500); // Bags
        } elseif (str_contains($materialName, 'steel') || str_contains($materialName, 'metal')) {
            return rand(20, 100); // Pieces/meters
        } elseif (str_contains($materialName, 'lumber') || str_contains($materialName, 'wood')) {
            return rand(50, 200); // Pieces
        } elseif (str_contains($materialName, 'tile') || str_contains($materialName, 'vinyl')) {
            return rand(100, 500); // Square meters
        } elseif (str_contains($materialName, 'pipe') || str_contains($materialName, 'fitting')) {
            return rand(30, 150); // Pieces/meters
        } elseif (str_contains($materialName, 'wire') || str_contains($materialName, 'electrical')) {
            return rand(100, 300); // Meters
        } elseif (str_contains($materialName, 'sandpaper') || str_contains($materialName, 'tape')) {
            return rand(50, 200); // Pieces/rolls
        } else {
            return rand(20, 100); // Default
        }
    }

    /**
     * Generate minimum threshold based on material type
     */
    private function generateMinimumThreshold(Material $material): float
    {
        $materialName = strtolower($material->name);
        
        // Set minimum threshold as 20-30% of typical stock level
        if (str_contains($materialName, 'paint') || str_contains($materialName, 'primer')) {
            return rand(10, 50);
        } elseif (str_contains($materialName, 'cement') || str_contains($materialName, 'concrete')) {
            return rand(20, 100);
        } elseif (str_contains($materialName, 'steel') || str_contains($materialName, 'metal')) {
            return rand(5, 25);
        } elseif (str_contains($materialName, 'lumber') || str_contains($materialName, 'wood')) {
            return rand(10, 50);
        } elseif (str_contains($materialName, 'tile') || str_contains($materialName, 'vinyl')) {
            return rand(20, 100);
        } elseif (str_contains($materialName, 'pipe') || str_contains($materialName, 'fitting')) {
            return rand(5, 30);
        } elseif (str_contains($materialName, 'wire') || str_contains($materialName, 'electrical')) {
            return rand(20, 60);
        } elseif (str_contains($materialName, 'sandpaper') || str_contains($materialName, 'tape')) {
            return rand(10, 40);
        } else {
            return rand(5, 25);
        }
    }

    /**
     * Generate expiry date for perishable materials
     */
    private function generateExpiryDate(Material $material): ?string
    {
        $materialName = strtolower($material->name);
        
        // Only set expiry dates for perishable materials
        if (str_contains($materialName, 'paint') || str_contains($materialName, 'primer') || 
            str_contains($materialName, 'adhesive') || str_contains($materialName, 'sealant')) {
            return now()->addMonths(rand(6, 24))->format('Y-m-d');
        }
        
        return null; // No expiry for non-perishable materials
    }
} 