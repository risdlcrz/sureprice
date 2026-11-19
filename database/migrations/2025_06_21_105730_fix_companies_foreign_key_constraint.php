<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check if there are any orphaned records in companies table
        // where user_id doesn't exist in users table
        $orphanedCompanies = DB::table('companies')
            ->leftJoin('users', 'companies.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->select('companies.id', 'companies.user_id')
            ->get();

        if ($orphanedCompanies->count() > 0) {
            // Log the orphaned records for debugging
            \Illuminate\Support\Facades\Log::warning('Found orphaned companies with invalid user_id:', $orphanedCompanies->toArray());
            
            // Delete orphaned companies
            DB::table('companies')
                ->leftJoin('users', 'companies.user_id', '=', 'users.id')
                ->whereNull('users.id')
                ->delete();
        }

        // Now let's check if the foreign key constraint already exists
        // Driver-agnostic check (the old information_schema query only worked on MySQL)
        $foreignKeys = collect(Schema::getForeignKeys('companies'))
            ->filter(fn ($fk) => $fk['columns'] === ['user_id'] && $fk['foreign_table'] === 'users')->all();
        if (empty($foreignKeys)) {
            // Add the foreign key constraint if it doesn't exist
            Schema::table('companies', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the foreign key constraint
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
