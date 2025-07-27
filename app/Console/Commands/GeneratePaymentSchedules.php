<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use Illuminate\Support\Facades\Log;

class GeneratePaymentSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:generate-payment-schedules {--contract-id= : Specific contract ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate payment schedules for contracts that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractId = $this->option('contract-id');
        
        if ($contractId) {
            $contracts = Contract::where('id', $contractId)->get();
        } else {
            $contracts = Contract::where('status', 'approved')
                ->where(function($query) {
                    $query->whereNull('payment_schedule')
                          ->orWhere('payment_schedule', '')
                          ->orWhere('payment_schedule', '[]');
                })
                ->get();
        }

        if ($contracts->isEmpty()) {
            $this->info('No contracts found that need payment schedules generated.');
            return;
        }

        $this->info("Found {$contracts->count()} contract(s) to process.");

        $bar = $this->output->createProgressBar($contracts->count());
        $bar->start();

        foreach ($contracts as $contract) {
            try {
                $this->generatePaymentSchedule($contract);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError processing contract {$contract->id}: " . $e->getMessage());
                Log::error("Error generating payment schedule for contract {$contract->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Payment schedules generated successfully!');
    }

    private function generatePaymentSchedule(Contract $contract)
    {
        $paymentSchedule = [];
        
        if ($contract->payment_plan) {
            $plan = $contract->payment_plan;
            $total = $contract->total_amount;
            
            if ($plan === '30% down, 40% halfway, 30% on completion') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.30,
                    'due_date' => $contract->start_date->format('Y-m-d')
                ];
                
                // Calculate halfway date (middle of project duration)
                $projectDuration = $contract->start_date->diffInDays($contract->end_date);
                $halfwayDate = $contract->start_date->copy()->addDays($projectDuration / 2);
                
                $paymentSchedule[] = [
                    'stage' => 'Halfway Payment',
                    'amount' => $total * 0.40,
                    'due_date' => $halfwayDate->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.30,
                    'due_date' => $contract->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === '50/50') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.50,
                    'due_date' => $contract->start_date->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.50,
                    'due_date' => $contract->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'Full upon completion') {
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total,
                    'due_date' => $contract->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'milestone') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.20,
                    'due_date' => $contract->start_date->format('Y-m-d')
                ];
                
                // After Foundation (25% of project duration)
                $foundationDate = $contract->start_date->copy()->addDays($contract->start_date->diffInDays($contract->end_date) * 0.25);
                $paymentSchedule[] = [
                    'stage' => 'After Foundation',
                    'amount' => $total * 0.20,
                    'due_date' => $foundationDate->format('Y-m-d')
                ];
                
                // After Structure (60% of project duration)
                $structureDate = $contract->start_date->copy()->addDays($contract->start_date->diffInDays($contract->end_date) * 0.60);
                $paymentSchedule[] = [
                    'stage' => 'After Structure',
                    'amount' => $total * 0.30,
                    'due_date' => $structureDate->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.30,
                    'due_date' => $contract->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'monthly3') {
                $monthlyAmount = $total / 3;
                for ($i = 1; $i <= 3; $i++) {
                    $dueDate = $contract->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
            elseif ($plan === 'monthly6') {
                $monthlyAmount = $total / 6;
                for ($i = 1; $i <= 6; $i++) {
                    $dueDate = $contract->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
            elseif ($plan === 'monthly12') {
                $monthlyAmount = $total / 12;
                for ($i = 1; $i <= 12; $i++) {
                    $dueDate = $contract->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
        }
        
        if (!empty($paymentSchedule)) {
            $contract->payment_schedule = json_encode($paymentSchedule);
            $contract->save();
            
            // Generate payment records if they don't exist
            if ($contract->payments()->count() === 0) {
                $contract->generatePayments();
            }
        }
    }
} 