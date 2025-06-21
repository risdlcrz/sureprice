<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Party;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $entityType = '';
            if ($company->supplier_type === 'Individual') { // Clients
                $entityType = 'client';
            } elseif ($company->supplier_type === 'Contractor') {
                $entityType = 'contractor';
            } else {
                continue; // We only need clients and contractors in the parties table for now
            }

            Party::updateOrCreate(
                ['email' => $company->email],
                [
                    'entity_type' => $entityType,
                    'name' => $company->contact_person,
                    'company_name' => $company->company_name,
                    'street' => $company->street,
                    'unit' => rand(1, 100) . 'A',
                    'barangay' => 'Barangay ' . rand(1, 100),
                    'city' => $company->city,
                    'state' => $company->state,
                    'postal' => $company->postal,
                    'phone' => $company->mobile_number,
                ]
            );
        }

        $this->command->info('Parties seeded successfully!');
    }
} 