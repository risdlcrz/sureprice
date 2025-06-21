<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Philippine first names (common)
        $firstNames = [
            'Maria', 'Jose', 'Antonio', 'Francisco', 'Manuel', 'Pedro', 'Juan', 'Carlos', 'Josefina', 'Ana',
            'Carmen', 'Rosa', 'Teresa', 'Isabel', 'Dolores', 'Concepcion', 'Patricia', 'Gloria', 'Elena', 'Victoria',
            'Ramon', 'Miguel', 'Luis', 'Alberto', 'Roberto', 'Fernando', 'Ricardo', 'Eduardo', 'Alfredo', 'Jorge',
            'Rafael', 'Daniel', 'Mario', 'Santiago', 'Felipe', 'Andres', 'Enrique', 'Joaquin', 'Alejandro', 'Diego',
            'Gabriel', 'Adrian', 'Christian', 'Mark', 'John', 'Michael', 'David', 'James', 'Robert', 'Christopher',
            'Sarah', 'Michelle', 'Jennifer', 'Jessica', 'Amanda', 'Nicole', 'Stephanie', 'Melissa', 'Ashley', 'Rachel',
            'Angela', 'Kimberly', 'Lisa', 'Heather', 'Rebecca', 'Laura', 'Sharon', 'Cynthia', 'Amy', 'Deborah',
            'Pamela', 'Donna', 'Carol', 'Sandra', 'Brenda', 'Diane', 'Janet', 'Shirley', 'Joyce', 'Margaret',
            'Helen', 'Dorothy', 'Betty', 'Ruth', 'Virginia', 'Frances', 'Jean', 'Alice', 'Martha', 'Gloria',
            'Evelyn', 'Irene', 'Florence', 'Lillian', 'Rose', 'Catherine', 'Grace', 'Marie', 'Ann', 'Elizabeth',
            'Barbara', 'Linda', 'Susan', 'Nancy', 'Karen', 'Betty', 'Helen', 'Sandra', 'Donna', 'Carol'
        ];

        // Philippine last names (common)
        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Andres', 'Dela Cruz',
            'Mercado', 'Ramos', 'Aquino', 'Castro', 'Martinez', 'Sanchez', 'Torres', 'Flores', 'Rivera', 'Morales',
            'Gonzales', 'Perez', 'Gomez', 'Diaz', 'Reyes', 'Romero', 'Herrera', 'Espinosa', 'Valdez', 'Molina',
            'Ortiz', 'Silva', 'Cruz', 'Moreno', 'Jimenez', 'Munoz', 'Alvarez', 'Ruiz', 'Herrera', 'Medina',
            'Aguilar', 'Vargas', 'Castillo', 'Cortez', 'Lopez', 'Gutierrez', 'Castro', 'Fernandez', 'Ramirez', 'Reyes',
            'Torres', 'Morales', 'Ortiz', 'Silva', 'Cruz', 'Moreno', 'Jimenez', 'Munoz', 'Alvarez', 'Ruiz',
            'Herrera', 'Medina', 'Aguilar', 'Vargas', 'Castillo', 'Cortez', 'Lopez', 'Gutierrez', 'Castro', 'Fernandez',
            'Ramirez', 'Reyes', 'Torres', 'Morales', 'Ortiz', 'Silva', 'Cruz', 'Moreno', 'Jimenez', 'Munoz',
            'Alvarez', 'Ruiz', 'Herrera', 'Medina', 'Aguilar', 'Vargas', 'Castillo', 'Cortez', 'Lopez', 'Gutierrez',
            'Castro', 'Fernandez', 'Ramirez', 'Reyes', 'Torres', 'Morales', 'Ortiz', 'Silva', 'Cruz', 'Moreno'
        ];

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

        // Create company users (clients and suppliers)
        $companyCount = 50;
        for ($i = 1; $i <= $companyCount; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $userType = $i <= 25 ? 'client' : 'supplier';
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'username' => strtolower($firstName) . '_' . strtolower($lastName) . '_company_' . $i,
                'email' => strtolower($firstName) . '.' . strtolower($lastName) . '.company' . $i . '@example.com',
                'user_type' => $userType,
                'role' => $userType,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Users seeded successfully!');
    }
} 