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
        $paymentSchedule = $contract->generatePaymentSchedule();

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