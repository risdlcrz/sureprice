<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScopeType;
use App\Models\Material;

class ScopeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scopes = [
            [
                'code' => 'site_preparation',
                'name' => 'Site Preparation',
                'category' => 'General',
                'estimated_days' => 2,
                'labor_rate' => 500.00,
                'materials_data' => [
                    [
                        'name' => 'Safety Equipment',
                        'cost' => 1000.00,
                        'unit' => 'set',
                        'is_per_area' => false
                    ],
                    [
                        'name' => 'Cleaning Supplies',
                        'cost' => 500.00,
                        'unit' => 'set',
                        'is_per_area' => false
                    ]
                ],
                'items' => [
                    'Site inspection and assessment',
                    'Safety equipment setup',
                    'Area cleaning and preparation',
                    'Material storage setup'
                ]
            ],
            [
                'code' => 'demolition',
                'name' => 'Demolition Work',
                'category' => 'General',
                'estimated_days' => 3,
                'labor_rate' => 800.00,
                'materials_data' => [
                    [
                        'name' => 'Demolition Tools',
                        'cost' => 2000.00,
                        'unit' => 'set',
                        'is_per_area' => false
                    ],
                    [
                        'name' => 'Waste Disposal',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ]
                ],
                'items' => [
                    'Wall demolition',
                    'Floor removal',
                    'Debris clearing',
                    'Waste disposal'
                ]
            ],
            [
                'code' => 'painting',
                'name' => 'Painting Work',
                'category' => 'Finishing',
                'estimated_days' => 4,
                'labor_rate' => 150.00,
                'materials_data' => [
                    [
                        'name' => 'Paint',
                        'cost' => 250.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ],
                    [
                        'name' => 'Primer',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ]
                ],
                'items' => [
                    'Surface preparation',
                    'Primer application',
                    'Paint application',
                    'Touch-up work'
                ]
            ],
            [
                'code' => 'tiling',
                'name' => 'Tiling Work',
                'category' => 'Finishing',
                'estimated_days' => 5,
                'labor_rate' => 300.00,
                'materials_data' => [
                    [
                        'name' => 'Tiles',
                        'cost' => 500.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ],
                    [
                        'name' => 'Grout',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ]
                ],
                'items' => [
                    'Surface preparation',
                    'Tile layout',
                    'Tile installation',
                    'Grouting'
                ]
            ],
            [
                'code' => 'ceiling',
                'name' => 'Ceiling Work',
                'category' => 'Finishing',
                'estimated_days' => 3,
                'labor_rate' => 200.00,
                'materials_data' => [
                    [
                        'name' => 'Ceiling Boards',
                        'cost' => 400.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ],
                    [
                        'name' => 'Frames',
                        'cost' => 200.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ]
                ],
                'items' => [
                    'Frame installation',
                    'Board installation',
                    'Joint treatment',
                    'Finishing'
                ]
            ],
            [
                'code' => 'electrical',
                'name' => 'Electrical Work',
                'category' => 'Systems',
                'estimated_days' => 4,
                'labor_rate' => 400.00,
                'materials_data' => [
                    [
                        'name' => 'Wiring',
                        'cost' => 150.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ],
                    [
                        'name' => 'Fixtures',
                        'cost' => 500.00,
                        'unit' => 'set',
                        'is_per_area' => false
                    ]
                ],
                'items' => [
                    'Wiring installation',
                    'Fixture installation',
                    'Testing',
                    'Safety checks'
                ]
            ],
            [
                'code' => 'plumbing',
                'name' => 'Plumbing Work',
                'category' => 'Systems',
                'estimated_days' => 3,
                'labor_rate' => 350.00,
                'materials_data' => [
                    [
                        'name' => 'Pipes',
                        'cost' => 200.00,
                        'unit' => 'per sqm',
                        'is_per_area' => true
                    ],
                    [
                        'name' => 'Fixtures',
                        'cost' => 1000.00,
                        'unit' => 'set',
                        'is_per_area' => false
                    ]
                ],
                'items' => [
                    'Pipe installation',
                    'Fixture installation',
                    'Testing',
                    'Leak checks'
                ]
            ]
        ];

        foreach ($scopes as $scopeData) {
            $materialsData = $scopeData['materials_data'];
            unset($scopeData['materials_data']);
            
            $scope = ScopeType::create([
                'code' => $scopeData['code'],
                'name' => $scopeData['name'],
                'category' => $scopeData['category'],
                'estimated_days' => $scopeData['estimated_days'],
                'labor_rate' => $scopeData['labor_rate'],
                'items' => $scopeData['items']
            ]);

            // Create and attach materials
            foreach ($materialsData as $materialData) {
                $material = Material::firstOrCreate(
                    ['name' => $materialData['name']],
                    [
                        'unit' => $materialData['unit'],
                        'base_price' => $materialData['cost'],
                        'is_per_area' => $materialData['is_per_area'],
                        'code' => 'MAT' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                        'category_id' => 1 // Default category
                    ]
                );

                $scope->materials()->attach($material->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
