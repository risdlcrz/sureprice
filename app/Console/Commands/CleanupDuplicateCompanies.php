<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;

class CleanupDuplicateCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:duplicate-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate companies for each user, keeping only the most recent approved one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereIn('user_type', ['client', 'supplier'])->get();
        $totalDeleted = 0;
        foreach ($users as $user) {
            $companies = Company::where('user_id', $user->id)->orderByDesc('status')->orderByDesc('updated_at')->get();
            if ($companies->count() > 1) {
                // Prefer the most recent approved company
                $toKeep = $companies->where('status', 'approved')->first() ?: $companies->first();
                $toDelete = $companies->filter(fn($c) => $c->id !== $toKeep->id);
                foreach ($toDelete as $del) {
                    $del->delete();
                    $this->info("Deleted company ID {$del->id} for user {$user->username}");
                    $totalDeleted++;
                }
                $this->info("Kept company ID {$toKeep->id} (status: {$toKeep->status}) for user {$user->username}");
            }
        }
        $this->info("Done. Total companies deleted: $totalDeleted");
    }
}
