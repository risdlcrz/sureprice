<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierRankingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
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
        $topSuppliers = Cache::remember('supplier.top', now()->addMinutes(30), function () {
            $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
            return $this->rankingService->calculateRankings($suppliers)->take(3);
        });
        return view('admin.analytics-dashboard', compact('topSuppliers'));
    }

    public function supplierRankings()
    {
        // cache rankings for 30 minutes, invalidate on model events
        $rankings = Cache::remember('supplier.rankings', now()->addMinutes(30), function () {
            $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
            return $this->rankingService->calculateRankings($suppliers);
        });

        return view('admin.suppliers.rankings', compact('rankings'));
    }

    public function getTopSuppliers(): JsonResponse
    {
        $rankings = Cache::remember('supplier.rankings', now()->addMinutes(30), function () {
            $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
            return $this->rankingService->calculateRankings($suppliers);
        });

        $topSuppliers = $rankings
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

        $query = \DB::table('material_supplier_price_histories');
        if (!empty($selectedMaterialIds)) {
            $query->whereIn('material_id', $selectedMaterialIds);
        } else {
            // By default, show no data. User must select materials.
            $query->whereRaw('1 = 0');
        }
        $histories = $query->orderBy('date')->get();

        $priceData = collect($histories)->groupBy('material_id')->map(function ($items, $materialId) use ($materials) {
            $material = $materials->firstWhere('id', $materialId);
            return [
                'label' => $material ? $material->name : 'Unknown',
                'data' => collect($items)->map(function ($item) {
                    return [
                        'x' => $item->date,
                        'y' => $item->price
                    ];
                })->sortBy('x')->values()
            ];
        })->values();

        return view('procurement.analytics.price-analysis', [
            'priceData' => $priceData,
            'materials' => $materials,
            'selectedMaterialIds' => $selectedMaterialIds,
        ]);
    }
} 