<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Philippine company names (realistic)
        $clientCompanyNames = [
            'ABC Development Corporation', 'XYZ Real Estate Inc.', 'Metro Builders Group', 'Pacific Construction Co.',
            'Golden Properties Ltd.', 'Silver Star Development', 'Blue Ocean Builders', 'Green Earth Construction',
            'Red Diamond Properties', 'White Pearl Development', 'Black Gold Construction', 'Purple Heart Builders',
            'Orange Sunset Properties', 'Yellow Sun Development', 'Pink Rose Construction', 'Brown Earth Builders',
            'Gray Stone Properties', 'Cream City Development', 'Navy Blue Construction', 'Teal Ocean Builders',
            'Maroon Valley Properties', 'Olive Green Development', 'Lime Light Construction', 'Coral Reef Builders',
            'Indigo Sky Properties', 'Violet Moon Development', 'Amber Gold Construction', 'Ruby Red Builders',
            'Emerald Green Properties', 'Sapphire Blue Development', 'Diamond White Construction', 'Platinum Star Builders',
            'Titanium Gray Properties', 'Copper Brown Development', 'Bronze Age Construction', 'Iron Man Builders',
            'Steel Strong Properties', 'Aluminum Light Development', 'Zinc Bright Construction', 'Nickel Shine Builders',
            'Lead Heavy Properties', 'Tin Can Development', 'Mercury Quick Construction', 'Venus Bright Builders',
            'Mars Red Properties', 'Jupiter Big Development', 'Saturn Ring Construction', 'Uranus Far Builders',
            'Neptune Blue Properties', 'Pluto Small Development', 'Sun Bright Construction', 'Moon Light Builders'
        ];

        $supplierCompanyNames = [
            'Philippine Cement Corp.', 'Manila Steel Supply', 'Luzon Hardware Co.', 'Visayas Construction Materials',
            'Mindanao Tools & Equipment', 'Metro Manila Lumber', 'Cebu Electrical Supply', 'Davao Plumbing Materials',
            'Baguio Paint & Coatings', 'Iloilo Roofing Supply', 'Zamboanga Concrete Products', 'Cagayan Valley Aggregates',
            'Central Luzon Sand & Gravel', 'Southern Tagalog Steel', 'Bicol Region Hardware', 'Western Visayas Tools',
            'Eastern Visayas Equipment', 'Northern Mindanao Supplies', 'Southern Mindanao Materials', 'Caraga Region Hardware',
            'Cordillera Construction Supply', 'Ilocos Region Tools', 'Cagayan Valley Materials', 'Central Luzon Hardware',
            'Calabarzon Construction Supply', 'Mimaropa Tools & Equipment', 'Bicol Hardware Supply', 'Western Visayas Materials',
            'Central Visayas Construction', 'Eastern Visayas Hardware', 'Zamboanga Peninsula Supply', 'Northern Mindanao Tools',
            'Davao Region Materials', 'Soccsksargen Hardware', 'Caraga Construction Supply', 'Bangsamoro Tools & Equipment',
            'National Capital Region Supply', 'Cordillera Administrative Hardware', 'Ilocos Region Construction', 'Cagayan Valley Tools',
            'Central Luzon Materials', 'Calabarzon Hardware Supply', 'Mimaropa Construction', 'Bicol Region Tools',
            'Western Visayas Materials', 'Central Visayas Hardware', 'Eastern Visayas Supply', 'Zamboanga Peninsula Tools',
            'Northern Mindanao Materials', 'Davao Region Hardware', 'Soccsksargen Construction', 'Caraga Tools & Equipment',
            'Bangsamoro Materials Supply', 'NCR Hardware Co.', 'CAR Construction Supply', 'Region I Tools & Materials'
        ];

        // Philippine cities and provinces
        $cities = [
            'Manila', 'Quezon City', 'Caloocan', 'Las Piñas', 'Makati', 'Malabon', 'Mandaluyong', 'Marikina',
            'Muntinlupa', 'Navotas', 'Parañaque', 'Pasay', 'Pasig', 'San Juan', 'Taguig', 'Valenzuela',
            'Antipolo', 'Bacoor', 'Cabuyao', 'Cainta', 'Calamba', 'Dasmariñas', 'General Trias', 'Imus',
            'San Pedro', 'Santa Rosa', 'Trece Martires', 'Angeles', 'Bacolod', 'Baguio', 'Batangas City',
            'Cebu City', 'Davao City', 'Iloilo City', 'Zamboanga City', 'Cagayan de Oro', 'General Santos'
        ];

        $provinces = [
            'Metro Manila', 'Cavite', 'Laguna', 'Batangas', 'Rizal', 'Bulacan', 'Pampanga', 'Tarlac',
            'Nueva Ecija', 'Zambales', 'Bataan', 'Aurora', 'Quezon', 'Camarines Norte', 'Camarines Sur',
            'Albay', 'Sorsogon', 'Masbate', 'Catanduanes', 'Iloilo', 'Negros Occidental', 'Cebu', 'Bohol',
            'Siquijor', 'Negros Oriental', 'Leyte', 'Southern Leyte', 'Biliran', 'Samar', 'Eastern Samar',
            'Northern Samar', 'Zamboanga del Norte', 'Zamboanga del Sur', 'Zamboanga Sibugay', 'Bukidnon',
            'Misamis Occidental', 'Misamis Oriental', 'Lanao del Norte', 'Lanao del Sur', 'Davao del Norte',
            'Davao del Sur', 'Davao Oriental', 'Davao Occidental', 'Compostela Valley', 'Agusan del Norte',
            'Agusan del Sur', 'Surigao del Norte', 'Surigao del Sur', 'Dinagat Islands'
        ];

        // Designations
        $designations = [
            'President', 'Vice President', 'General Manager', 'Operations Manager', 'Project Manager',
            'Business Development Manager', 'Sales Manager', 'Marketing Manager', 'Finance Manager',
            'Human Resources Manager', 'Administrative Manager', 'Technical Manager', 'Engineering Manager',
            'Construction Manager', 'Procurement Manager', 'Logistics Manager', 'Quality Control Manager',
            'Safety Manager', 'Maintenance Manager', 'IT Manager', 'Legal Manager', 'Compliance Manager'
        ];

        // Supplier types
        $supplierTypes = ['Individual', 'Contractor', 'Material Supplier', 'Equipment Rental', 'Other'];

        // Business sizes
        $businessSizes = ['Solo', 'Small Enterprise', 'Medium', 'Large'];

        // Payment terms
        $paymentTerms = ['7 days', '15 days', '30 days'];

        // Get company users
        $clientUsers = User::where('user_type', 'client')->get();
        $supplierUsers = User::where('user_type', 'supplier')->get();

        $companyCount = 20;

        // Create client companies
        for ($i = 0; $i < $companyCount / 2; $i++) {
            $user = $clientUsers->get($i);
            if (!$user) continue;

            $city = $cities[array_rand($cities)];
            $province = $provinces[array_rand($provinces)];
            $companyName = $clientCompanyNames[$i] ?? 'Client Company ' . ($i + 1);
            
            // Split the name to get first and last name
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0];
            $lastName = end($nameParts);

            Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'email' => $user->email,
                    'company_name' => $companyName,
                    'supplier_type' => 'Individual', // Clients are typically individuals
                    'other_supplier_type' => null,
                    'business_reg_no' => 'BRN-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'contact_person' => $user->name,
                    'designation' => 'client',
                    'mobile_number' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'telephone_number' => '+63' . rand(2, 8) . rand(1000000, 9999999),
                    'street' => rand(1, 999) . ' ' . ['Rizal', 'Bonifacio', 'Aguinaldo', 'Luna', 'Mabini', 'Burgos', 'Jacinto', 'Del Pilar'][array_rand(['Rizal', 'Bonifacio', 'Aguinaldo', 'Luna', 'Mabini', 'Burgos', 'Jacinto', 'Del Pilar'])] . ' Street',
                    'city' => $city,
                    'state' => $province,
                    'postal' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'years_operation' => rand(1, 30),
                    'business_size' => $businessSizes[array_rand($businessSizes)],
                    'service_areas' => $province . ', ' . $city,
                    'vat_registered' => rand(0, 1),
                    'use_sureprice' => rand(0, 1),
                    'payment_terms' => $paymentTerms[array_rand($paymentTerms)],
                    'status' => 'approved',
                ]
            );
        }

        // Create supplier companies
        for ($i = 0; $i < $companyCount / 2; $i++) {
            $user = $supplierUsers->get($i);
            if (!$user) continue;

            $city = $cities[array_rand($cities)];
            $province = $provinces[array_rand($provinces)];
            $companyName = $supplierCompanyNames[$i] ?? 'Supplier Company ' . ($i + 1);
            $supplierType = $supplierTypes[array_rand($supplierTypes)];
            
            // Split the name to get first and last name
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0];
            $lastName = end($nameParts);

            Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'email' => $user->email,
                    'company_name' => $companyName,
                    'supplier_type' => $supplierType,
                    'other_supplier_type' => $supplierType === 'Other' ? 'Specialized Supplier' : null,
                    'business_reg_no' => 'BRN-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'contact_person' => $user->name,
                    'designation' => 'supplier',
                    'mobile_number' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'telephone_number' => '+63' . rand(2, 8) . rand(1000000, 9999999),
                    'street' => rand(1, 999) . ' ' . ['Rizal', 'Bonifacio', 'Aguinaldo', 'Luna', 'Mabini', 'Burgos', 'Jacinto', 'Del Pilar'][array_rand(['Rizal', 'Bonifacio', 'Aguinaldo', 'Luna', 'Mabini', 'Burgos', 'Jacinto', 'Del Pilar'])] . ' Street',
                    'city' => $city,
                    'state' => $province,
                    'postal' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'years_operation' => rand(1, 30),
                    'business_size' => $businessSizes[array_rand($businessSizes)],
                    'service_areas' => $province . ', ' . $city,
                    'vat_registered' => rand(0, 1),
                    'use_sureprice' => rand(0, 1),
                    'payment_terms' => $paymentTerms[array_rand($paymentTerms)],
                    'status' => 'approved',
                ]
            );
        }

        $this->command->info('Companies seeded successfully!');
    }
} 