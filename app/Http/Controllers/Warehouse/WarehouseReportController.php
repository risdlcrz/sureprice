<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Delivery;
use App\Models\StockMovement;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class WarehouseReportController extends Controller
{
    public function index()
    {
        $recentReports = Report::where('type', 'like', 'warehouse_%')
            ->with('generated_by')
            ->latest()
            ->take(10)
            ->get();

        return view('warehouse.reports.index', compact('recentReports'));
    }

    public function inventory(Request $request)
    {
        $query = Material::with(['category', 'stockMovements']);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('current_stock', '<', 'minimum_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('current_stock', '>=', 'minimum_stock');
                    break;
            }
        }

        $materials = $query->get();
        return view('warehouse.reports.inventory-web', compact('materials'));
    }

    public function inventoryPdf(Request $request)
    {
        $query = Material::with(['category', 'stockMovements']);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('current_stock', '<', 'minimum_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('current_stock', '>=', 'minimum_stock');
                    break;
            }
        }

        $materials = $query->get();
        $pdf = \PDF::loadView('warehouse.reports.inventory-pdf', compact('materials'));
        // Save report record
        $report = Report::create([
            'type' => 'warehouse_inventory',
            'generated_by_id' => auth()->id(),
            'parameters' => $request->all()
        ]);
        return $pdf->download('inventory-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['material', 'material.category']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->get();
        return view('warehouse.reports.movements-web', compact('movements'));
    }

    public function movementsPdf(Request $request)
    {
        $query = StockMovement::with(['material', 'material.category']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->get();
        $pdf = \PDF::loadView('warehouse.reports.movements-pdf', compact('movements'));
        $report = Report::create([
            'type' => 'warehouse_movements',
            'generated_by_id' => auth()->id(),
            'parameters' => $request->all()
        ]);
        return $pdf->download('stock-movements-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function deliveries(Request $request)
    {
        $query = Delivery::with(['items', 'items.material']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('expected_date', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->get();
        return view('warehouse.reports.deliveries-web', compact('deliveries'));
    }

    public function deliveriesPdf(Request $request)
    {
        $query = Delivery::with(['items', 'items.material']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('expected_date', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->get();
        $pdf = \PDF::loadView('warehouse.reports.deliveries-pdf', compact('deliveries'));
        $report = Report::create([
            'type' => 'warehouse_deliveries',
            'generated_by_id' => auth()->id(),
            'parameters' => $request->all()
        ]);
        return $pdf->download('deliveries-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function usage(Request $request)
    {
        $query = StockMovement::with(['material', 'material.category']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        $movements = $query->get();

        $usageStats = $movements->groupBy('material_id')
            ->map(function ($group) {
                return [
                    'material' => $group->first()->material,
                    'total_out' => $group->where('type', 'out')->sum('quantity'),
                    'total_in' => $group->where('type', 'in')->sum('quantity'),
                    'net_change' => $group->where('type', 'in')->sum('quantity') - $group->where('type', 'out')->sum('quantity')
                ];
            })
            ->sortByDesc('total_out');

        return view('warehouse.reports.usage-web', ['usageStats' => $usageStats]);
    }

    public function usagePdf(Request $request)
    {
        $query = StockMovement::with(['material', 'material.category']);

        // Apply filters
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }

        $movements = $query->get();

        $usageStats = $movements->groupBy('material_id')
            ->map(function ($group) {
                return [
                    'material' => $group->first()->material,
                    'total_out' => $group->where('type', 'out')->sum('quantity'),
                    'total_in' => $group->where('type', 'in')->sum('quantity'),
                    'net_change' => $group->where('type', 'in')->sum('quantity') - $group->where('type', 'out')->sum('quantity')
                ];
            })
            ->sortByDesc('total_out');

        $pdf = \PDF::loadView('warehouse.reports.usage-pdf', ['usageStats' => $usageStats]);
        $report = Report::create([
            'type' => 'warehouse_usage',
            'generated_by_id' => auth()->id(),
            'parameters' => $request->all()
        ]);
        return $pdf->download('material-usage-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function analytics(Request $request)
    {
        // Inventory levels (all materials)
        $materials = \App\Models\Material::with('category')->get();

        // Most used materials per project (by outgoing stock movements)
        $mostUsedByProject = \App\Models\StockMovement::where('type', 'out')
            ->with(['material', 'material.category'])
            ->selectRaw('material_id, reference_number, SUM(quantity) as total_used')
            ->groupBy('material_id', 'reference_number')
            ->orderByDesc('total_used')
            ->get();

        // Monthly usage trends (all materials)
        $monthlyTrends = \App\Models\StockMovement::where('type', 'out')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, material_id, SUM(quantity) as total_used')
            ->groupBy('year', 'month', 'material_id')
            ->orderBy('year')->orderBy('month')
            ->get();

        return view('warehouse.reports.analytics', compact('materials', 'mostUsedByProject', 'monthlyTrends'));
    }

    public function analyticsPdf(Request $request)
    {
        // Use the same data as the analytics page
        $materials = \App\Models\Material::with('category')->get();
        $mostUsedByProject = \App\Models\StockMovement::where('type', 'out')
            ->with(['material', 'material.category'])
            ->selectRaw('material_id, reference_number, SUM(quantity) as total_used')
            ->groupBy('material_id', 'reference_number')
            ->orderByDesc('total_used')
            ->get();
        $monthlyTrends = \App\Models\StockMovement::where('type', 'out')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, material_id, SUM(quantity) as total_used')
            ->groupBy('year', 'month', 'material_id')
            ->orderBy('year')->orderBy('month')
            ->get();

        $pdf = \PDF::loadView('warehouse.reports.analytics-pdf', compact('materials', 'mostUsedByProject', 'monthlyTrends'));
        return $pdf->download('warehouse-analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    public function download(Report $report)
    {
        // Generate the report based on its type and parameters
        switch ($report->type) {
            case 'warehouse_inventory':
                return $this->inventory(new Request($report->parameters));
            case 'warehouse_movements':
                return $this->movements(new Request($report->parameters));
            case 'warehouse_deliveries':
                return $this->deliveries(new Request($report->parameters));
            case 'warehouse_usage':
                return $this->usage(new Request($report->parameters));
            default:
                abort(404);
        }
    }

    public function show($id)
    {
        abort(404, 'Individual report view not available.');
    }
} 