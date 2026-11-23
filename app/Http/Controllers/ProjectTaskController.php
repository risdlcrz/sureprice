<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\SafeErrors;

class ProjectTaskController extends Controller
{
    public function index(Project $project)
    {
        $tasks = $project->tasks()->with('assignee')->latest()->paginate(10);
        return view('projects.tasks.index', compact('project', 'tasks'));
    }

    public function create(Project $project)
    {
        return view('projects.tasks.form', ['project' => $project, 'users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request, Project $project)
    {
        $task = $project->tasks()->create($request->validate(ProjectTask::rules(false)) + [
            'contract_id' => $project->contract_id,
            'status' => 'pending',
            'progress' => 0,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Task created successfully.');
    }

    public function show(Project $project, ProjectTask $task)
    {
        $task->load('assignee');
        return view('projects.tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, ProjectTask $task)
    {
        return view('projects.tasks.form', [
            'project' => $project,
            'task' => $task,
            'users' => User::orderBy('name')->get(),
            'rooms' => $task->contract?->rooms()->with('scopeTypes')->get() ?? collect(),
        ]);
    }

    public function update(Request $request, Project $project, ProjectTask $task)
    {
        // project progress is recalculated by ProjectTask's saved event
        $task->update($request->validate(ProjectTask::rules(true)));

        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, ProjectTask $task)
    {
        try {
            $task->delete();
            return redirect()->route('projects.tasks.index', $project)
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', SafeErrors::message($e, 'Failed to delete task'));
        }
    }
} 