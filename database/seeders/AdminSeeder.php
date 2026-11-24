<?php

namespace Database\Seeders;

// database/seeders/AdminSeeder.php
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Demo accounts with a fixed, published password: never on a live system.
        if (app()->isProduction()) {
            $this->command?->warn(static::class . ' skipped: it creates demo accounts and must not run in production.');
            return;
        }
        $admins = [
            [
                'name' => 'Manager One',
                'username' => 'manager1',
                'email' => 'manager1@example.com',
                'password' => Hash::make('PASS_123'),
                'user_type' => 'manager',
                'role' => 'manager',
            ],
            // Admin for oversight
            [
                'name' => 'Admin One',
                'username' => 'admin1',
                'email' => 'admin1@example.com',
                'password' => Hash::make('PASS_123'),
                'user_type' => 'admin',
                'role' => 'admin',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']], // Unique identifier
                $admin
            );
        }
    }
}
