<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsertMigrationRecord extends Seeder
{
    public function run()
    {
        $migration = env('MIGRATION_NAME');
        if (!$migration) {
            $this->command->error('MIGRATION_NAME environment variable is required');
            return;
        }

        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => (DB::table('migrations')->max('batch') ?? 0) + 1
        ]);

        $this->command->info("Migration {$migration} marked as completed");
    }
} 