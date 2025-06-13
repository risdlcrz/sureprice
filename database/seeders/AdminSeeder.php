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
                'email' => 'admin1@example.com',
                'password' => Hash::make('AdminPass123!'),
                'user_type' => 'admin',
                'role' => 'admin',
            ],
            [
                'name' => 'Backup Admin', 
                'username' => 'backupadmin',
                'email' => 'admin2@example.com',
                'password' => Hash::make('AdminPass456!'),
                'user_type' => 'admin',
                'role' => 'admin',
            ],
            [
                'name' => 'Contractor One',
                'username' => 'contractor1',
                'email' => 'contractor1@example.com',
                'password' => Hash::make('ContractorPass1!'),
                'user_type' => 'contractor'
            ],
            [
                'name' => 'Contractor Two',
                'username' => 'contractor2',
                'email' => 'contractor2@example.com',
                'password' => Hash::make('ContractorPass2!'),
                'user_type' => 'contractor'
            ],
            [
                'name' => 'Client One',
                'username' => 'client1',
                'email' => 'client1@example.com',
                'password' => Hash::make('ClientPass1!'),
                'user_type' => 'client'
            ],
            [
                'name' => 'Client Two',
                'username' => 'client2',
                'email' => 'client2@example.com',
                'password' => Hash::make('ClientPass2!'),
                'user_type' => 'client'
            ],
            [
                'name' => 'Test User 1',
                'username' => 'testuser1',
                'email' => 'testuser1@example.com',
                'password' => Hash::make('TestPass1!'),
                'user_type' => 'client'
            ],
            [
                'name' => 'Test User 2',
                'username' => 'testuser2',
                'email' => 'testuser2@example.com',
                'password' => Hash::make('TestPass2!'),
                'user_type' => 'contractor'
            ],
            [
                'name' => 'Test User 3',
                'username' => 'testuser3',
                'email' => 'testuser3@example.com',
                'password' => Hash::make('TestPass3!'),
                'user_type' => 'client'
            ],
            [
                'name' => 'Test User 4',
                'username' => 'testuser4',
                'email' => 'testuser4@example.com',
                'password' => Hash::make('TestPass4!'),
                'user_type' => 'contractor'
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