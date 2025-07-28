<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Models\Party;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FixContractClientRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:fix-client-relationships {--contract-id= : Specific contract ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix contract-client relationships by ensuring contracts are linked to the correct client parties';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractId = $this->option('contract-id');
        
        if ($contractId) {
            $contracts = Contract::where('id', $contractId)->get();
        } else {
            $contracts = Contract::with(['client'])->get();
        }

        if ($contracts->isEmpty()) {
            $this->info('No contracts found.');
            return;
        }

        $this->info("Found {$contracts->count()} contract(s) to process.");

        $bar = $this->output->createProgressBar($contracts->count());
        $bar->start();

        $fixed = 0;
        $created = 0;

        foreach ($contracts as $contract) {
            try {
                $result = $this->fixContractClientRelationship($contract);
                if ($result === 'fixed') {
                    $fixed++;
                } elseif ($result === 'created') {
                    $created++;
                }
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError processing contract {$contract->id}: " . $e->getMessage());
                Log::error("Error fixing contract client relationship for contract {$contract->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Contract client relationships fixed successfully!");
        $this->info("Fixed: $fixed, Created: $created");
    }

    private function fixContractClientRelationship(Contract $contract)
    {
        // If contract already has a client party, check if it's correct
        if ($contract->client) {
            $clientParty = $contract->client;
            
            // Try to find a matching company for this party
            $matchingCompany = Company::where(function($query) use ($clientParty) {
                $query->where('company_name', $clientParty->company_name)
                      ->orWhere('contact_person', $clientParty->name)
                      ->orWhere('email', $clientParty->email);
            })->first();

            if ($matchingCompany) {
                // Update the party with user_id if missing
                if (!$clientParty->user_id && $matchingCompany->user_id) {
                    $clientParty->update(['user_id' => $matchingCompany->user_id]);
                    $this->line("\nUpdated party {$clientParty->id} with user_id {$matchingCompany->user_id}");
                    return 'fixed';
                }
                return 'already_correct';
            }
        }

        // Try to find the correct client party based on contract information
        $clientParty = null;
        
        // First, try to find by company name or contact person
        if ($contract->client) {
            $clientParty = Party::where(function($query) use ($contract) {
                $query->where('company_name', $contract->client->company_name)
                      ->orWhere('name', $contract->client->name)
                      ->orWhere('email', $contract->client->email);
            })->where('entity_type', 'client')->first();
        }

        // If not found, try to find by contract client information
        if (!$clientParty && $contract->client) {
            $clientParty = Party::where(function($query) use ($contract) {
                $query->where('company_name', $contract->client->company_name)
                      ->orWhere('name', $contract->client->name)
                      ->orWhere('email', $contract->client->email);
            })->where('entity_type', 'client')->first();
        }

        // If still not found, try to create from company records
        if (!$clientParty) {
            $matchingCompany = Company::where('designation', 'client')
                ->where(function($query) use ($contract) {
                    if ($contract->client) {
                        $query->where('company_name', $contract->client->company_name)
                              ->orWhere('contact_person', $contract->client->name)
                              ->orWhere('email', $contract->client->email);
                    }
                })->first();

            if ($matchingCompany) {
                // Create a new party record for this company
                $clientParty = Party::create([
                    'entity_type' => 'client',
                    'name' => $matchingCompany->contact_person,
                    'company_name' => $matchingCompany->company_name,
                    'email' => $matchingCompany->email,
                    'phone' => $matchingCompany->mobile_number ?? $matchingCompany->telephone_number ?? 'N/A',
                    'street' => $matchingCompany->street ?? 'N/A',
                    'barangay' => $matchingCompany->barangay ?? 'N/A',
                    'city' => $matchingCompany->city ?? 'N/A',
                    'state' => $matchingCompany->state ?? 'N/A',
                    'postal' => $matchingCompany->postal ?? 'N/A',
                    'user_id' => $matchingCompany->user_id,
                ]);

                $this->line("\nCreated new party record for company {$matchingCompany->company_name}");
                $created = true;
            }
        }

        // Update contract with the correct client_id
        if ($clientParty && $contract->client_id !== $clientParty->id) {
            $oldClientId = $contract->client_id;
            $contract->update(['client_id' => $clientParty->id]);
            $this->line("\nUpdated contract {$contract->id} client_id from {$oldClientId} to {$clientParty->id}");
            return 'fixed';
        }

        return 'no_change';
    }
} 