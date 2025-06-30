<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['contract', 'projectManager', 'clientRepresentative'])
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
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
                'status' => 'pending'
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
            'status' => 'required|in:pending,active,on_hold,completed,cancelled',
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
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $onHoldProjects = Project::where('status', 'on_hold')->count();

        $recentProjects = Project::with(['contract', 'projectManager', 'clientRepresentative'])
            ->latest()
            ->take(5)
            ->get();

        $projectsByStatus = [
            'pending' => Project::where('status', 'pending')->count(),
            'active' => $activeProjects,
            'on_hold' => $onHoldProjects,
            'completed' => $completedProjects,
            'cancelled' => Project::where('status', 'cancelled')->count(),
        ];

        return view('admin.project-dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'onHoldProjects',
            'recentProjects',
            'projectsByStatus'
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
} 