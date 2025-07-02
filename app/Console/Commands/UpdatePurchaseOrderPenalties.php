<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use Carbon\Carbon;

class UpdatePurchaseOrderPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase-orders:update-penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update penalties for all overdue, unpaid purchase orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $overdueOrders = PurchaseOrder::whereNotNull('due_date')
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', $now)
            ->get();

        $this->info('Found ' . $overdueOrders->count() . ' overdue purchase orders.');
        $updated = 0;
        foreach ($overdueOrders as $order) {
            $oldPenalty = $order->penalty_accrued;
            $newPenalty = $order->calculateAndUpdatePenalty();
            if ($newPenalty != $oldPenalty) {
                $updated++;
                $this->line("PO #{$order->po_number}: Penalty updated from ₱" . number_format($oldPenalty, 2) . " to ₱" . number_format($newPenalty, 2));
            }
        }
        $this->info("Updated penalties for $updated purchase orders.");
        return 0;
    }
}
