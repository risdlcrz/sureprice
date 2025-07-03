<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Category;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        // Get all categories by slug for easy lookup
        $categories = [
            'general' => Category::firstOrCreate(['slug' => 'general'], ['name' => 'General', 'description' => 'General construction materials']),
            'construction' => Category::firstOrCreate(['slug' => 'construction'], ['name' => 'Construction', 'description' => 'Construction materials']),
            'electrical' => Category::firstOrCreate(['slug' => 'electrical'], ['name' => 'Electrical', 'description' => 'Electrical materials']),
            'plumbing' => Category::firstOrCreate(['slug' => 'plumbing'], ['name' => 'Plumbing', 'description' => 'Plumbing materials']),
            'finishing' => Category::firstOrCreate(['slug' => 'finishing'], ['name' => 'Finishing', 'description' => 'Finishing materials']),
            'tools' => Category::firstOrCreate(['slug' => 'tools'], ['name' => 'Tools', 'description' => 'Tools and equipment']),
            'paint' => Category::firstOrCreate(['slug' => 'paint'], ['name' => 'Paint', 'description' => 'Paint and coatings']),
        ];

        $materials = [
            // Painting Crew
            ['name' => 'Paint (latex/acrylic)', 'unit' => 'liters', 'base_price' => 500, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Primer', 'unit' => 'liters', 'base_price' => 450, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Sandpaper', 'unit' => 'sheets', 'base_price' => 25, 'category' => 'finishing', 'warranty_period' => null, 'current_stock' => 500, 'minimum_stock' => 50],
            ['name' => 'Caulk', 'unit' => 'kg', 'base_price' => 300, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ["name" => "Painter's tape", 'unit' => 'meters', 'base_price' => 50, 'category' => 'finishing', 'warranty_period' => null, 'current_stock' => 500, 'minimum_stock' => 50],
            // Drywall Finishing/Installation
            ['name' => 'Joint compound', 'unit' => 'kg', 'base_price' => 200, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            ['name' => 'Drywall tape', 'unit' => 'meters', 'base_price' => 30, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            ['name' => 'Gypsum board', 'unit' => 'sqm', 'base_price' => 350, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            ['name' => 'Screws', 'unit' => 'pcs', 'base_price' => 5, 'category' => 'construction', 'warranty_period' => null, 'current_stock' => 500, 'minimum_stock' => 50],
            ['name' => 'Metal studs/channels', 'unit' => 'meters', 'base_price' => 150, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            // Tile Installation
            ['name' => 'Tiles', 'unit' => 'sqm', 'base_price' => 800, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Thin-set mortar', 'unit' => 'kg', 'base_price' => 250, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Grout', 'unit' => 'kg', 'base_price' => 300, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Spacers', 'unit' => 'pcs', 'base_price' => 2, 'category' => 'finishing', 'warranty_period' => null, 'current_stock' => 500, 'minimum_stock' => 50],
            // Cabinetry Installation
            ['name' => 'Plywood/MDF', 'unit' => 'sqm', 'base_price' => 1200, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            ['name' => 'Screws/nails', 'unit' => 'pcs', 'base_price' => 8, 'category' => 'construction', 'warranty_period' => null, 'current_stock' => 500, 'minimum_stock' => 50],
            ['name' => 'Adhesive', 'unit' => 'kg', 'base_price' => 400, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            // Fireproofing
            ['name' => 'Spray-applied fireproofing', 'unit' => 'kg', 'base_price' => 600, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            ['name' => 'Wire mesh', 'unit' => 'sqm', 'base_price' => 200, 'category' => 'construction', 'warranty_period' => 12, 'current_stock' => 200, 'minimum_stock' => 20],
            // Electrical Wiring
            ['name' => 'Conduit', 'unit' => 'meters', 'base_price' => 150, 'category' => 'electrical', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Wires', 'unit' => 'meters', 'base_price' => 80, 'category' => 'electrical', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Junction boxes', 'unit' => 'pcs', 'base_price' => 200, 'category' => 'electrical', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            // Plumbing Rough In
            ['name' => 'PVC pipes', 'unit' => 'meters', 'base_price' => 200, 'category' => 'plumbing', 'warranty_period' => 6, 'current_stock' => 150, 'minimum_stock' => 15],
            ['name' => 'Fittings', 'unit' => 'pcs', 'base_price' => 150, 'category' => 'plumbing', 'warranty_period' => 6, 'current_stock' => 150, 'minimum_stock' => 15],
            // Flooring Installation
            ['name' => 'Vinyl planks', 'unit' => 'sqm', 'base_price' => 1200, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Underlayment', 'unit' => 'sqm', 'base_price' => 150, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            // Concrete Coating
            ['name' => 'Epoxy coating', 'unit' => 'kg', 'base_price' => 800, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
            ['name' => 'Sealant', 'unit' => 'kg', 'base_price' => 600, 'category' => 'finishing', 'warranty_period' => 24, 'current_stock' => 100, 'minimum_stock' => 10],
        ];

        foreach ($materials as $material) {
            $cat = $categories[$material['category']] ?? $categories['general'];
            $materialData = $material;
            unset($materialData['category']);
            $mat = Material::updateOrCreate(
                ['name' => $material['name']],
                array_merge($materialData, ['category_id' => $cat->id])
            );
            // Sync inventory
            \App\Models\Inventory::updateOrCreate(
                ['material_id' => $mat->id],
                [
                    'quantity' => $material['current_stock'],
                    'unit' => $material['unit'],
                    'status' => 'active',
                    'minimum_threshold' => $material['minimum_stock'],
                ]
            );
        }
    }
} 