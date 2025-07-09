<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\Stock;
use App\Models\SupplierEvaluation;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomSupplierSeeder extends Seeder
{
    public function run()
    {
        $supplierNames = [
            'Aguila Construction Supply', 'Bayanihan Builders', 'Luzviminda Hardware', 'Mabuhay Materials Co.',
            'Pinoy Prime Supplies', 'Tagumpay Trading', 'Lakbay Construction Depot', 'Matatag Merchandising',
            'Gintong Ani Enterprises', 'Silangan Steel & Cement', 'Bagong Bayan Hardware', 'Isla Construction Depot',
            'Kapitbahay Supplies', 'Malasakit Materials', 'Sulong Construction', 'Tibay Trading', 'Bughaw Builders',
            'Dalisay Depot', 'Luntian Hardware', 'Pilipinas Prime Supply', 'Kaagapay Construction', 'Sikap Merchandising',
            'Bayanihan Steel', 'Lakambini Materials', 'Tagumpay Hardware'
        ];
        $cities = ['Quezon City', 'Manila', 'Makati', 'Pasig', 'Taguig', 'Cebu City', 'Davao City', 'Baguio', 'Iloilo City', 'Cagayan de Oro'];
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Benguet', 'Iloilo', 'Misamis Oriental', 'Pampanga', 'Batangas', 'Laguna', 'Bulacan'];
        $materials = Material::all();
        $warehouses = Warehouse::all();
        if ($warehouses->isEmpty()) {
            $warehouses = collect([Warehouse::factory()->create(['name' => 'Warehouse A']), Warehouse::factory()->create(['name' => 'Warehouse B'])]);
        }
        foreach ($supplierNames as $i => $companyName) {
            $email = Str::slug($companyName, '_') . '@supplier.ph';
            $username = Str::slug($companyName, '_');
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $companyName,
                    'username' => $username,
                    'password' => Hash::make('PASS_123'),
                    'role' => 'supplier',
                    'user_type' => 'company',
                    'email_verified_at' => now(),
                ]
            );
            // Ensure supplier has a corresponding company record
            \App\Models\Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $companyName,
                    'contact_person' => 'Juan Dela Cruz',
                    'email' => $email,
                    'username' => $username,
                    'mobile_number' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'telephone_number' => '+63' . rand(2, 8) . rand(1000000, 9999999),
                    'street' => rand(1, 999) . ' ' . Arr::random($cities) . ' Street',
                    'city' => Arr::random($cities),
                    'state' => Arr::random($provinces),
                    'postal' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'years_operation' => rand(1, 30),
                    'business_size' => Arr::random(['Small', 'Medium', 'Large']),
                    'service_areas' => Arr::random($provinces) . ', ' . Arr::random($cities),
                    'vat_registered' => rand(0, 1),
                    'use_sureprice' => rand(0, 1),
                    'payment_terms' => Arr::random(['30 days', '45 days', '60 days', 'COD', 'EOM']),
                    'designation' => 'supplier',
                    'status' => 'approved',
                ]
            );
            $supplier = Supplier::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $companyName,
                    'contact_person' => 'Juan Dela Cruz',
                    'email' => $email,
                    'phone' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'address' => Arr::random($cities) . ', ' . Arr::random($provinces),
                    'tax_number' => 'TIN-' . rand(100000, 999999),
                    'registration_number' => 'REG-' . rand(100000, 999999),
                    'status' => 'active',
                    'company_id' => null,
                ]
            );
            // Assign a random subset of materials (5-12 per supplier)
            $materialSubset = $materials->random(rand(5, min(12, $materials->count())));
            foreach ($materialSubset as $material) {
                $price = rand(100, 1000);
                $leadTime = rand(1, 14) . ' days';
                $supplier->materials()->syncWithoutDetaching([
                    $material->id => [
                        'price' => $price,
                        'lead_time' => $leadTime,
                        'is_preferred' => (bool)rand(0, 1),
                        'approval_status' => 'approved',
                    ]
                ]);
                // Stock for this supplier-material in a random warehouse
                $warehouse = $warehouses->random();
                Stock::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'supplier_id' => $supplier->id,
                        'material_id' => $material->id,
                    ],
                    [
                        'current_stock' => rand(20, 200),
                        'threshold' => rand(5, 20),
                    ]
                );
            }
            // Seed 3-5 evaluations per supplier
            for ($j = 0; $j < rand(3, 5); $j++) {
                $date = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(0, 30));
                SupplierEvaluation::updateOrCreate(
                    [
                        'supplier_id' => $supplier->id,
                        'evaluation_date' => $date,
                    ],
                    [
                        'engagement_score' => rand(30, 50) / 10,
                        'delivery_speed_score' => rand(30, 50) / 10,
                        'delivery_ontime_ratio' => rand(80, 100),
                        'performance_score' => rand(30, 50) / 10,
                        'quality_score' => rand(30, 50) / 10,
                        'defect_ratio' => rand(0, 5) / 10,
                        'cost_variance_score' => rand(30, 50) / 10,
                        'cost_variance_ratio' => rand(0, 5) / 10,
                        'sustainability_score' => rand(30, 50) / 10,
                        'final_score' => rand(30, 50) / 10,
                    ]
                );
            }
            // Current evaluation
            SupplierEvaluation::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'evaluation_date' => Carbon::now(),
                ],
                [
                    'engagement_score' => rand(30, 50) / 10,
                    'delivery_speed_score' => rand(30, 50) / 10,
                    'delivery_ontime_ratio' => rand(80, 100),
                    'performance_score' => rand(30, 50) / 10,
                    'quality_score' => rand(30, 50) / 10,
                    'defect_ratio' => rand(0, 5) / 10,
                    'cost_variance_score' => rand(30, 50) / 10,
                    'cost_variance_ratio' => rand(0, 5) / 10,
                    'sustainability_score' => rand(30, 50) / 10,
                    'final_score' => rand(30, 50) / 10,
                ]
            );
        }
    }
} 