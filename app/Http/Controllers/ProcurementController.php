<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierInvitation;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Contract;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\Notification;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Activity;

class ProcurementController extends Controller
{
    private function logPageView($description)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => null,
            'model_id' => null
        ]);
    }

    public function index()
    {
        $this->logPageView('Viewed Procurement Dashboard');
        $recentInvitations = SupplierInvitation::with(['contract', 'materials'])
            ->latest()
            ->take(5)
            ->get();

        $recentInquiries = Inquiry::with('contract')
            ->latest()
            ->take(5)
            ->get();

        $recentQuotations = Quotation::with('contract')
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseOrders = PurchaseOrder::with(['contract', 'supplier'])
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseRequests = PurchaseRequest::with('contract')
            ->where('requested_by', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('procurement.dashboard', compact(
            'recentInvitations',
            'recentInquiries',
            'recentQuotations',
            'recentPurchaseOrders',
            'recentPurchaseRequests'
        ));
    }

    public function projectDashboard()
    {
        $this->logPageView('Viewed Project Dashboard');
        // Dummy or simple values for demonstration
        $totalBudget = \App\Models\Contract::sum('total_amount');
        $totalSpent = \App\Models\PurchaseOrder::sum('total_amount');
        $recentContracts = \App\Models\Contract::latest()->take(5)->get();
        $recentPurchaseOrders = \App\Models\PurchaseOrder::latest()->take(5)->get();
        $recentPurchaseRequests = \App\Models\PurchaseRequest::latest()->take(5)->get();
        return view('procurement.project-dashboard', compact('totalBudget', 'totalSpent', 'recentContracts', 'recentPurchaseOrders', 'recentPurchaseRequests'));
    }

    public function inventoryDashboard()
    {
        $this->logPageView('Viewed Inventory Dashboard');
        $inventories = Inventory::with(['material.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $lowStockItems = Inventory::lowStock()->count();
        $expiringItems = Inventory::expiring()->count();
        $totalItems = Inventory::count();

        return view('procurement.inventory-dashboard', compact('inventories', 'lowStockItems', 'expiringItems', 'totalItems'));
    }

    public function projectHistory()
    {
        $this->logPageView('Viewed Project History');
        $projects = Project::with(['contract', 'client'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('procurement.project-history', compact('projects'));
    }

    public function analyticsDashboard()
    {
        $this->logPageView('Viewed Analytics Dashboard');
        // Get analytics data
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        
        $totalPurchaseOrders = PurchaseOrder::count();
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
        $approvedPurchaseOrders = PurchaseOrder::where('status', 'approved')->count();

        return view('procurement.analytics-dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalPurchaseOrders',
            'pendingPurchaseOrders',
            'approvedPurchaseOrders'
        ));
    }

    public function notificationHub()
    {
        $this->logPageView('Viewed Notification Hub');
        // You can fetch procurement-specific notifications here
        $notifications = \App\Models\Notification::where('for_role', 'procurement')
                                     ->orWhere('for_user_id', Auth::id())
                                     ->latest()->get();

        return view('procurement.notification-hub', compact('notifications'));
    }

    public function inventoryCreate()
    {
        $this->logPageView('Viewed Inventory Create Page');
        // Return a view for creating a new inventory item
        return view('procurement.inventory-create');
    }

    public function inventoryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,obsolete',
            'last_restock_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $category = Category::firstOrCreate(['name' => $request->input('category_name')]);

            $material = Material::firstOrCreate(
                ['name' => $request->input('material_name'), 'category_id' => $category->id],
                ['unit' => $request->input('unit')]
            );

            $inventory = Inventory::create([
                'material_id' => $material->id,
                'quantity' => $request->input('quantity'),
                'unit' => $request->input('unit'),
                'location' => $request->input('location'),
                'status' => $request->input('status', 'active'),
                'last_restock_date' => $request->input('last_restock_date'),
            ]);

            $material->increment('current_stock', $request->input('quantity'));
        });

        return redirect()->route('procurement.inventory.index')
            ->with('success', 'Inventory item added successfully.');
    }

    public function inventoryEdit($id)
    {
        $this->logPageView('Viewed Inventory Edit Page');
        $inventory = Inventory::find($id);
        if (! $inventory) {
            return redirect()->route('procurement.inventory.create')
                ->with('warning', 'Inventory record not found. Please add it first.');
        }
        return view('procurement.inventory-edit', compact('inventory'));
    }

    public function inventoryUpdate(Request $request, Inventory $inventory)
    {
        $this->logPageView('Viewed Inventory Update Page');
        $validator = Validator::make($request->all(), [
            'material_name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,obsolete',
            'last_restock_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $inventory) {
            $oldQuantity = $inventory->quantity;

            $category = Category::firstOrCreate(['name' => $request->input('category_name')]);

            $material = $inventory->material;
            $material->update([
                'name' => $request->input('material_name'),
                'category_id' => $category->id,
                'unit' => $request->input('unit'),
            ]);

            $inventory->update([
                'quantity' => $request->input('quantity'),
                'unit' => $request->input('unit'),
                'location' => $request->input('location'),
                'status' => $request->input('status'),
                'last_restock_date' => $request->input('last_restock_date'),
            ]);

            // Adjust stock based on the quantity change
            $quantityDifference = $request->input('quantity') - $oldQuantity;
            $material->increment('current_stock', $quantityDifference);
        });

        return redirect()->route('procurement.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function inventoryDestroy(Inventory $inventory)
    {
        $this->logPageView('Viewed Inventory Destroy Page');
        DB::transaction(function () use ($inventory) {
            $material = $inventory->material;

            if ($material) {
                $material->decrement('current_stock', $inventory->quantity);
            }

            $inventory->delete();
        });

        return redirect()->route('procurement.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function inventoryAdjustStock(Request $request, Inventory $inventory)
    {
        $this->logPageView('Viewed Inventory Adjust Stock Page');
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
            'operation' => 'required|in:add,subtract',
            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request, $inventory) {
            if ($request->operation === 'subtract' && $request->quantity > $inventory->quantity) {
                $validator->errors()->add('quantity', 'Cannot subtract more than the available quantity.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $inventory) {
            $adjustment_quantity = $request->input('quantity');
            $operation = $request->input('operation');
            $material = $inventory->material;

            if ($operation === 'add') {
                $inventory->increment('quantity', $adjustment_quantity);
                if ($material) {
                    $material->increment('current_stock', $adjustment_quantity);
                }
            } elseif ($operation === 'subtract') {
                $inventory->decrement('quantity', $adjustment_quantity);
                if ($material) {
                    $material->decrement('current_stock', $adjustment_quantity);
                }
            }
        });

        return redirect()->route('procurement.inventory.index')
            ->with('success', 'Stock adjusted successfully.');
    }

    public function inventoryLowStock()
    {
        $this->logPageView('Viewed Inventory Low Stock Page');
        $inventories = \App\Models\Inventory::with(['material.category'])
            ->lowStock()
            ->orderBy('quantity', 'asc')
            ->paginate(10);
        return view('procurement.inventory-low-stock', compact('inventories'));
    }

    public function inventoryExpiring()
    {
        $this->logPageView('Viewed Inventory Expiring Page');
        $inventories = \App\Models\Inventory::with(['material.category'])
            ->expiring()
            ->orderBy('expiry_date', 'asc')
            ->paginate(10);
        return view('procurement.inventory-expiring', compact('inventories'));
    }

    public function projectShow(Project $project)
    {
        $this->logPageView('Viewed Project Show Page');
        $project->load([
            'contract',
            'projectManager',
            'clientRepresentative',
            'tasks',
            'contract.purchaseRequests',
            'contract.purchaseOrders'
        ]);

        return view('procurement.projects.show', compact('project'));
    }

    public function projectTasks(Project $project)
    {
        $this->logPageView('Viewed Project Tasks Page');
        $tasks = $project->tasks()
            ->with(['assignee'])
            ->latest()
            ->paginate(10);

        return view('procurement.projects.tasks', compact('project', 'tasks'));
    }

    public function projectProcurement(Project $project)
    {
        $this->logPageView('Viewed Project Procurement Page');
        $project->load([
            'contract.purchaseRequests' => function($query) {
                $query->latest();
            },
            'contract.purchaseOrders' => function($query) {
                $query->latest();
            },
            'contract.purchaseRequests.items',
            'contract.purchaseOrders.items'
        ]);

        return view('procurement.projects.procurement', compact('project'));
    }

    public function projectAnalytics(Project $project)
    {
        $this->logPageView('Viewed Project Analytics Page');
        $project->load([
            'contract.purchaseOrders',
            'contract.purchaseRequests',
            'tasks'
        ]);

        // Calculate procurement metrics
        $totalBudget = $project->budget;
        $totalSpent = $project->contract->purchaseOrders->sum('total_amount');
        $remainingBudget = $totalBudget - $totalSpent;
        $budgetUtilization = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;

        // Task metrics
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();
        $pendingTasks = $project->tasks->where('status', 'pending')->count();
        $taskCompletion = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        // Purchase request and order metrics
        $totalPurchaseRequests = $project->contract->purchaseRequests->count();
        $totalPurchaseOrders = $project->contract->purchaseOrders->count();
        $pendingPurchaseRequests = $project->contract->purchaseRequests->where('status', 'pending')->count();
        $pendingPurchaseOrders = $project->contract->purchaseOrders->where('status', 'pending')->count();

        return view('procurement.projects.analytics', compact(
            'project',
            'totalBudget',
            'totalSpent',
            'remainingBudget',
            'budgetUtilization',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'taskCompletion',
            'totalPurchaseRequests',
            'totalPurchaseOrders',
            'pendingPurchaseRequests',
            'pendingPurchaseOrders'
        ));
    }

    public function suppliersRankings()
    {
        $this->logPageView('Viewed Suppliers Rankings Page');
        $suppliers = \App\Models\Supplier::with(['evaluations', 'metrics'])->get();
        $rankingService = app(\App\Services\SupplierRankingService::class);
        $rankings = $rankingService->calculateRankings($suppliers);
        return view('procurement.suppliers-rankings', compact('rankings'));
    }

    // --- Supplier Recommendation for Procurement Analytics Dashboard ---
    public function generalSupplierRecommendation(Request $request)
    {
        $this->logPageView('Viewed General Supplier Recommendation Page');
        $materials = \App\Models\Material::orderBy('name')->get();
        $selectedMaterialId = $request->input('material_id', $materials->first()->id ?? null);

        $suppliers = \App\Models\Supplier::with(['evaluations', 'metrics', 'materials'])->get();
        $rankingService = app(\App\Services\SupplierRankingService::class);
        $rankings = $rankingService->calculateRankings($suppliers);

        // Filter by material if specified
        if ($selectedMaterialId) {
            $rankings = $rankings->filter(function ($ranking) use ($selectedMaterialId) {
                return $ranking['supplier']->materials->contains('id', $selectedMaterialId);
            });
        }

        // Format for recommendation display using the same weights as rankings
        $recommended = $rankings->sortByDesc('score')->take(5)->map(function ($ranking) {
            $supplier = $ranking['supplier'];
            $metrics = $supplier->metrics;
            return [
                'supplier' => [
                    'name' => $supplier->company_name,
                    'on_time_delivery_rate' => $metrics ? number_format(($metrics->ontime_deliveries / $metrics->total_deliveries) * 100, 2) : 0,
                    'average_defect_rate' => $metrics ? number_format(($metrics->defective_units / $metrics->total_units) * 100, 2) : 0,
                    'average_cost_variance' => $metrics ? number_format(abs(($metrics->actual_cost - $metrics->estimated_cost) / $metrics->estimated_cost), 2) : 0,
                ],
                'score' => $ranking['score'],
                'rank' => $ranking['rank'],
                'distance' => 5 - $ranking['score'] // Convert score to distance (lower is better)
            ];
        })->sortBy('distance')->values();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('procurement.suppliers.partials.recommendation-tables', [
                    'recommended' => $recommended,
                ])->render()
            ]);
        }

        return view('procurement.suppliers.general-recommendation', [
            'materials' => $materials,
            'selectedMaterialId' => $selectedMaterialId,
            'recommended' => $recommended,
        ]);
    }

    public function procurementLogs()
    {
        $this->logPageView('Viewed Procurement Logs Page');
        $procurementModels = [
            \App\Models\PurchaseOrder::class,
            \App\Models\PurchaseRequest::class,
            \App\Models\Quotation::class,
        ];
        $activities = \App\Models\Activity::whereIn('model_type', $procurementModels)
            ->latest()
            ->take(50)
            ->get();
        return view('procurement.logs', compact('activities'));
    }
} 