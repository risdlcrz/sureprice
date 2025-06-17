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
                    $query->whereColumn('stock', '<', 'minimum_stock');
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('stock', '>=', 'minimum_stock');
                    break;
            }
        }

        $materials = $query->get();

        // Generate PDF
        $pdf = PDF::loadView('warehouse.reports.inventory-pdf', compact('materials'));

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

        // Generate PDF
        $pdf = PDF::loadView('warehouse.reports.movements-pdf', compact('movements'));

        // Save report record
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

        // Calculate statistics
        $stats = [
            'total_deliveries' => $deliveries->count(),
            'on_time_deliveries' => $deliveries->where('status', 'completed')
                ->where('delivery_date', '<=', DB::raw('expected_date'))
                ->count(),
            'total_items' => $deliveries->sum('items_count'),
            'by_status' => $deliveries->groupBy('status')
                ->map->count()
        ];

        // Generate PDF
        $pdf = PDF::loadView('warehouse.reports.deliveries-pdf', compact('deliveries', 'stats'));

        // Save report record
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

        // Calculate usage statistics
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

        // Generate PDF
        $pdf = PDF::loadView('warehouse.reports.usage-pdf', compact('usageStats'));

        // Save report record
        $report = Report::create([
            'type' => 'warehouse_usage',
            'generated_by_id' => auth()->id(),
            'parameters' => $request->all()
        ]);

        return $pdf->download('material-usage-report-' . now()->format('Y-m-d') . '.pdf');
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
} 