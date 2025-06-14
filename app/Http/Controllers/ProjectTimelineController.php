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
                'id' => 'task-' . $task->id,
                'title' => $task->title,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => $task->end_date->format('Y-m-d'),
                'extendedProps' => [
                    'type' => 'task',
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'progress' => $task->progress,
                    'contract' => $task->contract ? $task->contract->contract_number : null,
                    'room' => $task->room ? $task->room->name : null,
                    'scope' => $task->scopeType ? $task->scopeType->name : null,
                    'assignee' => $task->assignee ? $task->assignee->name : null,
                ]
            ];
        });

        // Get contracts for search and progress calculation
        $contracts = Contract::with(['tasks', 'client', 'contractor', 'rooms', 'rooms.scopeTypes', 'rooms.scopeTypes.materials'])
            ->orderBy('contract_number')
            ->get();

        // Add contracts as calendar events
        $contractEvents = $contracts->map(function($contract) {
            return [
                'id' => 'contract-' . $contract->id,
                'title' => $contract->title ?? $contract->contract_number,
                'start' => $contract->start_date ? $contract->start_date->format('Y-m-d') : null,
                'end' => $contract->end_date ? $contract->end_date->format('Y-m-d') : null,
                'extendedProps' => [
                    'type' => 'contract',
                    'client' => $contract->client ? $contract->client->name : null,
                    'contractor' => $contract->contractor ? $contract->contractor->name : null,
                    'status' => $contract->status,
                    'progress' => $contract->progress ?? 0,
                    'budget' => $contract->total_amount,
                ]
            ];
        });

        // Add scope types as calendar events
        $scopeEvents = collect();
        foreach ($contracts as $contract) {
            if ($contract->start_date) { // Ensure contract has a start date
                foreach ($contract->rooms as $room) {
                    foreach ($room->scopeTypes as $scopeType) {
                        $scopeStartDate = \Carbon\Carbon::parse($contract->start_date);
                        $scopeEndDate = $scopeStartDate->copy()->addDays($scopeType->estimated_days ?? 0);

                        $scopeEvents->push([
                            'id' => 'scope-' . $scopeType->id . '-contract-' . $contract->id,
                            'title' => 'Scope: ' . ($scopeType->name ?? 'N/A') . ' in ' . ($room->name ?? 'N/A'),
                            'start' => $scopeStartDate->format('Y-m-d'),
                            'end' => $scopeEndDate->format('Y-m-d'),
                            'extendedProps' => [
                                'type' => 'scope',
                                'contract_id' => $contract->id,
                                'room_id' => $room->id,
                                'scope_type_id' => $scopeType->id,
                                'estimated_days' => $scopeType->estimated_days,
                                'status' => $contract->status // Inherit contract status for display
                            ],
                            'className' => 'status-' . ($contract->status ? strtolower($contract->status) : 'secondary') // Apply a class based on contract status
                        ]);
                    }
                }
            }
        }

        // Combine contracts, tasks, and scopes for the calendar
        $calendarEvents = $contractEvents->concat($tasks)->concat($scopeEvents)->values();

        // If no events, add a demo event
        if ($calendarEvents->isEmpty()) {
            $calendarEvents = collect([
                [
                    'id' => 'demo-1',
                    'title' => 'Demo Event',
                    'start' => now()->format('Y-m-d'),
                    'end' => now()->addDay()->format('Y-m-d'),
                    'extendedProps' => [
                        'type' => 'demo',
                        'client' => 'Demo Client',
                        'status' => 'approved',
                        'progress' => 100
                    ]
                ]
            ]);
        }

        // Calculate overall project progress
        $totalOverallProgress = 0;
        $totalOverallTasks = 0;
        foreach ($contracts as $contract) {
            $contractTasksCount = $contract->tasks->count();
            if ($contractTasksCount > 0) {
                $contractTotalProgress = $contract->tasks->sum('progress');
                $individualContractProgress = round(($contractTotalProgress / $contractTasksCount), 2);
                $contract->progress = $individualContractProgress; // Add progress to contract object

                $totalOverallProgress += $contractTotalProgress;
                $totalOverallTasks += $contractTasksCount;
            }
        }
        $overallProjectProgress = $totalOverallTasks > 0 ? round(($totalOverallProgress / $totalOverallTasks), 2) : 0;

        // Get users for assignee filter
        $users = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('project-timeline.index', [
            'contracts' => $contracts,
            'calendarEvents' => $calendarEvents,
            'overallProjectProgress' => $overallProjectProgress,
            'users' => $users
        ]);
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
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $searchTerm = $request->query('term');
            $statusFilter = $request->query('status');
            $startDateFilter = $request->query('startDate');
            $endDateFilter = $request->query('endDate');
            $minBudgetFilter = $request->query('minBudget');
            $maxBudgetFilter = $request->query('maxBudget');

            // Project Tasks as events
            $tasksQuery = \App\Models\ProjectTask::query();

            if ($searchTerm) {
                $tasksQuery->where('title', 'like', '%' . $searchTerm . '%');
            }
            if ($statusFilter && $statusFilter !== 'all') {
                $tasksQuery->where('status', $statusFilter);
            }
            if ($startDateFilter) {
                $tasksQuery->where('start_date', '>=', $startDateFilter);
            }
            if ($endDateFilter) {
                $tasksQuery->where('end_date', '<=', $endDateFilter);
            }

            $tasks = $tasksQuery->get()->map(function($task) {
                return [
                    'id' => 'task-' . $task->id,
                    'title' => $task->title,
                    'start' => $task->start_date->format('Y-m-d'),
                    'end' => $task->end_date->format('Y-m-d'),
                    'extendedProps' => [
                        'type' => 'task',
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'progress' => $task->progress,
                    ]
                ];
            });

            // Contracts as events
            $contractsQuery = \App\Models\Contract::query()->with(['client', 'contractor']);

            if ($searchTerm) {
                $contractsQuery->where(function($q) use ($searchTerm) {
                    $q->where('contract_id', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('client', function($q_client) use ($searchTerm) {
                          $q_client->where('name', 'like', '%' . $searchTerm . '%');
                      });
                });
            }
            if ($statusFilter && $statusFilter !== 'all') {
                $statuses = explode(',', $statusFilter);
                $contractsQuery->whereIn('status', $statuses);
            }
            if ($startDateFilter) {
                $contractsQuery->where('start_date', '>=', $startDateFilter);
            }
            if ($endDateFilter) {
                $contractsQuery->where('end_date', '<=', $endDateFilter);
            }
            if ($minBudgetFilter) {
                $contractsQuery->where('total_amount', '>=', $minBudgetFilter);
            }
            if ($maxBudgetFilter) {
                $contractsQuery->where('total_amount', '<=', $maxBudgetFilter);
            }

            $contracts = $contractsQuery->get()->map(function($contract) {
                $safeStatus = $contract->status ?: 'default';
                return [
                    'id' => 'contract-' . $contract->id,
                    'title' => $contract->client->name ?? 'Unknown Client',
                    'start' => $contract->start_date->format('Y-m-d'),
                    'end' => $contract->end_date->format('Y-m-d'),
                    'className' => 'status-' . $safeStatus,
                    'extendedProps' => [
                        'type' => 'contract',
                        'contract_id' => $contract->contract_id,
                        'client' => $contract->client->name ?? 'Unknown Client',
                        'contractor' => $contract->contractor->name ?? 'N/A',
                        'status' => $safeStatus,
                        'budget' => $contract->total_amount,
                        'scope' => $contract->scope_of_work,
                    ]
                ];
            });

            // Combine tasks and contracts for FullCalendar
            $calendarEvents = $tasks->concat($contracts);

            // Prepare Gantt data (only contracts for now, as tasks are separate)
            // Need to decide if tasks should also be on Gantt chart, and how to structure it.
            // For now, only contracts for Gantt to fix initial issue.
            $ganttTasks = $contracts->map(function($contract) {
                $safeStatus = $contract->status ?: 'default';
                return [
                    'id' => 'contract-' . $contract->id,
                    'name' => $contract->client->name ?? 'Unknown Client',
                    'start' => $contract->start_date->format('YYYY-MM-DD'),
                    'end' => $contract->end_date->format('YYYY-MM-DD'),
                    'progress' => match($safeStatus) {
                        'approved' => 100,
                        'draft' => 50,
                        'rejected' => 0,
                        default => 0
                    },
                    'dependencies' => '',
                    'custom_class' => 'status-' . $safeStatus
                ];
            });

            return response()->json([
                'calendar' => $calendarEvents,
                'gantt' => $ganttTasks
            ]);

            // Temporarily dump the data to debug
            dd($calendarEvents->toJson(), $ganttTasks->toJson());

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in ProjectTimelineController apiEvents: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in file ' . $e->getFile());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
} 