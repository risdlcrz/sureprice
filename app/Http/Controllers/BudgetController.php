<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\BudgetTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetTrackingService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index()
    {
        try {
            $contracts = Contract::with(['client', 'items.material'])
                ->where('status', 'active')
                ->get();

            $budgetData = [];
            foreach ($contracts as $contract) {
                $budget = $this->budgetService->calculateContractBudget($contract);
                $alert = $this->budgetService->checkBudgetAlert($contract);

                $budgetData[] = [
                    'contract' => $contract,
                    'budget' => $budget,
                    'alert' => $alert
                ];
            }

            return view('admin.budgets.index', compact('budgetData'));
        } catch (\Exception $e) {
            Log::error('Error in budget index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading budget data');
        }
    }

    public function show(Contract $contract)
    {
        try {
            $report = $this->budgetService->generateBudgetReport($contract);
            $alert = $this->budgetService->checkBudgetAlert($contract);

            return view('admin.budgets.show', compact('contract', 'report', 'alert'));
        } catch (\Exception $e) {
            Log::error('Error in budget show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating budget report');
        }
    }

    public function exportReport(Contract $contract)
    {
        try {
            $report = $this->budgetService->generateBudgetReport($contract);
            
            // Generate PDF using a PDF library (e.g., DomPDF)
            $pdf = \PDF::loadView('admin.budgets.report-pdf', compact('report'));
            
            return $pdf->download('budget-report-' . $contract->contract_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting budget report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting budget report');
        }
    }

    public function getBudgetAlerts()
    {
        try {
            $contracts = Contract::where('status', 'active')->get();
            $alerts = [];

            foreach ($contracts as $contract) {
                $alert = $this->budgetService->checkBudgetAlert($contract);
                if ($alert) {
                    $alerts[] = [
                        'contract' => $contract->contract_number,
                        'client' => $contract->client->name,
                        'alert' => $alert
                    ];
                }
            }

            return response()->json($alerts);
        } catch (\Exception $e) {
            Log::error('Error getting budget alerts: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving budget alerts'], 500);
        }
    }
} 