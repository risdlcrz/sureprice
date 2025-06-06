<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetrics;
use App\Services\SupplierRankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierRankingController extends Controller
{
    protected $rankingService;

    public function __construct(SupplierRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index()
    {
        $suppliers = Supplier::with(['evaluations', 'metrics'])->get();
        $rankings = $this->rankingService->calculateRankings($suppliers);
        
        return view('admin.suppliers.rankings', compact('rankings'));
    }

    public function downloadTemplate()
    {
        return $this->rankingService->generateTemplate();
    }

    public function downloadMaterialsTemplate()
    {
        return $this->rankingService->generateMaterialsTemplate();
    }

    public function storeEvaluation(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'delivery_speed_score' => 'required|numeric|min:0|max:5',
            'quality_score' => 'required|numeric|min:0|max:5',
            'cost_variance_score' => 'required|numeric|min:0|max:5',
            'engagement_score' => 'required|numeric|min:0|max:5',
            'performance_score' => 'required|numeric|min:0|max:5',
            'sustainability_score' => 'required|numeric|min:0|max:5',
        ]);

        $supplier->evaluations()->create($validated);

        return response()->json(['message' => 'Evaluation stored successfully']);
    }

    public function updateMetrics(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'ontime_deliveries' => 'required|integer|min:0',
            'total_deliveries' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
            'total_units' => 'required|integer|min:0',
            'actual_cost' => 'required|numeric|min:0',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        $supplier->metrics()->updateOrCreate(
            ['supplier_id' => $supplier->id],
            $validated
        );

        return response()->json(['message' => 'Metrics updated successfully']);
    }

    public function getLatestEvaluation(Supplier $supplier)
    {
        $evaluation = $supplier->evaluations()->latest()->first();
        $metrics = $supplier->metrics;

        if (!$evaluation && !$metrics) {
            return response()->json([
                'evaluation' => [
                    'delivery_speed_score' => 0,
                    'quality_score' => 0,
                    'cost_variance_score' => 0,
                    'engagement_score' => 0,
                    'performance_score' => 0,
                    'sustainability_score' => 0,
                ],
                'metrics' => [
                    'total_deliveries' => 0,
                    'ontime_deliveries' => 0,
                    'total_units' => 0,
                    'defective_units' => 0,
                    'estimated_cost' => 0,
                    'actual_cost' => 0,
                ]
            ]);
        }

        return response()->json([
            'evaluation' => $evaluation,
            'metrics' => $metrics
        ]);
    }
} 