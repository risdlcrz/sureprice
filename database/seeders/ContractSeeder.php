<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Party;
use App\Models\Project;
use App\Models\Material;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = Project::all();
        $contractors = Party::where('entity_type', 'contractor')->get();
        $clients = Party::where('entity_type', 'client')->get();
        $materials = Material::all();

        if ($projects->isEmpty() || $contractors->isEmpty() || $clients->isEmpty() || $materials->isEmpty()) {
            $this->command->info('Missing data for projects, parties, or materials. Skipping ContractSeeder.');
            return;
        }

        foreach ($projects as $project) {
            $client = $clients->random();
            $contractor = $contractors->random();

            $contract = Contract::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'client_id' => $client->id,
                    'contractor_id' => $contractor->id,
                ],
                [
                    'contract_number' => 'CON-' . time() . '-' . $project->id,
                    'title' => 'General Construction Contract for ' . $project->name,
                    'description' => 'This contract covers the general construction works for the project.',
                    'total_amount' => 0, // Will be calculated from items
                    'labor_cost' => $project->budget * (rand(15, 30) / 100), // 15-30% of budget
                    'materials_cost' => 0, // Will be calculated
                    'payment_method' => ['Bank Transfer', 'Check', 'Cash'][rand(0, 2)],
                    'payment_terms' => '30% downpayment, progress billing every 2 weeks.',
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'status' => ['draft', 'active', 'completed'][rand(0, 2)],
                ]
            );

            $materialsCost = 0;
            // Add 5 to 15 random materials as contract items
            for ($i = 0; $i < rand(5, 15); $i++) {
                $material = $materials->random();
                $quantity = rand(10, 200);
                $amount = $material->srp_price;
                $total = $quantity * $amount;
                $materialsCost += $total;

                ContractItem::create([
                    'contract_id' => $contract->id,
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'unit' => $material->unit,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'total' => $total,
                ]);
            }

            // Update contract with calculated costs
            $contract->materials_cost = $materialsCost;
            $contract->total_amount = $contract->labor_cost + $materialsCost;
            $contract->save();
        }
        
        $this->command->info('Contracts and Contract Items seeded successfully!');
    }
} 