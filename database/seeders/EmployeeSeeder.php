<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // More realistic Filipino data (DEMO/TEST DATA)
        $employeeCount = 60;
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Luz', 'Carlos', 'Rosa', 'Antonio', 'Carmen', 'Miguel', 'Isabel', 'Manuel', 'Teresa', 'Francisco', 'Gloria', 'Ramon', 'Elena', 'Roberto', 'Patricia', 'Andres', 'Emilio', 'Estrella', 'Julio', 'Ligaya', 'Mariano', 'Nicanor', 'Pilar', 'Salvador', 'Victoria'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Mendoza', 'Torres', 'Gonzales', 'Ramos', 'Lopez', 'Aquino', 'Cruz', 'Bautista', 'Castro', 'Flores', 'Morales', 'Gutierrez', 'Navarro', 'Domingo', 'Silva', 'Padilla', 'Soriano', 'Villanueva', 'Aguilar', 'Salazar', 'Rosales', 'Valdez', 'Santiago', 'Pascual', 'Rivera', 'Ocampo'];
        $roles = ['procurement', 'warehousing', 'contractor'];
        $cities = ['Quezon City', 'Manila', 'Makati', 'Pasig', 'Taguig', 'Cebu City', 'Davao City', 'Baguio', 'Iloilo City', 'Cagayan de Oro'];
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Benguet', 'Iloilo', 'Misamis Oriental'];
        for ($i = 0; $i < $employeeCount; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $role = $roles[array_rand($roles)];
            $username = strtolower(Str::slug($firstName . ' ' . $lastName, '')) . $i;
            $email = $username . '@sureprice.com';
            // Create the User record
            $user = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'user_type' => 'employee',
                    'role' => $role,
                    'password' => Hash::make('PASS_123'), // DEMO/TEST PASSWORD
                    'email_verified_at' => now(),
                ]
            );
            // Create the corresponding Employee record
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company_name' => 'SurePrice Construction Corp.',
                    'street' => rand(1, 999) . ' Rizal Street',
                    'barangay' => 'Barangay ' . rand(1, 100),
                    'city' => $cities[array_rand($cities)],
                    'state' => $provinces[array_rand($provinces)],
                    'postal' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'phone' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                ]
            );
        }

        // Add finance employees
        $financeEmployees = [
            [
                'first_name' => 'Finance',
                'last_name' => 'Manager',
                'username' => 'financemanager',
                'email' => 'financemanager@sureprice.com',
            ],
            [
                'first_name' => 'Finance',
                'last_name' => 'Officer',
                'username' => 'financeofficer',
                'email' => 'financeofficer@sureprice.com',
            ],
            [
                'first_name' => 'Finance',
                'last_name' => 'Analyst',
                'username' => 'financeanalyst',
                'email' => 'financeanalyst@sureprice.com',
            ],
        ];
        foreach ($financeEmployees as $fin) {
            $user = User::updateOrCreate(
                ['username' => $fin['username']],
                [
                    'name' => $fin['first_name'] . ' ' . $fin['last_name'],
                    'email' => $fin['email'],
                    'user_type' => 'employee',
                    'role' => 'finance',
                    'password' => Hash::make('PASS_123'),
                    'email_verified_at' => now(),
                ]
            );
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'first_name' => $fin['first_name'],
                    'last_name' => $fin['last_name'],
                    'email' => $user->email,
                    'role' => 'finance',
                    'company_name' => 'SurePrice Construction Corp.',
                    'street' => 'Finance St.',
                    'barangay' => 'Barangay 1',
                    'city' => 'Makati',
                    'state' => 'Metro Manila',
                    'postal' => '1200',
                    'phone' => '+639171234567',
                ]
            );
        }

        $this->command->info('60 Employees seeded successfully!');
    }
} 