<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Party;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class EnsureClientPartyRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:ensure-party-records {--user-id= : Specific user ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all client users have proper party records linked to their company information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $users = User::where('id', $userId)->where('user_type', 'company')->where('role', 'client')->get();
        } else {
            $users = User::where('user_type', 'company')->where('role', 'client')->get();
        }

        if ($users->isEmpty()) {
            $this->info('No client users found.');
            return;
        }

        $this->info("Found {$users->count()} client user(s) to process.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $fixed = 0;
        $created = 0;

        foreach ($users as $user) {
            try {
                $result = $this->ensureUserPartyRecord($user);
                if ($result === 'fixed') {
                    $fixed++;
                } elseif ($result === 'created') {
                    $created++;
                }
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError processing user {$user->id}: " . $e->getMessage());
                Log::error("Error ensuring party record for user {$user->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Client party records ensured successfully!");
        $this->info("Fixed: $fixed, Created: $created");
    }

    private function ensureUserPartyRecord(User $user)
    {
        $company = $user->company;
        
        if (!$company) {
            $this->line("\nUser {$user->id} has no company record.");
            return 'no_company';
        }

        // Check if user already has a party relationship
        if ($user->party) {
            $this->line("\nUser {$user->id} already has a party relationship.");
            return 'already_exists';
        }

        // Try to find existing party by email or company name
        $party = Party::where(function($query) use ($company) {
            $query->where('email', $company->email)
                  ->orWhere('company_name', $company->company_name)
                  ->orWhere('name', $company->contact_person);
        })->where('entity_type', 'client')->first();
        
        if ($party) {
            // Update the party with user_id if missing
            if (!$party->user_id) {
                $party->update(['user_id' => $user->id]);
                $this->line("\nUpdated party {$party->id} with user_id {$user->id}");
                return 'fixed';
            }
            return 'already_correct';
        }

        // Create new party record
        $party = Party::create([
            'entity_type' => 'client',
            'name' => $company->contact_person,
            'company_name' => $company->company_name,
            'email' => $company->email,
            'phone' => $company->mobile_number ?? $company->telephone_number ?? 'N/A',
            'street' => $company->street ?? 'N/A',
            'barangay' => $company->barangay ?? 'N/A',
            'city' => $company->city ?? 'N/A',
            'state' => $company->state ?? 'N/A',
            'postal' => $company->postal ?? 'N/A',
            'user_id' => $user->id,
        ]);
        
        $this->line("\nCreated new party record for user {$user->id}: {$party->name}");
        return 'created';
    }
} 