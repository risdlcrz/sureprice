<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Explicit import

return new class extends Migration
{
    public function up()
    {
        // Drop the index if it exists to avoid errors
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique('users_username_unique');
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
        });

        // Add missing columns if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->after('email');
            }
            
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('company')->after('password');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }
        });

        // Add unique index back after ensuring no duplicates
        Schema::table('users', function (Blueprint $table) {
            // Remove any duplicate usernames first
            DB::statement('UPDATE users u1, users u2 
                          SET u1.username = CONCAT(u1.username, "_", u1.id)
                          WHERE u1.id > u2.id 
                          AND u1.username = u2.username');
            
            // Now add the unique index
            $table->unique('username', 'users_username_unique');
        });

        // Convert user_type to enum safely
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'employee', 'company') NOT NULL DEFAULT 'company'");
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'user_type', 'last_login_at']);
        });
    }
};