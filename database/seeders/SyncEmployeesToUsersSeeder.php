<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SyncEmployeesToUsersSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $user = User::where('email', $employee->email)->first();
            if (!$user) {
                // Create user if not exists
                $user = User::create([
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $employee->email,
                    'username' => $employee->username,
                    'password' => Hash::make(Str::random(12)),
                    'role' => $employee->role,
                    'user_type' => 'employee',
                    'force_password_change' => true,
                ]);
                // Link employee to user
                $employee->user_id = $user->id;
                $employee->save();
            } else {
                // Ensure role and user_type are correct
                $user->role = $employee->role;
                $user->user_type = 'employee';
                $user->save();
                // Link employee to user if not already linked
                if ($employee->user_id !== $user->id) {
                    $employee->user_id = $user->id;
                    $employee->save();
                }
            }
        }
    }
} 