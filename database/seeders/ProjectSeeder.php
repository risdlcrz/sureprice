<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientCompanies = Company::where('supplier_type', 'Individual')->get();

        if ($clientCompanies->isEmpty()) {
            $this->command->info('No client companies found, skipping ProjectSeeder.');
            return;
        }

        foreach ($clientCompanies as $company) {
            Project::updateOrCreate(
                ['client_name' => $company->company_name],
                [
                    'name' => 'Construction of ' . $company->company_name . ' Residence',
                    'description' => 'A 2-story residential project for ' . $company->company_name,
                    'start_date' => Carbon::now()->subDays(rand(10, 60)),
                    'end_date' => Carbon::now()->addDays(rand(90, 365)),
                    'status' => ['active', 'planning', 'completed'][rand(0, 2)],
                    'budget' => rand(1000000, 10000000),
                ]
            );
        }

        $this->command->info('Projects seeded successfully!');
    }
} 