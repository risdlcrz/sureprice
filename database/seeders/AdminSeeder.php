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
                'name' => 'Juan Admin Dela Cruz',
                'username' => 'juandelacruz_admin',
                'email' => 'juan.admin@sureprice.com',
                'password' => Hash::make('PASS_123'), // DEMO/TEST PASSWORD
                'user_type' => 'admin',
                'role' => 'admin',
            ],
            [
                'name' => 'Maria Admin Santos',
                'username' => 'mariaadmin_santos',
                'email' => 'maria.admin@sureprice.com',
                'password' => Hash::make('PASS_123'), // DEMO/TEST PASSWORD
                'user_type' => 'admin',
                'role' => 'admin',
            ],
            [
                'name' => 'Main Admin',
                'username' => 'mainadmin',
                'email' => 'admin1@example.com',
                'password' => Hash::make('PASS_123'), // DEMO/TEST PASSWORD
                'user_type' => 'admin',
                'role' => 'admin',
            ],
            [
                'name' => 'Backup Admin',
                'username' => 'backupadmin',
                'email' => 'admin2@example.com',
                'password' => Hash::make('PASS_123'), // DEMO/TEST PASSWORD
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