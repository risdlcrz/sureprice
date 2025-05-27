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
        $admins = [
            [
                'name' => 'Main Admin',
                'username' => 'mainadmin',
                'email' => 'admin1@example.com', // Changed to unique
                'password' => Hash::make('AdminPass123!'),
                'user_type' => 'admin', // Matches your DB column
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Backup Admin', 
                'username' => 'backupadmin',
                'email' => 'admin2@example.com', // Changed to unique
                'password' => Hash::make('AdminPass456!'),
                'user_type' => 'admin', // Matches your DB column
                'email_verified_at' => now(),
            ]
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']], // Unique identifier
                $admin
            );
        }
    }
}