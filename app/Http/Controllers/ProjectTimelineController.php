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
        $user = auth()->user();
        $party = $user->party;

        // Build contracts query
        $contractsQuery = Contract::with([
            'tasks', 'client', 'contractor', 'rooms', 'rooms.scopeTypes', 'rooms.scopeTypes.materials',
            'purchaseOrders', 'purchaseOrders.deliveries', 'purchaseOrders.items'
        ])->orderBy('contract_number');

        // If the user is a client, restrict to their contracts only
        if ($party && $party->entity_type === 'client') {
            $contractsQuery->where('client_id', $party->id);
        }

        $contracts = $contractsQuery->get();

        // Restrict project tasks to only those under the client's contracts
        $contractIds = $contracts->pluck('id')->toArray();
        $query = ProjectTask::with(['contract', 'room', 'scopeType', 'assignee'])
            ->whereIn('contract_id', $contractIds);

        // Filter by contract if specified
        if ($request->has('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('due_date', [$request->start_date, $request->end_date]);
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
                'title' => $task->name,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => ($task->due_date ?? $task->start_date)->format('Y-m-d'),
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
        $contracts = Contract::with(['tasks', 'client', 'contractor', 'rooms', 'rooms.scopeTypes', 'rooms.scopeTypes.materials', 'purchaseOrders', 'purchaseOrders.deliveries', 'purchaseOrders.items'])
            ->orderBy('contract_number')
            ->get();

        // Add contracts as calendar events
        $contractEvents = $contracts->map(function($contract) {
            return (object)[
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
                    'contract_number' => $contract->contract_number
                ]
            ];
        });

        // Add scope types as calendar events (parallel per room, sequenced within each room)
        $scopeEvents = collect();
        foreach ($contracts as $contract) {
            if ($contract->start_date) {
                foreach ($contract->rooms as $room) {
                    $currentScopeStart = \Carbon\Carbon::parse($contract->start_date);
                    $scopes = $room->scopeTypes->sortBy(function($scopeType) {
                        return $scopeType->order ?? $scopeType->id;
                    });
                    foreach ($scopes as $scopeType) {
                        $scopeDuration = $scopeType->estimated_days ?? 0;
                        $scopeEnd = $currentScopeStart->copy()->addDays(max($scopeDuration - 1, 0));
                        $scopeEvents->push([
                            'id' => 'scope-' . $scopeType->id . '-contract-' . $contract->id,
                            'title' => $scopeType->name . ($room->name ? ' (' . $room->name . ')' : ''),
                            'start' => $currentScopeStart->format('Y-m-d'),
                            'end' => $scopeEnd->format('Y-m-d'),
                            'extendedProps' => [
                                'type' => 'scope',
                                'contract_id' => $contract->id,
                                'room_id' => $room->id,
                                'scope_type_id' => $scopeType->id,
                                'estimated_days' => $scopeType->estimated_days,
                                'status' => $contract->status,
                                'contractor_id' => $contract->contractor_id,
                                'contract_number' => $contract->contract_number,
                                'contractor' => $contract->contractor ? $contract->contractor->name : 'N/A',
                                'room' => $room->name,
                                'scope' => $scopeType->name
                            ],
                            'className' => 'status-' . ($contract->status ? strtolower($contract->status) : 'secondary')
                        ]);
                        $currentScopeStart = $scopeEnd->copy()->addDay();
                    }
                }
            }
        }

        // Add purchase order deliveries as calendar events
        $deliveryEvents = collect();
        foreach ($contracts as $contract) {
            foreach ($contract->purchaseOrders as $po) {
                $deliveryDate = $po->delivery_date;
                if ($deliveryDate) {
                    $deliveryEvents->push([
                        'id' => 'po-' . $po->id . '-contract-' . $contract->id,
                        'title' => 'PO: ' . ($po->po_number ?? 'N/A') . ' - ' . ($po->status ? ucwords(str_replace('_', ' ', $po->status)) : 'N/A'),
                        'start' => $deliveryDate->format('Y-m-d'),
                        'end' => $deliveryDate->format('Y-m-d'), // Deliveries are point-in-time events
                        'extendedProps' => [
                            'type' => 'delivery',
                            'contract_id' => $contract->id,
                            'po_id' => $po->id,
                            'status' => $po->status,
                            'expected_delivery_date' => $po->delivery_date ? $po->delivery_date->format('Y-m-d') : null,
                            'delivery_date' => $po->delivery_date ? $po->delivery_date->format('Y-m-d') : null,
                            'total_amount' => $po->total_amount,
                        ],
                        'className' => 'status-' . ($po->status ? strtolower($po->status) : 'secondary')
                    ]);
                }
            }
        }

        // Combine only scope events for the calendar
        $calendarEvents = $scopeEvents->values();

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
                        'progress' => 100,
                        'contract_number' => 'CT20240001'
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
        return view('projects.tasks.form', [
            'timeline' => true,
            'projects' => \App\Models\Project::with('contract')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(ProjectTask::rules(false) + ['project_id' => 'required|exists:projects,id']);
        $project = \App\Models\Project::findOrFail($validated['project_id']);

        ProjectTask::create($validated + [
            'contract_id' => $project->contract_id,
            'status' => 'pending',
            'progress' => 0,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('project-timeline.index')->with('success', 'Task created successfully.');
    }

    public function show(ProjectTask $task)
    {
        $task->load(['contract', 'room', 'scopeType', 'assignee', 'creator']);
        return view('project-timeline.show', compact('task'));
    }

    public function edit(ProjectTask $task)
    {
        return view('projects.tasks.form', [
            'timeline' => true,
            'task' => $task,
            'users' => User::orderBy('name')->get(),
            'rooms' => $task->contract?->rooms()->with('scopeTypes')->get() ?? collect(),
        ]);
    }

    public function update(Request $request, ProjectTask $task)
    {
        $task->update($request->validate(ProjectTask::rules(true)));

        return redirect()->route('project-timeline.index')->with('success', 'Task updated successfully.');
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

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Progress updated successfully'])
            : back()->with('success', 'Progress updated successfully.');
    }

    public function markAsDelayed(ProjectTask $task)
    {
        $task->markAsDelayed();
        return redirect()->back()->with('success', 'Task marked as delayed.');
    }

} 