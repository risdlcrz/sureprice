<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierRankingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;
use App\Models\Contract;
use App\Models\PurchaseOrderItem;
use App\Models\Material;

class AnalyticsController extends Controller
{
    protected $rankingService;

    public function __construct(SupplierRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index()
    {
        $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
        $topSuppliers = $this->rankingService->calculateRankings($suppliers)->take(3);
        return view('admin.analytics-dashboard', compact('topSuppliers'));
    }

    public function supplierRankings()
    {
        $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
        $rankings = $this->rankingService->calculateRankings($suppliers);
        
        return view('admin.suppliers.rankings', compact('rankings'));
    }

    public function getTopSuppliers(): JsonResponse
    {
        $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
        $topSuppliers = $this->rankingService->calculateRankings($suppliers)
            ->take(3)
            ->map(function ($ranking) {
                return [
                    'company_name' => $ranking['supplier']->company_name,
                    'score' => $ranking['score']
                ];
            });
        
        return response()->json($topSuppliers);
    }

    public function transactions()
    {
        $transactions = Transaction::whereHas('payment', function ($query) {
            $query->whereNotNull('purchase_order_id');
        })
        ->with('payment.purchaseOrder.supplier')
        ->latest()
        ->paginate(15);
            
        return view('procurement.analytics.transactions', compact('transactions'));
    }

    public function budgetAllocation()
    {
        $contracts = Contract::with('transactions')->get();

        $chartData = $contracts->map(function ($contract) {
            return [
                'label' => $contract->contract_number,
                'budget' => $contract->total_amount,
                'expenditure' => $contract->transactions->sum('amount'),
            ];
        });

        return view('procurement.analytics.budget-allocation', [
            'chartData' => $chartData
        ]);
    }

    public function priceAnalysis(Request $request)
    {
        $materials = Material::orderBy('name')->get();
        $selectedMaterialIds = $request->input('material_ids', []);

        $priceDataQuery = PurchaseOrderItem::with('material', 'purchaseOrder')
            ->whereHas('purchaseOrder');

        if (!empty($selectedMaterialIds)) {
            $priceDataQuery->whereIn('material_id', $selectedMaterialIds);
        } else {
            // By default, show no data. User must select materials.
            $priceDataQuery->whereRaw('1 = 0');
        }

        $priceData = $priceDataQuery
            ->get()
            ->groupBy('material.name')
            ->map(function ($items, $materialName) {
                return [
                    'label' => $materialName,
                    'data' => $items->map(function ($item) {
                        if ($item->purchaseOrder && $item->purchaseOrder->created_at) {
                            return [
                                'x' => $item->purchaseOrder->created_at->format('Y-m-d'),
                                'y' => $item->unit_price
                            ];
                        }
                        return null;
                    })->filter()->sortBy('x')->values()
                ];
            })
            ->values();

        return view('procurement.analytics.price-analysis', [
            'priceData' => $priceData,
            'materials' => $materials,
            'selectedMaterialIds' => $selectedMaterialIds,
        ]);
    }
} 