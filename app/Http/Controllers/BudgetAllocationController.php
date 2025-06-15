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
        
        // Initialize variables
        $selectedContract = null;
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
        
        // Get selected contract or default to the latest one
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

            // Calculate total spent
            $totalSpent = $selectedContract->transactions->sum('amount');

            // Get filter parameters
            $type = $request->input('type', 'all');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $amountFrom = $request->input('amount_from');
            $amountTo = $request->input('amount_to');

            // Base query for POs
            $poQuery = $selectedContract->purchaseOrders()
                ->with('supplier')
                ->where('status', 'approved');

            // Base query for transactions
            $transactionQuery = $selectedContract->transactions();

            // Apply date filters if provided
            if ($dateFrom) {
                $poQuery->where('created_at', '>=', $dateFrom);
                $transactionQuery->where('date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $poQuery->where('created_at', '<=', $dateTo);
                $transactionQuery->where('date', '<=', $dateTo);
            }

            // Get recent POs with optimized query
            if ($type === 'all' || $type === 'purchase_order') {
                $recentPOs = $poQuery->latest('created_at')
                    ->take(10)
                    ->get()
                    ->map(function($po) {
                        return (object)[
                            'id' => $po->id,
                            'date' => $po->created_at,
                            'description' => "PO #{$po->po_number} - " . ($po->supplier->company_name ?? 'Unknown Supplier'),
                            'amount' => (float)$po->total_amount,
                            'type' => 'purchase_order',
                            'status' => $po->status,
                            'payment_status' => $po->payment_status ?? 'pending',
                            'notes' => $po->notes,
                            'supplier' => $po->supplier,
                            'items' => $po->items
                        ];
                    });
            } else {
                $recentPOs = collect();
            }

            // Get recent regular transactions with optimized query
            if ($type === 'all' || $type === 'transaction') {
                $recentRegularTransactions = $transactionQuery->latest('date')
                    ->take(10)
                    ->get()
                    ->map(function($transaction) {
                        return (object)[
                            'id' => $transaction->id,
                            'date' => $transaction->date,
                            'description' => $transaction->description,
                            'amount' => (float)$transaction->amount,
                            'type' => 'transaction',
                            'status' => $transaction->status ?? 'completed',
                            'payment_status' => $transaction->payment_status ?? 'completed',
                            'notes' => $transaction->notes,
                            'category' => $transaction->category
                        ];
                    });
            } else {
                $recentRegularTransactions = collect();
            }

            // Apply amount filters if provided
            if ($amountFrom) {
                $recentPOs = $recentPOs->filter(function($po) use ($amountFrom) {
                    return $po->amount >= $amountFrom;
                });
                $recentRegularTransactions = $recentRegularTransactions->filter(function($transaction) use ($amountFrom) {
                    return $transaction->amount >= $amountFrom;
                });
            }
            if ($amountTo) {
                $recentPOs = $recentPOs->filter(function($po) use ($amountTo) {
                    return $po->amount <= $amountTo;
                });
                $recentRegularTransactions = $recentRegularTransactions->filter(function($transaction) use ($amountTo) {
                    return $transaction->amount <= $amountTo;
                });
            }

            // Combine and sort all transactions
            $recentTransactions = $recentPOs->concat($recentRegularTransactions)
                ->sortByDesc('date')
                ->take(10)
                ->values();

            // Prepare data for charts
            $monthlyData = $this->prepareMonthlySpendingData($selectedContract);
            $weeklyData = $this->prepareWeeklySpendingData($selectedContract);
            $categoryData = $this->prepareCategoryBreakdown($selectedContract);
            $supplierData = $this->prepareSupplierBreakdown($selectedContract);
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

        return view('admin.budget-allocation', compact(
            'contracts',
            'selectedContract',
            'totalSpent',
            'monthlyData',
            'weeklyData',
            'categoryData',
            'supplierData',
            'recentTransactions'
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

    private function prepareSupplierData($contract)
    {
        $data = new stdClass();
        
        // Group items by supplier and calculate totals
        $supplierTotals = $contract->items()
            ->whereNotNull('supplier_id')
            ->select('supplier_id', 'supplier_name', DB::raw('SUM(total) as total_amount'))
            ->groupBy('supplier_id', 'supplier_name')
            ->get();

        $data->labels = $supplierTotals->pluck('supplier_name')->toArray();
        $data->values = $supplierTotals->pluck('total_amount')->toArray();

        return $data;
    }

    private function prepareMonthlyData($contract)
    {
        $data = new stdClass();
        
        // Get transactions grouped by month
        $monthlyTransactions = $contract->transactions()
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data->labels = $monthlyTransactions->pluck('month')->map(function($month) {
            return Carbon::createFromFormat('Y-m', $month)->format('M Y');
        })->toArray();
        
        $data->values = $monthlyTransactions->pluck('total')->toArray();

        return $data;
    }

    private function prepareWeeklyData($contract)
    {
        $data = new stdClass();
        
        // Get transactions grouped by week
        $weeklyTransactions = $contract->transactions()
            ->select(DB::raw('YEARWEEK(date) as week'), DB::raw('SUM(amount) as total'))
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $data->labels = $weeklyTransactions->pluck('week')->map(function($week) {
            $year = substr($week, 0, 4);
            $weekNum = substr($week, 4);
            return "Week $weekNum, $year";
        })->toArray();
        
        $data->values = $weeklyTransactions->pluck('total')->toArray();

        return $data;
    }

    private function prepareCategoryData($contract)
    {
        $data = new stdClass();
        
        // Group items by material category and calculate totals
        $categoryTotals = $contract->items()
            ->join('materials', 'contract_items.material_id', '=', 'materials.id')
            ->join('categories', 'materials.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(contract_items.total) as total_amount'))
            ->groupBy('categories.name')
            ->get();

        $data->labels = $categoryTotals->pluck('name')->toArray();
        $data->values = $categoryTotals->pluck('total_amount')->toArray();

        return $data;
    }
} 