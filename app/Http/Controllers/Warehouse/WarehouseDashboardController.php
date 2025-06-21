<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Delivery;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        // Get total materials count
        $totalMaterials = Material::count();
        
        // Get low stock materials (less than 10% of minimum stock)
        $lowStockMaterials = Material::whereRaw('current_stock < (minimum_stock * 0.1)')
            ->with('category')
            ->get();
            
        // Get pending deliveries
        $pendingDeliveries = Delivery::where('status', 'pending')
            ->with(['items.material'])
            ->latest()
            ->take(5)
            ->get();
            
        // Get recent stock movements
        $recentMovements = StockMovement::with(['material'])
            ->latest()
            ->take(10)
            ->get();
            
        // Get stock value statistics
        $stockValue = Material::sum(DB::raw('current_stock * base_price'));
        
        // Get monthly stock movements
        $monthlyMovements = StockMovement::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as incoming'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as outgoing')
        )
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->get();
        
        return view('warehouse.dashboard', compact(
            'totalMaterials',
            'lowStockMaterials',
            'pendingDeliveries',
            'recentMovements',
            'stockValue',
            'monthlyMovements'
        ));
    }
    
    public function getStockAlerts()
    {
        $alerts = Material::whereRaw('current_stock < minimum_stock')
            ->with('category')
            ->get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'current_stock' => $material->current_stock,
                    'minimum_stock' => $material->minimum_stock,
                    'category' => $material->category->name,
                    'status' => $material->current_stock < ($material->minimum_stock * 0.1) ? 'critical' : 'warning'
                ];
            });
            
        return response()->json($alerts);
    }
    
    public function getStockMovements(Request $request)
    {
        $query = StockMovement::with(['material'])
            ->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                return $q->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            });
            
        $movements = $query->latest()->paginate(15);
        
        return response()->json($movements);
    }
} 