<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Contract;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SupplierSelectionService;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['contract', 'projectManager', 'clientRepresentative']);

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('project_number', 'like', "%$search%")
                  ->orWhereHas('contract', function($c) use ($search) {
                      $c->where('contract_number', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%")
                        ->orWhere('title', 'like', "%$search%") ;
                  });
            });
        }

        $projects = $query->latest()->paginate(10);

        $userParty = null;
        if (auth()->check()) {
            $userParty = \App\Models\Party::where('email', auth()->user()->email)->first();
        }

        return view('projects.index', compact('projects', 'userParty'));
    }

    public function create()
    {
        $contracts = Contract::where('status', 'active')->get();
        $users = User::all();
        
        return view('projects.create', compact('contracts', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'project_manager_id' => 'required|exists:users,id',
            'client_representative_id' => 'required|exists:users,id',
            'budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $project = Project::create([
                'project_number' => Project::generateProjectNumber(),
                ...$validated,
                'status' => 'proposed'
            ]);

            DB::commit();

            return redirect()->route('projects.show', $project)
                ->with('success', 'Project created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project->load(['contract', 'projectManager', 'clientRepresentative', 'tasks']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $contracts = Contract::where('status', 'active')->get();
        $users = User::all();
        
        return view('projects.edit', compact('project', 'contracts', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:proposed,planning,approved,in_progress,on_hold,completed,closed,cancelled',
            'project_manager_id' => 'required|exists:users,id',
            'client_representative_id' => 'required|exists:users,id',
            'budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            $project->update($validated);
            return redirect()->route('projects.show', $project)
                ->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $totalProjects = Project::count();
        $proposedProjects = Project::where('status', 'proposed')->count();
        $planningProjects = Project::where('status', 'planning')->count();
        $approvedProjects = Project::where('status', 'approved')->count();
        $inProgressProjects = Project::where('status', 'in_progress')->count();
        $onHoldProjects = Project::where('status', 'on_hold')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $closedProjects = Project::where('status', 'closed')->count();
        $cancelledProjects = Project::where('status', 'cancelled')->count();

        $recentProjects = Project::with(['contract', 'projectManager', 'clientRepresentative'])
            ->latest()
            ->take(5)
            ->get();

        $projectsByStatus = [
            'proposed' => $proposedProjects,
            'planning' => $planningProjects,
            'approved' => $approvedProjects,
            'in_progress' => $inProgressProjects,
            'on_hold' => $onHoldProjects,
            'completed' => $completedProjects,
            'closed' => $closedProjects,
            'cancelled' => $cancelledProjects,
        ];

        // --- Added for dashboard quick stats ---
        $totalBudget = \App\Models\Contract::sum('total_amount');
        $totalSpent = \App\Models\Contract::sum('materials_cost') + \App\Models\Contract::sum('labor_cost');
        $recentContracts = \App\Models\Contract::latest()->take(5)->get();
        $recentPurchaseOrders = \App\Models\PurchaseOrder::latest()->take(5)->get();
        // ---------------------------------------

        return view('admin.project-dashboard', compact(
            'totalProjects',
            'proposedProjects',
            'planningProjects',
            'approvedProjects',
            'inProgressProjects',
            'onHoldProjects',
            'completedProjects',
            'closedProjects',
            'cancelledProjects',
            'recentProjects',
            'projectsByStatus',
            'totalBudget',
            'totalSpent',
            'recentContracts',
            'recentPurchaseOrders'
        ));
    }

    public function updateProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        try {
            $project->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Project progress updated successfully.',
                'progress' => $project->progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project progress: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- Supplier Recommendation and Selection ---
    public function recommendSuppliers(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $budget = $project->budget;

        // Get all materials for dropdown
        $materials = Material::orderBy('name')->get();
        $selectedMaterialId = $request->input('material_id', $materials->first()->id ?? null);

        // Define ideal metrics (can be adjusted or made user-input)
        $projectFeatures = [
            'on_time_delivery_rate' => $request->input('on_time_delivery_rate', 90),
            'average_defect_rate' => $request->input('average_defect_rate', 2),
            'average_cost_variance' => $request->input('average_cost_variance', 0),
        ];

        // Fetch all suppliers with their metrics and materials
        $suppliers = Supplier::with(['metrics', 'materials'])->get()->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'material_ids' => $supplier->materials->pluck('id')->toArray(),
                'on_time_delivery_rate' => $supplier->metrics ? $supplier->metrics->on_time_delivery_rate : 0,
                'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                'cost' => $supplier->metrics->average_cost_variance ?? 0, // or use a price field if available
            ];
        })->toArray();

        $service = new SupplierSelectionService();
        $filteredSuppliers = $service->filterByMaterial($suppliers, $selectedMaterialId);
        $recommended = $service->recommend($filteredSuppliers, $projectFeatures, 5);
        $optimal = $service->optimize($recommended, $budget);

        // If AJAX, return partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.suppliers.partials.recommendation-tables', [
                    'recommended' => $recommended,
                    'optimal' => $optimal,
                ])->render()
            ]);
        }

        return view('admin.suppliers.recommendations', [
            'project' => $project,
            'materials' => $materials,
            'selectedMaterialId' => $selectedMaterialId,
            'recommended' => $recommended,
            'optimal' => $optimal,
            'projectFeatures' => $projectFeatures,
        ]);
    }

    // General Supplier Recommendation for Analytics Dashboard
    public function generalSupplierRecommendation(Request $request)
    {
        $materials = \App\Models\Material::orderBy('name')->get();
        $selectedMaterialId = $request->input('material_id', $materials->first()->id ?? null);

        $projectFeatures = [
            'on_time_delivery_rate' => $request->input('on_time_delivery_rate', 90),
            'average_defect_rate' => $request->input('average_defect_rate', 2),
            'average_cost_variance' => $request->input('average_cost_variance', 0),
        ];
        $budget = $request->input('budget', 100000); // Default or user input

        $suppliers = Supplier::with(['metrics', 'materials'])->get()->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'material_ids' => $supplier->materials->pluck('id')->toArray(),
                'on_time_delivery_rate' => $supplier->metrics ? $supplier->metrics->on_time_delivery_rate : 0,
                'average_defect_rate' => $supplier->metrics->average_defect_rate ?? 0,
                'average_cost_variance' => $supplier->metrics->average_cost_variance ?? 0,
                'cost' => $supplier->metrics->average_cost_variance ?? 0,
            ];
        })->toArray();

        $service = new \App\Services\SupplierSelectionService();
        $filteredSuppliers = $service->filterByMaterial($suppliers, $selectedMaterialId);
        $recommended = $service->recommend($filteredSuppliers, $projectFeatures, 5);
        $optimal = $service->optimize($recommended, $budget);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.suppliers.partials.recommendation-tables', [
                    'recommended' => $recommended,
                    'optimal' => $optimal,
                ])->render()
            ]);
        }

        return view('admin.suppliers.general-recommendation', [
            'materials' => $materials,
            'selectedMaterialId' => $selectedMaterialId,
            'recommended' => $recommended,
            'optimal' => $optimal,
            'projectFeatures' => $projectFeatures,
            'budget' => $budget,
        ]);
    }
} 