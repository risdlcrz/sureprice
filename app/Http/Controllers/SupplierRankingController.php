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
        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'required|numeric|min:0.5|max:5|multiple_of:0.5',
            'comments' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $evaluation = $supplier->evaluations()->create([
                'evaluator_id' => auth()->id(),
                'evaluation_date' => now(),
                'ratings' => $request->ratings,
                'comments' => $request->comments,
                'average_rating' => collect($request->ratings)->avg()
            ]);

            // Update supplier's average rating
            $supplier->update([
                'average_rating' => $supplier->evaluations()->avg('average_rating')
            ]);

            DB::commit();

            return back()->with('success', 'Evaluation submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing supplier evaluation: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit evaluation. Please try again.');
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