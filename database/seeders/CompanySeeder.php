<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        
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