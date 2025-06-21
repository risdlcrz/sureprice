<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $supplierCompanies = Company::where('supplier_type', '!=', 'Individual')->get();
        

        foreach ($supplierCompanies as $index => $company) {
            $contact = $contactPersons[$index % count($contactPersons)];
            Supplier::updateOrCreate(
                [
                    'company_name' => $company->company_name,
                ],
                [
                    'contact_person' => $contact,
                    'email' => $company->email,
                    'phone' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'address' => $company->street . ', ' . $company->city . ', ' . $company->state,
                    'tax_number' => 'TIN-' . rand(100000000, 999999999),
                    'registration_number' => $company->business_reg_no,
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Suppliers seeded successfully!');
    }
} 