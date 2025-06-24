<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;

class FixCompanyUserLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:company-user-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure every client/supplier user has a company with status approved and correct user_id.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fixed = 0;
        $created = 0;
        $users = User::whereIn('user_type', ['client', 'supplier'])->get();
        foreach ($users as $user) {
            $company = Company::where('user_id', $user->id)->first();
            if ($company) {
                if ($company->status !== 'approved') {
                    $company->status = 'approved';
                    $company->save();
                    $this->info("Fixed status for company {$company->company_name} (User: {$user->username})");
                    $fixed++;
                }
            } else {
                // Create a new company for this user
                $company = Company::create([
                    'user_id' => $user->id,
                    'company_name' => $user->username . ' Company',
                    'contact_person' => $user->username,
                    'designation' => $user->role,
                    'email' => $user->email,
                    'username' => $user->username,
                    'mobile_number' => '+639000000000',
                    'telephone_number' => null,
                    'street' => 'Unknown',
                    'barangay' => 'Unknown',
                    'city' => 'Unknown',
                    'state' => 'Unknown',
                    'postal' => '0000',
                    'supplier_type' => 'Individual',
                    'other_supplier_type' => null,
                    'business_reg_no' => null,
                    'years_operation' => 1,
                    'business_size' => 'Solo',
                    'service_areas' => null,
                    'vat_registered' => false,
                    'use_sureprice' => false,
                    'payment_terms' => '7 days',
                    'status' => 'approved',
                ]);
                $this->info("Created company for user {$user->username}");
                $created++;
            }
        }
        $this->info("Done. Fixed: $fixed, Created: $created");
    }
}
