@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Manager Dashboard</h1>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Total Projects</h3>
                    <p class="display-6 text-primary mb-0">{{ $projects->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">In Progress</h3>
                    <p class="display-6 text-info mb-0">{{ $projects->where('status', 'in_progress')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Completed</h3>
                    <p class="display-6 text-success mb-0">{{ $projects->where('status', 'completed')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-lg w-100 rounded-pill"><i class="bi bi-plus-lg"></i> New Project</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-success btn-lg w-100 rounded-pill"><i class="bi bi-folder2-open"></i> All Projects</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('project-timeline.index') }}" class="btn btn-outline-info btn-lg w-100 rounded-pill"><i class="bi bi-calendar-range"></i> Project Timeline</a>
        </div>
    </div>
    @if(auth()->user()->hasRole('admin'))
        {{-- Place admin-only actions here. If any approval/reject/draft buttons are ever added, they will only show for admins. --}}
    @endif
    <!-- Recent Projects -->
    @php $recentProjects = $projects->sortByDesc('created_at')->take(5); @endphp
    @if($recentProjects->count())
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Recent Projects</div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush">
                    @foreach($recentProjects as $project)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $project->name }}</span>
                            <span class="text-muted small">{{ $project->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(isset($projects) && $projects->count())
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Project Progress</div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush">
                    @foreach($projects as $project)
                        @php
                            $now = \Carbon\Carbon::now();
                            $start = \Carbon\Carbon::parse($project->start_date);
                            $end = \Carbon\Carbon::parse($project->end_date);
                            $totalDays = $start->diffInDays($end) ?: 1;
                            $elapsedDays = $start->diffInDays(min($now, $end));
                            $progressPercent = min(100, round(($elapsedDays / $totalDays) * 100));
                        @endphp
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $project->name }}</span>
                                <span class="ms-3">{{ $progressPercent }}% ({{ $elapsedDays }} of {{ $totalDays }} days)</span>
                            </div>
                            <div class="progress mt-2" style="height: 16px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $progressPercent }}%
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Quick Access - full-width, well positioned -->
    <section class="mb-5">
        <h2 class="mb-3 fw-semibold" style="color:#198754;font-size:1.25rem;">Quick Access</h2>
        <div class="card manager-quick-access-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 manager-quick-access-table w-100">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Module</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr onclick="window.location.href='{{ route('admin.project') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-tasks text-primary"></i></td>
                                <td>Project & Procurement</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('history.dashboard') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-history text-success"></i></td>
                                <td>Project History</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('inventory.index') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-boxes text-info"></i></td>
                                <td>Inventory</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('admin.transactions') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-exchange-alt text-primary"></i></td>
                                <td>Transactions</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('payments.index') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-money-check-alt text-success"></i></td>
                                <td>Payments</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('messages.index') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-comments text-info"></i></td>
                                <td>Messages</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                            <tr onclick="window.location.href='{{ route('admin.feedback.analytics') }}';" style="cursor:pointer;">
                                <td><i class="fas fa-chart-line text-primary"></i></td>
                                <td>Client Feedback Analytics</td>
                                <td class="text-end"><i class="fas fa-chevron-right text-muted small"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@push('styles')
    @vite(['resources/css/manager-dashboard.css'])
@endpush
@endsection 