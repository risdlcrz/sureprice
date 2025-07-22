<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Delivery;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;

class WarehouseDashboardController extends Controller
{
    private function logPageView($description, $modelType = null, $modelId = null)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    public function index()
    {
        try {
            $this->logPageView('Viewed Warehouse Dashboard');
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
        } catch (\Exception $e) {
            \Log::error('Error loading warehouse dashboard: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Failed to load warehouse dashboard: ' . $e->getMessage());
        }
    }
    
    public function getStockAlerts()
    {
        try {
            $this->logPageView('Viewed Warehouse Stock Alerts');
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
        } catch (\Exception $e) {
            \Log::error('Error fetching stock alerts: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch stock alerts.'], 500);
        }
    }
    
    public function getStockMovements(Request $request)
    {
        try {
            $this->logPageView('Viewed Warehouse Stock Movements');
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
        } catch (\Exception $e) {
            \Log::error('Error fetching stock movements: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch stock movements.'], 500);
        }
    }

    public function warehouseLogs()
    {
        $this->logPageView('Viewed Warehouse Logs');
        $activities = Activity::latest()->take(50)->get();
        return view('warehouse.logs', compact('activities'));
    }
} 