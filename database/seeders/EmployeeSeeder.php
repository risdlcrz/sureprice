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
        // Data for generating random employees
        $firstNames = [
            'Maria', 'Jose', 'Antonio', 'Francisco', 'Manuel', 'Pedro', 'Juan', 'Carlos', 'Josefina', 'Ana',
            'Ramon', 'Miguel', 'Luis', 'Alberto', 'Roberto', 'Fernando', 'Ricardo', 'Eduardo', 'Alfredo', 'Jorge'
        ];
        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Andres', 'Dela Cruz'
        ];
        $cities = [
            'Manila', 'Quezon City', 'Caloocan', 'Las Piñas', 'Makati', 'Mandaluyong', 'Marikina', 'Taguig'
        ];
        $provinces = ['Metro Manila'];

        $employeeCount = 50;
        $roles = ['procurement', 'warehousing', 'contractor'];

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
                    'password' => Hash::make('password123'),
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

        $this->command->info('50 Employees seeded successfully!');
    }
} 