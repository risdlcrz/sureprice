{{-- Project task table. Params: $project, $tasks, $actions (bool: link + view/edit buttons), $empty (text) --}}
<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Task</th><th>Assigned To</th><th>Due Date</th><th>Status</th><th>Progress</th>
                @if($actions)<th>Actions</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>
                        @if($actions)
                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-decoration-none">{{ $task->name }}</a>
                        @else
                            {{ $task->name }}
                        @endif
                        @if(in_array($task->priority, ['high', 'urgent']))
                            <x-status-badge :status="$task->priority" kind="priority" />
                        @endif
                    </td>
                    <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                    <td class="text-nowrap">
                        {{ $task->due_date?->format('M d, Y') ?? '-' }}
                        @if($task->isOverdue())<span class="badge bg-danger">Overdue</span>@endif
                    </td>
                    <td><x-status-badge :status="$task->status" /></td>
                    <td style="min-width: 120px"><x-progress :value="$task->progress" /></td>
                    @if($actions)
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <x-empty-row :cols="$actions ? 6 : 5">{{ $empty }}</x-empty-row>
            @endforelse
        </tbody>
    </table>
</div>
