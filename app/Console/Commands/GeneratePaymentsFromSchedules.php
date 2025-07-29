<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use Illuminate\Support\Facades\Log;

class GeneratePaymentsFromSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:generate-from-schedules {--contract-id= : Specific contract ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate payment records from existing payment schedules for contracts that don\'t have payment records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractId = $this->option('contract-id');
        
        if ($contractId) {
            $contracts = Contract::where('id', $contractId)
                ->where('status', 'approved')
                ->whereNotNull('payment_schedule')
                ->where('payment_schedule', '!=', '')
                ->where('payment_schedule', '!=', '[]')
                ->get();
        } else {
            $contracts = Contract::where('status', 'approved')
                ->whereNotNull('payment_schedule')
                ->where('payment_schedule', '!=', '')
                ->where('payment_schedule', '!=', '[]')
                ->get();
        }

        if ($contracts->isEmpty()) {
            $this->info('No contracts found with payment schedules.');
            return;
        }

        $this->info("Found {$contracts->count()} contract(s) with payment schedules.");

        $bar = $this->output->createProgressBar($contracts->count());
        $bar->start();

        $generatedCount = 0;
        $errorCount = 0;

        foreach ($contracts as $contract) {
            try {
                // Check if payments already exist for this contract
                if ($contract->payments()->count() === 0) {
                    $contract->generatePayments();
                    $generatedCount++;
                    $this->line("\nGenerated payments for contract: {$contract->contract_number} (ID: {$contract->id})");
                } else {
                    $this->line("\nContract {$contract->contract_number} (ID: {$contract->id}) already has payment records.");
                }
                $bar->advance();
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("\nError processing contract {$contract->id}: " . $e->getMessage());
                Log::error("Error generating payments for contract {$contract->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        
        if ($generatedCount > 0) {
            $this->info("Successfully generated payments for {$generatedCount} contract(s)!");
        }
        
        if ($errorCount > 0) {
            $this->warn("Encountered errors with {$errorCount} contract(s). Check the logs for details.");
        }
        
        if ($generatedCount === 0 && $errorCount === 0) {
            $this->info("All contracts already have payment records generated.");
        }
    }
} 