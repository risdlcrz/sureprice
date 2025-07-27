<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Party;
use Illuminate\Support\Facades\Log;

class FixClientPartyRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-client-party-relationships {--user-id= : Specific user ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix client party relationships for users who don\'t have party records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $users = User::where('id', $userId)->where('role', 'client')->get();
        } else {
            $users = User::where('role', 'client')->get();
        }

        if ($users->isEmpty()) {
            $this->info('No client users found.');
            return;
        }

        $this->info("Found {$users->count()} client user(s) to process.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            try {
                $this->fixUserPartyRelationship($user);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError processing user {$user->id}: " . $e->getMessage());
                Log::error("Error fixing party relationship for user {$user->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Client party relationships fixed successfully!');
    }

    private function fixUserPartyRelationship(User $user)
    {
        // Check if user already has a party relationship
        if ($user->party) {
            $this->line("\nUser {$user->id} already has a party relationship.");
            return;
        }

        // Try to find existing party by email
        $party = Party::where('email', $user->email)->first();
        
        if (!$party) {
            // Create new party record
            $party = Party::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? 'N/A',
                'type' => 'client',
                'entity_type' => 'company',
                'street' => 'N/A',
                'barangay' => 'N/A',
                'city' => 'N/A',
                'state' => 'N/A',
                'postal' => 'N/A',
            ]);
            
            $this->line("\nCreated new party record for user {$user->id}: {$party->name}");
        } else {
            $this->line("\nFound existing party record for user {$user->id}: {$party->name}");
        }

        // Update user with party_id
        $user->update(['party_id' => $party->id]);
        
        $this->line("Linked user {$user->id} to party {$party->id}");
    }
} 