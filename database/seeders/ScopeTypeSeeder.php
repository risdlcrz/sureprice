<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScopeType;

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
                'materials' => [
                    [
                        'name' => 'Safety Equipment',
                        'cost' => 1000.00,
                        'unit' => 'set',
                        'isPerArea' => false
                    ],
                    [
                        'name' => 'Cleaning Supplies',
                        'cost' => 500.00,
                        'unit' => 'set',
                        'isPerArea' => false
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
                'materials' => [
                    [
                        'name' => 'Demolition Tools',
                        'cost' => 2000.00,
                        'unit' => 'set',
                        'isPerArea' => false
                    ],
                    [
                        'name' => 'Waste Disposal',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
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
                'materials' => [
                    [
                        'name' => 'Paint',
                        'cost' => 250.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
                    ],
                    [
                        'name' => 'Primer',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
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
                'materials' => [
                    [
                        'name' => 'Tiles',
                        'cost' => 500.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
                    ],
                    [
                        'name' => 'Grout',
                        'cost' => 100.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
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
                'materials' => [
                    [
                        'name' => 'Ceiling Boards',
                        'cost' => 400.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
                    ],
                    [
                        'name' => 'Frames',
                        'cost' => 200.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
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
                'materials' => [
                    [
                        'name' => 'Wiring',
                        'cost' => 150.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
                    ],
                    [
                        'name' => 'Fixtures',
                        'cost' => 500.00,
                        'unit' => 'set',
                        'isPerArea' => false
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
                'materials' => [
                    [
                        'name' => 'Pipes',
                        'cost' => 200.00,
                        'unit' => 'per sqm',
                        'isPerArea' => true
                    ],
                    [
                        'name' => 'Fixtures',
                        'cost' => 1000.00,
                        'unit' => 'set',
                        'isPerArea' => false
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

        foreach ($scopes as $scope) {
            ScopeType::updateOrCreate(
                ['code' => $scope['code']],
                $scope
            );
        }
    }
}
