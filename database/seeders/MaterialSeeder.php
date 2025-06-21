<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Category;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        // Ensure a 'General' category exists
        $category = Category::firstOrCreate([
            'name' => 'General'
        ], [
            'slug' => 'general',
            'description' => 'General construction materials'
        ]);

        $materials = [
            // Painting Crew
            ['name' => 'Paint (latex/acrylic)', 'unit' => 'liters', 'base_price' => 500],
            ['name' => 'Primer', 'unit' => 'liters', 'base_price' => 450],
            ['name' => 'Sandpaper', 'unit' => 'sheets', 'base_price' => 25],
            ['name' => 'Caulk', 'unit' => 'kg', 'base_price' => 300],
            ["name" => "Painter's tape", 'unit' => 'meters', 'base_price' => 50],
            // Drywall Finishing
            ['name' => 'Joint compound', 'unit' => 'kg', 'base_price' => 200],
            ['name' => 'Drywall tape', 'unit' => 'meters', 'base_price' => 30],
            // Drywall Installation
            ['name' => 'Gypsum board', 'unit' => 'sqm', 'base_price' => 350],
            ['name' => 'Screws', 'unit' => 'pcs', 'base_price' => 5],
            ['name' => 'Metal studs/channels', 'unit' => 'meters', 'base_price' => 150],
            // Tile Installation
            ['name' => 'Tiles', 'unit' => 'sqm', 'base_price' => 800],
            ['name' => 'Thin-set mortar', 'unit' => 'kg', 'base_price' => 250],
            ['name' => 'Grout', 'unit' => 'kg', 'base_price' => 300],
            ['name' => 'Spacers', 'unit' => 'pcs', 'base_price' => 2],
            // Cabinetry Installation
            ['name' => 'Plywood/MDF', 'unit' => 'sqm', 'base_price' => 1200],
            ['name' => 'Screws/nails', 'unit' => 'pcs', 'base_price' => 8],
            ['name' => 'Adhesive', 'unit' => 'kg', 'base_price' => 400],
            // Fireproofing
            ['name' => 'Spray-applied fireproofing', 'unit' => 'kg', 'base_price' => 600],
            ['name' => 'Wire mesh', 'unit' => 'sqm', 'base_price' => 200],
            // Electrical Wiring
            ['name' => 'Conduit', 'unit' => 'meters', 'base_price' => 150],
            ['name' => 'Wires', 'unit' => 'meters', 'base_price' => 80],
            ['name' => 'Junction boxes', 'unit' => 'pcs', 'base_price' => 200],
            // Plumbing Rough In
            ['name' => 'PVC pipes', 'unit' => 'meters', 'base_price' => 200],
            ['name' => 'Fittings', 'unit' => 'pcs', 'base_price' => 150],
            // Flooring Installation
            ['name' => 'Vinyl planks', 'unit' => 'sqm', 'base_price' => 1200],
            ['name' => 'Underlayment', 'unit' => 'sqm', 'base_price' => 150],
            // Concrete Coating
            ['name' => 'Epoxy coating', 'unit' => 'kg', 'base_price' => 800],
            ['name' => 'Sealant', 'unit' => 'kg', 'base_price' => 600],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['name' => $material['name']],
                array_merge($material, ['category_id' => $category->id])
            );
        }
    }
} 