<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\Contract;
use App\Models\Room;
use App\Models\ScopeType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectTimelineController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectTask::with(['contract', 'room', 'scopeType', 'assignee']);

        // Filter by contract if specified
        if ($request->has('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assignee
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Get tasks for calendar view
        $tasks = $query->get()->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => $task->end_date->format('Y-m-d'),
                'status' => $task->status,
                'priority' => $task->priority,
                'progress' => $task->progress,
                'contract' => $task->contract->contract_number,
                'room' => $task->room ? $task->room->name : null,
                'scope' => $task->scopeType ? $task->scopeType->name : null,
                'assignee' => $task->assignee ? $task->assignee->name : null,
                'url' => route('project-tasks.show', $task)
            ];
        });

        // Get contracts for search
        $contracts = Contract::select('id', 'contract_number', 'title')
            ->orderBy('contract_number')
            ->get();

        // Get users for assignee filter
        $users = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('project-timeline.index', compact('tasks', 'contracts', 'users'));
    }

    public function create()
    {
        $contracts = Contract::with(['rooms', 'rooms.scopeTypes'])
            ->orderBy('contract_number')
            ->get();
        $users = User::orderBy('name')->get();

        return view('project-timeline.create', compact('contracts', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'room_id' => 'nullable|exists:rooms,id',
            'scope_type_id' => 'nullable|exists:scope_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['progress'] = 0;

        $task = ProjectTask::create($validated);

        return redirect()->route('project-timeline.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(ProjectTask $task)
    {
        $task->load(['contract', 'room', 'scopeType', 'assignee', 'creator']);
        return view('project-timeline.show', compact('task'));
    }

    public function edit(ProjectTask $task)
    {
        $contracts = Contract::with(['rooms', 'rooms.scopeTypes'])
            ->orderBy('contract_number')
            ->get();
        $users = User::orderBy('name')->get();

        return view('project-timeline.edit', compact('task', 'contracts', 'users'));
    }

    public function update(Request $request, ProjectTask $task)
    {
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'scope_type_id' => 'nullable|exists:scope_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'progress' => 'required|integer|min:0|max:100',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        $task->update($validated);

        return redirect()->route('project-timeline.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(ProjectTask $task)
    {
        $task->delete();
        return redirect()->route('project-timeline.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateProgress(Request $request, ProjectTask $task)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $task->updateProgress($validated['progress']);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully'
        ]);
    }

    public function markAsDelayed(ProjectTask $task)
    {
        $task->markAsDelayed();
        return redirect()->back()->with('success', 'Task marked as delayed.');
    }

    public function getTasksByContract(Contract $contract)
    {
        $tasks = $contract->tasks()
            ->with(['room', 'scopeType', 'assignee'])
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->start_date->format('Y-m-d'),
                    'end' => $task->end_date->format('Y-m-d'),
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'progress' => $task->progress,
                    'room' => $task->room ? $task->room->name : null,
                    'scope' => $task->scopeType ? $task->scopeType->name : null,
                    'assignee' => $task->assignee ? $task->assignee->name : null,
                    'url' => route('project-tasks.show', $task)
                ];
            });

        return response()->json($tasks);
    }

    public function apiEvents(Request $request)
    {
        // Project Tasks as events
        $tasks = \App\Models\ProjectTask::all()->map(function($task) {
            return [
                'id' => 'task-' . $task->id,
                'title' => $task->title,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => $task->end_date->format('Y-m-d'),
                'type' => 'task',
                'backgroundColor' => '#3788d8', // blue for tasks
            ];
        });

        // Contracts as events
        $contracts = \App\Models\Contract::all()->map(function($contract) {
            return [
                'id' => 'contract-' . $contract->id,
                'title' => 'Contract: ' . $contract->contract_number,
                'start' => $contract->start_date->format('Y-m-d'),
                'end' => $contract->end_date->format('Y-m-d'),
                'type' => 'contract',
                'backgroundColor' => '#28a745', // green for contracts
            ];
        });

        // Merge and return all events
        $events = $tasks->merge($contracts)->values();

        return response()->json($events);
    }
} 