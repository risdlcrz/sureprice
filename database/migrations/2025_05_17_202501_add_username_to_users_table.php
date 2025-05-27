<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username column if it doesn't exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')
                      ->unique()
                      ->after('email')
                      ->nullable(); // Temporary, we'll update existing records
            }
        });

        // Update existing records with temporary usernames
        \App\Models\User::whereNull('username')
            ->each(function ($user) {
                $user->update([
                    'username' => 'user_' . $user->id
                ]);
            });

        // Change column to non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')
                  ->nullable(false)
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};