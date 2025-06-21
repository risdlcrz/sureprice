<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
   public function run()
{
    // Remove the factory call completely
    // User::factory()->create([...]);

    // Add your admin seeder
    $this->call([
        AdminSeeder::class,
        MaterialSeeder::class,
        ScopeTypeSeeder::class,
        ScopeTypeMaterialSeeder::class,
        SyncEmployeesToUsersSeeder::class,
    ]);
}
}