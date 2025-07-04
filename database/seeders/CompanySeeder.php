<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // DEMO/TEST DATA: 50 companies (25 clients, 25 suppliers)
        $companyCount = 50;
        $cities = ['Quezon City', 'Manila', 'Makati', 'Pasig', 'Taguig', 'Cebu City', 'Davao City', 'Baguio', 'Iloilo City', 'Cagayan de Oro', 'San Juan', 'Mandaluyong', 'Paranaque', 'Las Pinas', 'Valenzuela', 'Caloocan', 'Marikina', 'Muntinlupa', 'Navotas', 'Malabon'];
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Benguet', 'Iloilo', 'Misamis Oriental', 'Pampanga', 'Batangas', 'Laguna', 'Bulacan'];
        $businessSizes = ['Small', 'Medium', 'Large'];
        $paymentTerms = ['30 days', '45 days', '60 days', 'COD', 'EOM'];
        $supplierTypes = ['Distributor', 'Manufacturer', 'Retailer', 'Wholesaler', 'Other'];
        $clientCompanyNames = [];
        $supplierCompanyNames = [
            'Aguila Construction Supply',
            'Bayanihan Builders',
            'Luzviminda Hardware',
            'Mabuhay Materials Co.',
            'Pinoy Prime Supplies',
            'Tagumpay Trading',
            'Lakbay Construction Depot',
            'Matatag Merchandising',
            'Gintong Ani Enterprises',
            'Silangan Steel & Cement',
            'Bagong Bayan Hardware',
            'Isla Construction Depot',
            'Kapitbahay Supplies',
            'Malasakit Materials',
            'Sulong Construction',
            'Tibay Trading',
            'Bughaw Builders',
            'Dalisay Depot',
            'Luntian Hardware',
            'Pilipinas Prime Supply',
            'Kaagapay Construction',
            'Sikap Merchandising',
            'Bayanihan Steel',
            'Lakambini Materials',
            'Tagumpay Hardware'
        ];
        for ($i = 1; $i <= 25; $i++) {
            $clientCompanyNames[] = 'Client Company ' . $i;
            $supplierCompanyNames[] = 'Supplier Company ' . $i;
        }

        // Get company users
        $clientUsers = User::where('user_type', 'company')->where('role', 'client')->get();
        $supplierUsers = User::where('user_type', 'company')->where('role', 'supplier')->get();

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