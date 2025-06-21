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

class ProcurementController extends Controller
{
    public function index()
    {
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
        $projects = Project::with(['contract', 'client'])
            ->latest()
            ->get();

        return view('procurement.project-dashboard', compact('projects'));
    }

    public function inventoryDashboard()
    {
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
        $projects = Project::with(['contract', 'client'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('procurement.project-history', compact('projects'));
    }

    public function analyticsDashboard()
    {
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
        // You can fetch procurement-specific notifications here
        $notifications = \App\Models\Notification::where('for_role', 'procurement')
                                     ->orWhere('for_user_id', Auth::id())
                                     ->latest()->get();

        return view('procurement.notification-hub', compact('notifications'));
    }

    public function inventoryCreate()
    {
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

    public function inventoryEdit(Inventory $inventory)
    {
        return view('procurement.inventory-edit', compact('inventory'));
    }

    public function inventoryUpdate(Request $request, Inventory $inventory)
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
        // Return a view or data for low stock items
        return view('procurement.inventory-low-stock');
    }

    public function inventoryExpiring()
    {
        // Return a view or data for expiring items
        return view('procurement.inventory-expiring');
    }
} 