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
        $rankings = $this->getSupplierRankings();
        $topSuppliers = $rankings->take(3);
        return view('admin.analytics-dashboard', compact('topSuppliers'));
    }

    /**
     * Get supplier rankings, caching only lightweight data (ids, scores, ranks)
     * to avoid exceeding MySQL max_allowed_packet when using database cache.
     *
     * @return \Illuminate\Support\Collection<int, array{supplier: \App\Models\Supplier, score: float, rank: int|null}>
     */
    protected function getSupplierRankings()
    {
        $lightKey = 'supplier.rankings.light';

        $cached = Cache::get($lightKey);
        if ($cached !== null) {
            $ids = collect($cached)->pluck('supplier_id')->all();
            $suppliers = Supplier::with(['evaluations', 'metrics'])->whereIn('id', $ids)->get()->keyBy('id');
            return collect($cached)->map(function ($row) use ($suppliers) {
                $supplier = $suppliers->get($row['supplier_id']);
                return [
                    'supplier' => $supplier,
                    'score' => $row['score'],
                    'rank' => $row['rank'],
                ];
            })->filter(fn ($row) => $row['supplier'] !== null)->values();
        }

        // Remove legacy cache entry that stored full Eloquent models (exceeded max_allowed_packet)
        Cache::forget('supplier.rankings');

        $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
        $rankings = $this->rankingService->calculateRankings($suppliers);

        $light = $rankings->map(fn ($r) => [
            'supplier_id' => $r['supplier']->id,
            'score' => $r['score'],
            'rank' => $r['rank'],
        ])->all();

        Cache::put($lightKey, $light, now()->addMinutes(30));

        return $rankings;
    }

    public function supplierRankings()
    {
        // Cache only lightweight data (supplier_id, score, rank) to avoid exceeding MySQL max_allowed_packet
        $rankings = $this->getSupplierRankings();

        return view('admin.suppliers.rankings', compact('rankings'));
    }

    public function getTopSuppliers(): JsonResponse
    {
        $rankings = $this->getSupplierRankings();

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