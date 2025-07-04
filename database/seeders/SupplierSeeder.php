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
        
        // DEMO/TEST DATA: All seeded suppliers are for testing/demo purposes
        $contactPersons = [
            'Juan Dela Cruz', 'Maria Santos', 'Jose Reyes', 'Ana Garcia', 'Pedro Mendoza', 'Luz Torres', 'Carlos Gonzales', 'Rosa Ramos', 'Antonio Lopez', 'Carmen Aquino',
            'Miguel Cruz', 'Isabel Bautista', 'Manuel Castro', 'Teresa Flores', 'Francisco Morales', 'Gloria Gutierrez', 'Ramon Navarro', 'Elena Domingo', 'Roberto Silva', 'Patricia Padilla',
            'Andres Soriano', 'Emilio Villanueva', 'Estrella Aguilar', 'Julio Salazar', 'Ligaya Rosales', 'Mariano Valdez', 'Nicanor Santiago', 'Pilar Pascual', 'Salvador Rivera', 'Victoria Ocampo'
        ];

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
                    'user_id' => $company->user_id,
                    'company_id' => $company->id,
                ]
            );
        }

        $this->command->info('Suppliers seeded successfully!');
    }
} 