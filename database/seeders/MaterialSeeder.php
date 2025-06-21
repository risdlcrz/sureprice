<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Support\Str;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Painting' => [
                ['name' => 'Paint (latex/acrylic)', 'unit' => 'liters', 'base_price' => 500],
                ['name' => 'Primer', 'unit' => 'liters', 'base_price' => 450],
                ['name' => 'Sandpaper', 'unit' => 'sheets', 'base_price' => 25],
                ['name' => 'Caulk', 'unit' => 'kg', 'base_price' => 300],
                ["name" => "Painter's tape", 'unit' => 'meters', 'base_price' => 50],
            ],
            'Drywall & Framing' => [
                ['name' => 'Joint compound', 'unit' => 'kg', 'base_price' => 200],
                ['name' => 'Drywall tape', 'unit' => 'meters', 'base_price' => 30],
                ['name' => 'Gypsum board', 'unit' => 'sqm', 'base_price' => 350],
                ['name' => 'Screws', 'unit' => 'pcs', 'base_price' => 5],
                ['name' => 'Metal studs/channels', 'unit' => 'meters', 'base_price' => 150],
            ],
            'Tiling' => [
                ['name' => 'Tiles', 'unit' => 'sqm', 'base_price' => 800],
                ['name' => 'Thin-set mortar', 'unit' => 'kg', 'base_price' => 250],
                ['name' => 'Grout', 'unit' => 'kg', 'base_price' => 300],
                ['name' => 'Spacers', 'unit' => 'pcs', 'base_price' => 2],
            ],
            'Cabinetry' => [
                ['name' => 'Plywood/MDF', 'unit' => 'sqm', 'base_price' => 1200],
                ['name' => 'Screws/nails', 'unit' => 'pcs', 'base_price' => 8],
                ['name' => 'Adhesive', 'unit' => 'kg', 'base_price' => 400],
            ],
            'Fireproofing' => [
                ['name' => 'Spray-applied fireproofing', 'unit' => 'kg', 'base_price' => 600],
                ['name' => 'Wire mesh', 'unit' => 'sqm', 'base_price' => 200],
            ],
            'Electrical' => [
                ['name' => 'Conduit', 'unit' => 'meters', 'base_price' => 150],
                ['name' => 'Wires', 'unit' => 'meters', 'base_price' => 80],
                ['name' => 'Junction boxes', 'unit' => 'pcs', 'base_price' => 200],
            ],
            'Plumbing' => [
                ['name' => 'PVC pipes', 'unit' => 'meters', 'base_price' => 200],
                ['name' => 'Fittings', 'unit' => 'pcs', 'base_price' => 150],
            ],
            'Flooring' => [
                ['name' => 'Vinyl planks', 'unit' => 'sqm', 'base_price' => 1200],
                ['name' => 'Underlayment', 'unit' => 'sqm', 'base_price' => 150],
            ],
            'Coatings & Sealants' => [
                ['name' => 'Epoxy coating', 'unit' => 'kg', 'base_price' => 800],
                ['name' => 'Sealant', 'unit' => 'kg', 'base_price' => 600],
            ],
        ];

        foreach ($categories as $categoryName => $materials) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );

            foreach ($materials as $materialData) {
                Material::updateOrCreate(
                    ['name' => $materialData['name']],
                    array_merge($materialData, ['category_id' => $category->id])
                );
            }
        }
    }
} 