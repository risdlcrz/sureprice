<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Transaction;
use App\Models\ContractItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use stdClass;
use Illuminate\Support\Facades\DB;

class BudgetAllocationController extends Controller
{
    public function index(Request $request)
    {
        // Get all contracts for dropdown
        $contracts = Contract::with(['client'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get selected contract or default to the latest one
        $selectedContract = null;
        $contractId = $request->input('contract_id');
        
        if ($contractId) {
            $selectedContract = Contract::with([
                'client',
                'items.material.category',
                'items.supplier',
                'transactions' => function($query) {
                    $query->orderBy('date', 'desc');
                },
                'purchaseOrders' => function($query) {
                    $query->where('status', 'approved')
                         ->orderBy('created_at', 'desc');
                },
                'purchaseOrders.supplier',
                'purchaseOrders.items.material.category'
            ])->findOrFail($contractId);
        } elseif ($contracts->isNotEmpty()) {
            $selectedContract = Contract::with([
                'client',
                'items.material.category',
                'items.supplier',
                'transactions' => function($query) {
                    $query->orderBy('date', 'desc');
                },
                'purchaseOrders' => function($query) {
                    $query->where('status', 'approved')
                         ->orderBy('created_at', 'desc');
                },
                'purchaseOrders.supplier',
                'purchaseOrders.items.material.category'
            ])->findOrFail($contracts->first()->id);
        }

        if ($selectedContract) {
            // Calculate budget metrics
            $totalBudget = (float)$selectedContract->total_amount;
            
            // Calculate total spent from POs and transactions
            $approvedPOTotal = $selectedContract->purchaseOrders
                ->sum(function($po) {
                    return (float)$po->total_amount;
                });
            
            $transactionsTotal = $selectedContract->transactions
                ->sum(function($transaction) {
                    return (float)$transaction->amount;
                });
            
            $totalSpent = $approvedPOTotal + $transactionsTotal;
            
            // Recent transactions (combining POs and regular transactions)
            $recentTransactions = collect();
            
            // Add POs as transactions
            foreach ($selectedContract->purchaseOrders->take(5) as $po) {
                $recentTransactions->push((object)[
                    'date' => $po->created_at,
                    'description' => "PO #{$po->po_number} - " . ($po->supplier->name ?? 'Unknown Supplier'),
                    'amount' => (float)$po->total_amount,
                    'type' => 'purchase_order'
                ]);
            }
            
            // Add regular transactions
            foreach ($selectedContract->transactions->take(5) as $transaction) {
                $recentTransactions->push((object)[
                    'date' => $transaction->date,
                    'description' => $transaction->description,
                    'amount' => (float)$transaction->amount,
                    'type' => 'transaction'
                ]);
            }
            
            // Sort and limit transactions
            $recentTransactions = $recentTransactions
                ->sortByDesc('date')
                ->take(5)
                ->values();

            // Prepare spending chart data (both monthly and weekly)
            $monthlyData = $this->prepareMonthlySpendingData($selectedContract);
            $weeklyData = $this->prepareWeeklySpendingData($selectedContract);
            
            // Prepare cost breakdown data (both by category and supplier)
            $categoryData = $this->prepareCategoryBreakdown($selectedContract);
            $supplierData = $this->prepareSupplierBreakdown($selectedContract);

        } else {
            $totalBudget = 0;
            $totalSpent = 0;
            $recentTransactions = collect();
            $monthlyData = new stdClass();
            $weeklyData = new stdClass();
            $categoryData = new stdClass();
            $supplierData = new stdClass();
            
            // Initialize empty data structures
            foreach (['monthlyData', 'weeklyData', 'categoryData', 'supplierData'] as $var) {
                ${$var}->labels = [];
                ${$var}->values = [];
            }
        }

        return view('admin.budget-allocation', compact(
            'contracts',
            'selectedContract',
            'recentTransactions',
            'totalBudget',
            'totalSpent',
            'monthlyData',
            'weeklyData',
            'categoryData',
            'supplierData'
        ));
    }

    private function prepareMonthlySpendingData($contract)
    {
        $data = new stdClass();
        $data->labels = [];
        $data->values = [];

        if (!$contract) return $data;

        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $monthlySpending = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $monthlySpending[$monthKey] = 0;
            $data->labels[] = $currentDate->format('M Y');
            $currentDate->addMonth();
        }

        // Add PO amounts
        foreach ($contract->purchaseOrders as $po) {
            $monthKey = Carbon::parse($po->created_at)->format('Y-m');
            if (isset($monthlySpending[$monthKey])) {
                $monthlySpending[$monthKey] += (float)$po->total_amount;
            }
        }

        // Add transaction amounts
        foreach ($contract->transactions as $transaction) {
            $monthKey = Carbon::parse($transaction->date)->format('Y-m');
            if (isset($monthlySpending[$monthKey])) {
                $monthlySpending[$monthKey] += (float)$transaction->amount;
            }
        }

        $data->values = array_values($monthlySpending);
        return $data;
    }

    private function prepareWeeklySpendingData($contract)
    {
        $data = new stdClass();
        $data->labels = [];
        $data->values = [];

        if (!$contract) return $data;

        $startDate = Carbon::now()->subWeeks(11)->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        
        $weeklySpending = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $weekKey = $currentDate->format('Y-W');
            $weeklySpending[$weekKey] = 0;
            $data->labels[] = 'Week ' . $currentDate->format('W');
            $currentDate->addWeek();
        }

        // Add PO amounts
        foreach ($contract->purchaseOrders as $po) {
            $weekKey = Carbon::parse($po->created_at)->format('Y-W');
            if (isset($weeklySpending[$weekKey])) {
                $weeklySpending[$weekKey] += (float)$po->total_amount;
            }
        }

        // Add transaction amounts
        foreach ($contract->transactions as $transaction) {
            $weekKey = Carbon::parse($transaction->date)->format('Y-W');
            if (isset($weeklySpending[$weekKey])) {
                $weeklySpending[$weekKey] += (float)$transaction->amount;
            }
        }

        $data->values = array_values($weeklySpending);
        return $data;
    }

