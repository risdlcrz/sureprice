<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Transaction;
use Carbon\Carbon;
use stdClass;

class BudgetAllocationController extends Controller
{
    public function index()
    {
        // Get recent transactions
        $recentTransactions = Transaction::orderBy('date', 'desc')
            ->take(5)
            ->get();

        // Calculate total budget and spent
        $totalBudget = Contract::sum('total_amount');
        $totalSpent = Transaction::sum('amount');

        // Get spending data for the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $spendingChartData = new stdClass();
        $spendingChartData->labels = [];
        $spendingChartData->values = [];

        // Get daily spending for the current month
        $dailySpending = Transaction::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare data for spending chart
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $spendingChartData->labels[] = $currentDate->format('M d');
            
            $daySpending = $dailySpending->firstWhere('date', $currentDate->format('Y-m-d'));
            $spendingChartData->values[] = $daySpending ? $daySpending->total : 0;
            
            $currentDate->addDay();
        }

        // Prepare cost breakdown data
        $costBreakdownData = new stdClass();
        $costBreakdownData->labels = ['Office Supplies', 'Transportation', 'Utilities', 'Miscellaneous'];
        
        $costBreakdown = Transaction::selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category')
            ->toArray();

        $costBreakdownData->values = array_map(function($category) use ($costBreakdown) {
            return $costBreakdown[$category] ?? 0;
        }, $costBreakdownData->labels);

        return view('admin.budget-allocation', compact(
            'recentTransactions',
            'totalBudget',
            'totalSpent',
            'spendingChartData',
            'costBreakdownData'
        ));
    }
} 