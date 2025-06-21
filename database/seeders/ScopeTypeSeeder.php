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
                'code' => 'painting_crew',
                'name' => 'Painting Crew',
                'category' => 'Painting',
                'is_wall_work' => true,
                'estimated_days' => 2,
                'labor_rate' => 350.00,
                'complexity_factor' => 1.2,
                'tasks' => [
                    [
                        'name' => 'Surface Prep',
                        'labor_hours_per_sqm' => 0.2,
                        'description' => 'Includes cleaning, sanding, priming.'
                    ],
                    [
                        'name' => 'Paint Application',
                        'labor_hours_per_sqm' => 0.15,
                        'description' => '2 coats (cut-in + rolling).'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Paint (latex/acrylic)',
                        'unit' => 'liters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 10,
                        'waste_factor' => 1.1,
                        'base_price' => 500
                    ],
                    [
                        'name' => 'Primer',
                        'unit' => 'liters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 12,
                        'waste_factor' => 1.1,
                        'base_price' => 450
                    ],
                    [
                        'name' => 'Sandpaper',
                        'unit' => 'sheets',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 10,
                        'waste_factor' => 1.2,
                        'base_price' => 25
                    ],
                    [
                        'name' => 'Caulk',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 100,
                        'waste_factor' => 1.1,
                        'base_price' => 300
                    ],
                    [
                        'name' => "Painter's tape",
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 2,
                        'waste_factor' => 1.1,
                        'base_price' => 50
                    ]
                ]
            ],
            [
                'code' => 'drywall_finishing',
                'name' => 'Drywall Finishing',
                'category' => 'Painting',
                'is_wall_work' => true,
                'estimated_days' => 3,
                'labor_rate' => 400.00,
                'complexity_factor' => 1.3,
                'tasks' => [
                    [
                        'name' => 'Drywall Finishing',
                        'labor_hours_per_sqm' => 0.35,
                        'description' => 'Taping, mudding, sanding.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Joint compound',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 5,
                        'waste_factor' => 1.2,
                        'base_price' => 200
                    ],
                    [
                        'name' => 'Drywall tape',
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 3,
                        'waste_factor' => 1.1,
                        'base_price' => 30
                    ],
                    [
                        'name' => 'Sandpaper',
                        'unit' => 'sheets',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 5,
                        'waste_factor' => 1.2,
                        'base_price' => 25
                    ]
                ]
            ],
            [
                'code' => 'drywall_installation',
                'name' => 'Drywall Installation',
                'category' => 'Fit-outs',
                'is_wall_work' => true,
                'estimated_days' => 4,
                'labor_rate' => 450.00,
                'complexity_factor' => 1.4,
                'tasks' => [
                    [
                        'name' => 'Framing',
                        'labor_hours_per_sqm' => 0.4,
                        'description' => 'Install metal/wood studs'
                    ],
                    [
                        'name' => 'Hanging',
                        'labor_hours_per_sqm' => 0.3,
                        'description' => 'Secure gypsum boards to studs'
                    ],
                    [
                        'name' => 'Cutting',
                        'labor_hours_per_sqm' => 0.2,
                        'description' => 'Fit boards around outlets/doors'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Gypsum board',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'waste_factor' => 1.1,
                        'base_price' => 350
                    ],
                    [
                        'name' => 'Screws',
                        'unit' => 'pcs',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 0.1,
                        'waste_factor' => 1.2,
                        'base_price' => 5
                    ],
                    [
                        'name' => 'Metal studs/channels',
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 0.5,
                        'waste_factor' => 1.1,
                        'base_price' => 150
                    ]
                ]
            ],
            [
                'code' => 'tile_installation',
                'name' => 'Tile Installation',
                'category' => 'Fit-outs',
                'is_wall_work' => true,
                'estimated_days' => 5,
                'labor_rate' => 500.00,
                'complexity_factor' => 1.5,
                'tasks' => [
                    [
                        'name' => 'Tile Installation',
                        'labor_hours_per_sqm' => 0.5,
                        'description' => 'Layout, mortar, grout.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Tiles',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'waste_factor' => 1.1,
                        'base_price' => 800
                    ],
                    [
                        'name' => 'Thin-set mortar',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 4,
                        'waste_factor' => 1.2,
                        'base_price' => 250
                    ],
                    [
                        'name' => 'Grout',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 2,
                        'waste_factor' => 1.1,
                        'base_price' => 300
                    ],
                    [
                        'name' => 'Spacers',
                        'unit' => 'pcs',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 12,
                        'waste_factor' => 1.2,
                        'base_price' => 2
                    ]
                    ]
                ],
            [
                'code' => 'cabinetry_installation',
                'name' => 'Cabinetry Installation',
                'category' => 'Fit-outs',
                'is_wall_work' => true,
                'estimated_days' => 6,
                'labor_rate' => 550.00,
                'complexity_factor' => 1.6,
                'tasks' => [
                    [
                        'name' => 'Measurement & Assembly',
                        'labor_hours_per_sqm' => 0.5,
                        'description' => 'Verify dimensions, construct cabinets'
                    ],
                    [
                        'name' => 'Installation',
                        'labor_hours_per_sqm' => 0.4,
                        'description' => 'Secure to walls/floor'
                    ],
                    [
                        'name' => 'Finishing',
                        'labor_hours_per_sqm' => 0.2,
                        'description' => 'Attach hardware (handles, hinges)'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Plywood/MDF',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'waste_factor' => 1.15,
                        'base_price' => 1200
                    ],
                    [
                        'name' => 'Screws/nails',
                        'unit' => 'pcs',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 17,
                        'waste_factor' => 1.1,
                        'base_price' => 8
                    ],
                    [
                        'name' => 'Adhesive',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 10,
                        'waste_factor' => 1.2,
                        'base_price' => 400
                    ]
                ]
            ],
            [
                'code' => 'fireproofing',
                'name' => 'Fireproofing Spray',
                'category' => 'MEPFS',
                'is_wall_work' => true,
                'estimated_days' => 3,
                'labor_rate' => 400.00,
                'complexity_factor' => 1.4,
                'tasks' => [
                    [
                        'name' => 'Fireproofing Spray',
                        'labor_hours_per_sqm' => 0.075,
                        'description' => 'Vertical surfaces only.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Spray-applied fireproofing',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 1.75,
                        'waste_factor' => 1.2,
                        'base_price' => 600
                    ],
                    [
                        'name' => 'Wire mesh',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'waste_factor' => 1.1,
                        'base_price' => 200
                    ]
                ]
                    ],
                    [
                'code' => 'electrical_wiring',
                'name' => 'Electrical Wiring',
                'category' => 'MEPFS',
                'is_wall_work' => true,
                'estimated_days' => 4,
                'labor_rate' => 450.00,
                'complexity_factor' => 1.5,
                'tasks' => [
                    [
                        'name' => 'Electrical Wiring',
                        'labor_hours_per_sqm' => 0.125,
                        'description' => 'Rough-in for walls/floors.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Conduit',
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 0.5,
                        'waste_factor' => 1.1,
                        'base_price' => 150
                    ],
                    [
                        'name' => 'Wires',
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 1,
                        'waste_factor' => 1.2,
                        'base_price' => 80
                    ],
                    [
                        'name' => 'Junction boxes',
                        'unit' => 'pcs',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 0.1,
                        'waste_factor' => 1.1,
                        'base_price' => 200
                    ]
                ]
            ],
            [
                'code' => 'plumbing_rough_in',
                'name' => 'Plumbing Pipes',
                'category' => 'MEPFS',
                'is_wall_work' => true,
                'estimated_days' => 3,
                'labor_rate' => 400.00,
                'complexity_factor' => 1.3,
                'tasks' => [
                    [
                        'name' => 'Plumbing Pipes',
                        'labor_hours_per_sqm' => 0.175,
                        'description' => 'PVC/CPVC installation.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'PVC pipes',
                        'unit' => 'meters',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 0.3,
                        'waste_factor' => 1.1,
                        'base_price' => 200
                    ],
                    [
                        'name' => 'Fittings',
                        'unit' => 'pcs',
                        'is_per_area' => true,
                        'is_wall_material' => true,
                        'coverage_rate' => 2.5,
                        'waste_factor' => 1.2,
                        'base_price' => 150
                    ]
                ]
                    ],
                    [
                'code' => 'flooring_installation',
                'name' => 'Vinyl Flooring',
                'category' => 'Infrastructure',
                'is_wall_work' => false,
                'estimated_days' => 4,
                'labor_rate' => 500.00,
                'complexity_factor' => 1.4,
                'tasks' => [
                    [
                        'name' => 'Vinyl Flooring',
                        'labor_hours_per_sqm' => 0.25,
                        'description' => 'Includes underlayment.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Vinyl planks',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => false,
                        'waste_factor' => 1.1,
                        'base_price' => 1200
                    ],
                    [
                        'name' => 'Underlayment',
                        'unit' => 'sqm',
                        'is_per_area' => true,
                        'is_wall_material' => false,
                        'waste_factor' => 1.05,
                        'base_price' => 150
                    ],
                    [
                        'name' => 'Adhesive',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => false,
                        'coverage_rate' => 5,
                        'waste_factor' => 1.2,
                        'base_price' => 400
                    ]
                ]
            ],
            [
                'code' => 'concrete_coating',
                'name' => 'Concrete Waterproofing',
                'category' => 'Infrastructure',
                'is_wall_work' => false,
                'estimated_days' => 3,
                'labor_rate' => 450.00,
                'complexity_factor' => 1.3,
                'tasks' => [
                    [
                        'name' => 'Concrete Waterproofing',
                        'labor_hours_per_sqm' => 0.125,
                        'description' => 'Epoxy/polyurethane application.'
                    ]
                ],
                'materials_data' => [
                    [
                        'name' => 'Epoxy coating',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => false,
                        'coverage_rate' => 0.35,
                        'waste_factor' => 1.2,
                        'base_price' => 800
                    ],
                    [
                        'name' => 'Sealant',
                        'unit' => 'kg',
                        'is_per_area' => true,
                        'is_wall_material' => false,
                        'coverage_rate' => 0.1,
                        'waste_factor' => 1.1,
                        'base_price' => 600
                    ]
                ]
            ]
        ];

        foreach ($scopes as $scopeData) {
            $materialsData = $scopeData['materials_data'];
            $tasks = $scopeData['tasks'];
            unset($scopeData['materials_data']);
            unset($scopeData['tasks']);
            
            $scope = ScopeType::updateOrCreate(
                ['code' => $scopeData['code']],
                [
                    'name' => $scopeData['name'],
                    'category' => $scopeData['category'],
                    'is_wall_work' => $scopeData['is_wall_work'],
                    'estimated_days' => $scopeData['estimated_days'],
                    'labor_rate' => $scopeData['labor_rate'],
                    'complexity_factor' => $scopeData['complexity_factor'],
                    'tasks' => json_encode($tasks)
                ]
            );

            // Create and attach materials
            foreach ($materialsData as $materialData) {
                $material = Material::firstOrCreate(
                    ['name' => $materialData['name']],
                    [
                        'unit' => $materialData['unit'],
                        'base_price' => $materialData['base_price'],
                        'is_per_area' => $materialData['is_per_area'],
                        'is_wall_material' => $materialData['is_wall_material'],
                        'coverage_rate' => $materialData['coverage_rate'] ?? null,
                        'waste_factor' => $materialData['waste_factor'] ?? 1.1,
                        'code' => 'MAT' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                        'category_id' => 1 // Default category
                    ]
                );

                // Use syncWithoutDetaching to avoid duplicate attachments
                $scope->materials()->syncWithoutDetaching([$material->id => [
                    'created_at' => now(),
                    'updated_at' => now()
                ]]);
            }
        }
    }
}
