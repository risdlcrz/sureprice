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
        return view('analytics.dashboard', ['links' => [
            'transactions' => 'admin.transactions', 'budget' => 'admin.budget-allocation', 'rankings' => 'admin.supplier-rankings',
            'prices' => 'admin.price-analysis', 'recommendation' => 'analytics.supplier-recommendation', 'feedback' => 'admin.feedback.analytics',
        ]]);
    }


    public function supplierRankings(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('search', '')) ?: null;

        return view('suppliers.rankings', [
            'rankings' => $this->rankingService->cachedRankings($search),
            'topSuppliers' => $this->rankingService->topSuppliers(),
            'search' => $search,
            'canEvaluate' => $request->user()->isAdmin(),
            'backRoute' => 'admin.analytics',
        ]);
    }

    public function getTopSuppliers(): JsonResponse
    {
        $rankings = $this->rankingService->cachedRankings();

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