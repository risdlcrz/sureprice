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
        $contactPersons = [
            'Juan Dela Cruz', 'Maria Santos', 'Jose Reyes', 'Ana Bautista', 'Pedro Garcia', 'Carmen Mendoza',
            'Ramon Torres', 'Luz Ocampo', 'Carlos Mercado', 'Rosa Ramos', 'Miguel Aquino', 'Teresa Castro',
            'Alfredo Martinez', 'Patricia Sanchez', 'Fernando Flores', 'Elena Rivera', 'Roberto Morales',
            'Isabel Gonzales', 'Ricardo Perez', 'Dolores Gomez', 'Eduardo Diaz', 'Victoria Romero',
            'Jorge Herrera', 'Gloria Espinosa', 'Daniel Valdez', 'Sandra Molina', 'Mario Ortiz', 'Helen Silva',
            'Santiago Cruz', 'Grace Moreno', 'Felipe Jimenez', 'Rebecca Munoz', 'Andres Alvarez', 'Amy Ruiz',
            'Enrique Herrera', 'Janet Medina', 'Joaquin Aguilar', 'Sharon Vargas', 'Alejandro Castillo',
            'Donna Cortez', 'Diego Lopez', 'Carol Gutierrez', 'Gabriel Castro', 'Margaret Fernandez',
            'Adrian Ramirez', 'Joyce Reyes', 'Christian Torres', 'Shirley Morales', 'Mark Ortiz', 'Betty Silva',
            'John Cruz', 'Virginia Moreno', 'Michael Jimenez', 'Frances Munoz', 'David Alvarez', 'Jean Ruiz',
            'James Herrera', 'Alice Medina', 'Robert Aguilar', 'Martha Vargas', 'Christopher Castillo',
            'Gloria Cortez', 'Sarah Lopez', 'Evelyn Gutierrez', 'Michelle Castro', 'Irene Fernandez',
            'Jennifer Ramirez', 'Florence Reyes', 'Jessica Torres', 'Lillian Morales', 'Amanda Ortiz',
            'Rose Silva', 'Nicole Cruz', 'Catherine Moreno', 'Stephanie Jimenez', 'Marie Munoz', 'Melissa Alvarez',
            'Ashley Ruiz', 'Rachel Herrera', 'Angela Medina', 'Kimberly Aguilar', 'Lisa Vargas', 'Heather Castillo',
            'Rebecca Cortez', 'Laura Lopez', 'Sharon Gutierrez', 'Cynthia Castro', 'Amy Fernandez', 'Deborah Ramirez',
            'Pamela Reyes', 'Donna Torres', 'Carol Morales', 'Sandra Ortiz', 'Brenda Silva', 'Diane Cruz',
            'Janet Moreno', 'Shirley Jimenez', 'Joyce Munoz', 'Margaret Alvarez', 'Helen Ruiz', 'Dorothy Herrera'
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
                ]
            );
        }

        $this->command->info('Suppliers seeded successfully!');
    }
} 