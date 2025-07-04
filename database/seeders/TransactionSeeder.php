<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Contract;
use App\Models\User;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $contracts = Contract::all();
        $users = User::all();
        $types = ['payment', 'refund', 'adjustment'];
        $statuses = ['pending', 'completed', 'failed'];
        $methods = ['Bank Transfer', 'Check', 'Cash'];
        foreach ($contracts as $contract) {
            // Seed 3-6 past transactions per contract
            for ($i = 0; $i < rand(3, 6); $i++) {
                $date = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(0, 30));
                Transaction::create([
                    'contract_id' => $contract->id,
                    'date' => $date,
                    'description' => 'Transaction for contract ' . $contract->contract_number,
                    'amount' => rand(10000, 500000),
                    'type' => $types[array_rand($types)],
                    'status' => $statuses[array_rand($statuses)],
                    'payment_method' => $methods[array_rand($methods)],
                    'reference_number' => 'TXN-' . rand(100000, 999999),
                    'notes' => 'Auto-generated for demo',
                    'created_by' => $users->random()->id,
                ]);
            }
            // Seed a current/ongoing transaction
            Transaction::create([
                'contract_id' => $contract->id,
                'date' => Carbon::now(),
                'description' => 'Ongoing transaction for contract ' . $contract->contract_number,
                'amount' => rand(10000, 500000),
                'type' => $types[array_rand($types)],
                'status' => 'pending',
                'payment_method' => $methods[array_rand($methods)],
                'reference_number' => 'TXN-' . rand(100000, 999999),
                'notes' => 'Ongoing transaction for demo',
                'created_by' => $users->random()->id,
            ]);
        }
        $this->command->info('Transactions seeded successfully!');
    }
} 