<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierRankingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
} 