<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    public function index(Project $project)
    {
        $tasks = $project->tasks()->with('assignee')->latest()->paginate(10);
        return view('projects.tasks.index', compact('project', 'tasks'));
    }

    public function create(Project $project)
    {
        $users = User::all();
        return view('projects.tasks.create', compact('project', 'users'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $task = $project->tasks()->create([
                ...$validated,
                'status' => 'pending',
                'progress' => 0
            ]);

            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    public function show(Project $project, ProjectTask $task)
    {
        $task->load('assignee');
        return view('projects.tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, ProjectTask $task)
    {
        $users = User::all();
        return view('projects.tasks.edit', compact('project', 'task', 'users'));
    }

    public function update(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'required|exists:users,id',
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        try {
            $task->update($validated);

            // Update project progress based on tasks
            $averageProgress = $project->tasks()->avg('progress') ?? 0;
            $project->update(['progress' => round($averageProgress)]);

            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project, ProjectTask $task)
    {
        try {
            $task->delete();
            return redirect()->route('projects.tasks.index', $project)
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }
} 