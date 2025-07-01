<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users table (most fundamental)
        // Removed creation of 'users' table to avoid duplicate table creation.
        // 2. Categories table
        // Removed creation of 'categories' table to avoid duplicate table creation.
        // 3. Companies table
        // Removed creation of 'companies' table to avoid duplicate table creation.
        // 4. Suppliers table
        // Removed creation of 'suppliers' table to avoid duplicate table creation.
        // 6. Warehouses table
        // Removed creation of 'warehouses' table to avoid duplicate table creation.
        // 7. Contracts table
        // Removed creation of 'contracts' table to avoid duplicate table creation.
    }

    public function down(): void
    {
        // Schema::dropIfExists('contracts'); // Removed
        // Schema::dropIfExists('warehouses'); // Removed
        // Schema::dropIfExists('suppliers'); // Removed
        // Schema::dropIfExists('companies'); // Removed
        // Schema::dropIfExists('categories'); // Removed
        // Schema::dropIfExists('users'); // Removed
    }
}; 