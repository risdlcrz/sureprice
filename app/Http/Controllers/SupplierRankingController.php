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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierRankingController extends Controller
{
    protected $rankingService;

    public function __construct(SupplierRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $query = Supplier::with(['evaluations', 'metrics']);

        if ($search !== '') {
            $query->where('company_name', 'like', "%{$search}%");
        }

        $suppliers = $query->get();
        $rankings = $this->rankingService->calculateRankings($suppliers);

        if ($search !== '' && count($rankings) === 0) {
            session()->flash('search_error', "No suppliers found matching '{$search}'.");
        }

        return view('admin.suppliers.rankings', compact('rankings'))->with('search', $search);
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
        $request->validate([
            'delivery_speed_score' => 'required|numeric|min:0|max:5',
            'quality_score' => 'required|numeric|min:0|max:5',
            'cost_variance_score' => 'required|numeric|min:0|max:5',
            'performance_score' => 'required|numeric|min:0|max:5',
            'engagement_score' => 'required|numeric|min:0|max:5',
            'sustainability_score' => 'required|numeric|min:0|max:5',
            'delivery_ontime_ratio' => 'required|numeric|min:0|max:1',
            'defect_ratio' => 'required|numeric|min:0|max:1',
            'cost_variance_ratio' => 'required|numeric|min:0',
            'final_score' => 'required|numeric|min:0|max:5'
        ]);

        try {
            DB::beginTransaction();

            // Delete existing evaluation for this supplier if any (keep only latest)
            $supplier->evaluations()->delete();

            $evaluation = $supplier->evaluations()->create([
                'delivery_speed_score' => $request->delivery_speed_score,
                'delivery_ontime_ratio' => $request->delivery_ontime_ratio,
                'quality_score' => $request->quality_score,
                'defect_ratio' => $request->defect_ratio,
                'cost_variance_score' => $request->cost_variance_score,
                'cost_variance_ratio' => $request->cost_variance_ratio,
                'performance_score' => $request->performance_score,
                'engagement_score' => $request->engagement_score,
                'sustainability_score' => $request->sustainability_score,
                'final_score' => $request->final_score,
                'evaluation_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evaluation submitted successfully.',
                'evaluation' => $evaluation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing supplier evaluation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit evaluation. Please try again.'
            ], 500);
        }
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

    public function getPurchaseOrderMetrics(Supplier $supplier)
    {
        $metrics = $supplier->purchaseOrders()
            ->where('is_completed', true)
            ->selectRaw('
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN is_on_time = 1 THEN 1 ELSE 0 END) as ontime_deliveries,
                SUM(total_units) as total_units,
                SUM(defective_units) as defective_units,
                SUM(estimated_cost) as estimated_cost,
                SUM(actual_cost) as actual_cost
            ')
            ->first();

        if (!$metrics) {
            return response()->json([
                'total_deliveries' => 0,
                'ontime_deliveries' => 0,
                'total_units' => 0,
                'defective_units' => 0,
                'estimated_cost' => 0,
                'actual_cost' => 0
            ]);
        }

        return response()->json($metrics);
    }
} 