    private function prepareCategoryBreakdown($contract)
    {
        $data = new stdClass();
        $data->labels = [];
        $data->values = [];

        if (!$contract) return $data;

        $categoryTotals = [];

        // Calculate totals from PO items
        foreach ($contract->purchaseOrders as $po) {
            foreach ($po->items as $item) {
                $category = optional($item->material->category)->name ?? 'Uncategorized';
                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = 0;
                }
                $categoryTotals[$category] += (float)$item->total;
            }
        }

        // Calculate totals from contract items
        foreach ($contract->items as $item) {
            $category = optional($item->material->category)->name ?? 'Uncategorized';
            if (!isset($categoryTotals[$category])) {
                $categoryTotals[$category] = 0;
            }
            $categoryTotals[$category] += (float)$item->total;
        }

        // Remove empty categories and sort by amount
        $categoryTotals = array_filter($categoryTotals);
        arsort($categoryTotals);

        // Take top 5 categories and group others
        $topCategories = array_slice($categoryTotals, 0, 5, true);
        $otherTotal = array_sum(array_slice($categoryTotals, 5));
        
        if ($otherTotal > 0) {
            $topCategories['Others'] = $otherTotal;
        }

        $data->labels = array_keys($topCategories);
        $data->values = array_values($topCategories);

        return $data;
    }

    private function prepareSupplierBreakdown($contract)
    {
        $data = new stdClass();
        $data->labels = [];
        $data->values = [];

        if (!$contract) return $data;

        $supplierTotals = [];

        // Calculate totals from POs
        foreach ($contract->purchaseOrders as $po) {
            $supplierName = optional($po->supplier)->company_name ?? $po->supplier_name ?? 'Unknown Supplier';
            if (!isset($supplierTotals[$supplierName])) {
                $supplierTotals[$supplierName] = 0;
            }
            $supplierTotals[$supplierName] += (float)$po->total_amount;
        }

        // Calculate totals from contract items
        foreach ($contract->items as $item) {
            $supplierName = optional($item->supplier)->company_name ?? $item->supplier_name ?? 'Unknown Supplier';
            if (!isset($supplierTotals[$supplierName])) {
                $supplierTotals[$supplierName] = 0;
            }
            $supplierTotals[$supplierName] += (float)$item->total;
        }

        // Remove empty suppliers and sort by amount
        $supplierTotals = array_filter($supplierTotals);
        arsort($supplierTotals);

        // Take top 5 suppliers and group others
        $topSuppliers = array_slice($supplierTotals, 0, 5, true);
        $otherTotal = array_sum(array_slice($supplierTotals, 5));
        
        if ($otherTotal > 0) {
            $topSuppliers['Others'] = $otherTotal;
        }

        $data->labels = array_keys($topSuppliers);
        $data->values = array_values($topSuppliers);

        return $data;
    }
} 