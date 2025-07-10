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

    <!-- Quick Access Cards Based on Sidebar (with Images) -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Quick Access</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Dashboard Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('manager.dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard1.jpg') }}" class="card-img-top flex-grow-1" alt="Dashboard" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Dashboard</h5>
                    </div>
                </div>
            </div>
            <!-- Project & Procurement Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('projects.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard2.jpg') }}" class="card-img-top flex-grow-1" alt="Project & Procurement" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project & Procurement</h5>
                    </div>
                </div>
            </div>
            <!-- Project History Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '/history-dashboard';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard3.jpg') }}" class="card-img-top flex-grow-1" alt="Project History" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project History</h5>
                    </div>
                </div>
            </div>
            <!-- Inventory Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('materials.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard4.jpg') }}" class="card-img-top flex-grow-1" alt="Inventory" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Inventory</h5>
                    </div>
                </div>
            </div>
            <!-- Transactions Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '/transactions';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard5.jpg') }}" class="card-img-top flex-grow-1" alt="Transactions" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transactions</h5>
                    </div>
                </div>
            </div>
            <!-- Payments Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '/payments';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard6.jpg') }}" class="card-img-top flex-grow-1" alt="Payments" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Payments</h5>
                    </div>
                </div>
            </div>
            <!-- Messages Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '/messages';" style="cursor:pointer;">
                    <img src="{{ asset('images/dashboard7.jpg') }}" class="card-img-top flex-grow-1" alt="Messages" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Messages</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
.hover-shadow:hover {
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.15) !important;
    transform: translateY(-4px) scale(1.02);
    transition: box-shadow 0.2s, transform 0.2s;
}
</style>
@endsection 