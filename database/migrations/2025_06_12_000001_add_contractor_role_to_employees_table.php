<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if we're using SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            Schema::table('employees', function (Blueprint $table) {
                $table->string('role')->default('procurement')->change();
            });
        } else {
            // For MySQL, use the MODIFY approach
            DB::statement("ALTER TABLE employees MODIFY role ENUM('procurement','warehousing','contractor') NOT NULL");
        }
    }

    public function down()
    {
        // Check if we're using SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('role')->default('procurement')->change();
            });
        } else {
            // For MySQL, revert to original
            DB::statement("ALTER TABLE employees MODIFY role ENUM('procurement','warehousing') NOT NULL");
        }
    }
}; 