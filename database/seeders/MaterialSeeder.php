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
            [
                'name' => 'Cement',
                'unit' => 'bag',
                'base_price' => 250.00,
                'srp_price' => 300.00,
            ],
            [
                'name' => 'Sand',
                'unit' => 'cubic meter',
                'base_price' => 800.00,
                'srp_price' => 950.00,
            ],
            [
                'name' => 'Gravel',
                'unit' => 'cubic meter',
                'base_price' => 900.00,
                'srp_price' => 1050.00,
            ],
            [
                'name' => 'Paint',
                'unit' => 'gallon',
                'base_price' => 350.00,
                'srp_price' => 400.00,
            ],
            [
                'name' => 'Tiles',
                'unit' => 'box',
                'base_price' => 1200.00,
                'srp_price' => 1350.00,
            ],
            [
                'name' => 'Steel Bar',
                'unit' => 'piece',
                'base_price' => 400.00,
                'srp_price' => 450.00,
            ],
            [
                'name' => 'Plywood',
                'unit' => 'sheet',
                'base_price' => 700.00,
                'srp_price' => 800.00,
            ],
            [
                'name' => 'Electrical Wire',
                'unit' => 'roll',
                'base_price' => 1500.00,
                'srp_price' => 1700.00,
            ],
            [
                'name' => 'PVC Pipe',
                'unit' => 'length',
                'base_price' => 120.00,
                'srp_price' => 150.00,
            ],
            [
                'name' => 'Nails',
                'unit' => 'kg',
                'base_price' => 80.00,
                'srp_price' => 100.00,
            ],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['name' => $material['name']],
                array_merge($material, ['category_id' => $category->id])
            );
        }
    }
} 