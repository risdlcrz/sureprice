<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // Core Data (No dependencies)
            AdminSeeder::class,
            UserSeeder::class,          // Creates client/supplier users
            EmployeeSeeder::class,      // Creates employee users & profiles
            WarehouseSeeder::class,
            MaterialSeeder::class,
            ScopeTypeSeeder::class,     // Creates scope types and some materials

            // Company & Supplier Setup
            CompanySeeder::class,         // Depends on UserSeeder
            SupplierSeeder::class,        // Depends on CompanySeeder
            MaterialSupplierSeeder::class,// Depends on MaterialSeeder & SupplierSeeder

            // Inventory & Stock
            InventorySeeder::class,       // Depends on MaterialSeeder & WarehouseSeeder
            WarehouseStockSeeder::class,  // Depends on MaterialSeeder & WarehouseSeeder

            // Scope & Material Linking
            ScopeTypeMaterialSeeder::class, // Depends on ScopeTypeSeeder & MaterialSeeder

            // Projects, Contracts, and Parties
            ProjectSeeder::class,
            PartySeeder::class,
            ContractSeeder::class,        // Depends on ProjectSeeder & PartySeeder

            // Procurement Cycle
            ProcurementSeeder::class,     // Depends on Contracts, Employees, Suppliers, Materials
        ]);
    }
}