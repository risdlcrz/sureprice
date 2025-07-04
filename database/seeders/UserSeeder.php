<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        // Create admin users first
        $adminUsers = [
            [
                'name' => 'Admin System',
                'username' => 'admin',
                'email' => 'admin@sureprice.com',
                'user_type' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Maria Santos Admin',
                'username' => 'msantos_admin',
                'email' => 'msantos@sureprice.com',
                'user_type' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jose Reyes Admin',
                'username' => 'jreyes_admin',
                'email' => 'jreyes@sureprice.com',
                'user_type' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        ];

        foreach (
            $adminUsers as $admin
        ) {
            User::updateOrCreate(
                [
                    'username' => $admin['username'],
                ],
                $admin
            );
        }

        // Create employee users (procurement and warehousing)
        // This is now handled by EmployeeSeeder
        /*
        $employeeCount = 50;
        for ($i = 1; $i <= $employeeCount; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $role = $i <= 25 ? 'procurement' : 'warehousing';
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'username' => strtolower($firstName) . '_' . strtolower($lastName) . '_' . $i,
                'email' => strtolower($firstName) . '.' . strtolower($lastName) . $i . '@sureprice.com',
                'user_type' => 'employee',
                'role' => $role,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
        }
        */

        // More realistic Filipino names and emails
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Luz', 'Carlos', 'Rosa', 'Antonio', 'Carmen', 'Miguel', 'Isabel', 'Manuel', 'Teresa', 'Francisco', 'Gloria', 'Ramon', 'Elena', 'Roberto', 'Patricia'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Mendoza', 'Torres', 'Gonzales', 'Ramos', 'Lopez', 'Aquino', 'Cruz', 'Bautista', 'Castro', 'Flores', 'Morales', 'Gutierrez', 'Navarro', 'Domingo', 'Silva', 'Padilla'];

        // Create company users (clients and suppliers)
        $companyCount = 50;
        for ($i = 1; $i <= $companyCount; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $userType = $i <= 25 ? 'client' : 'supplier';
            $role = $userType;
            $status = 'active';
            $email = strtolower($firstName) . '.' . strtolower(str_replace(' ', '', $lastName)) . '.company' . $i . '@example.com';
            $username = strtolower($firstName) . '_' . strtolower(str_replace(' ', '', $lastName)) . '_company_' . $i;
            User::updateOrCreate([
                'username' => $username,
            ], [
                'name' => $firstName . ' ' . $lastName,
                'username' => $username,
                'email' => $email,
                'user_type' => $userType,
                'role' => $role,
                'status' => $status,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Users seeded successfully!');
    }
} 