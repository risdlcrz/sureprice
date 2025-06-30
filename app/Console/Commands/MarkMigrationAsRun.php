<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkMigrationAsRun extends Command
{
    protected $signature = 'migration:mark-as-run {migration}';
    protected $description = 'Mark a migration as completed without running it';

    public function handle()
    {
        $migration = $this->argument('migration');
        
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => (DB::table('migrations')->max('batch') ?? 0) + 1
        ]);

        $this->info("Migration {$migration} marked as completed");
    }
} 