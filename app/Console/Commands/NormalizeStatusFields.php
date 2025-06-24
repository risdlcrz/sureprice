<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeStatusFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'normalize:status-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize status fields in companies, contracts, and purchase_requests tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = [
            'companies',
            'contracts',
            'purchase_requests',
        ];
        $allowed = ['approved', 'pending', 'rejected'];
        foreach ($tables as $table) {
            $rows = DB::table($table)->select('id', 'status')->get();
            $invalid = [];
            foreach ($rows as $row) {
                $normalized = strtolower(trim($row->status));
                if (!in_array($normalized, $allowed)) {
                    $invalid[] = [
                        'id' => $row->id,
                        'old' => $row->status,
                        'normalized' => $normalized,
                    ];
                }
                DB::table($table)->where('id', $row->id)->update(['status' => $normalized]);
            }
            if (count($invalid)) {
                $this->warn("Invalid status values found in $table:");
                foreach ($invalid as $inv) {
                    $this->line("ID: {$inv['id']}, Old: '{$inv['old']}', Normalized: '{$inv['normalized']}'");
                }
            } else {
                $this->info("All status values in $table normalized successfully.");
            }
        }
        $this->info('Status normalization complete.');
    }
}
