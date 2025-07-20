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
        <div class="row">
            <!-- Row 1: First 3 cards -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.projects.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage9.jpg') }}" class="card-img-top" alt="Project & Procurement" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Project & Procurement</h5>
                    </div>
                </div>
            </div>
            <!-- Project History Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('history.dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/projectdash1.jpg') }}" class="card-img-top" alt="Project History" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Project History</h5>
                    </div>
                </div>
            </div>
            <!-- Inventory Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('inventory.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/historydash3.jpg') }}" class="card-img-top" alt="Inventory" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Inventory</h5>
                    </div>
                </div>
            </div>
            
            <!-- Row 2: Second 3 cards -->
            <!-- Transactions Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('admin.transactions') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage10.png') }}" class="card-img-top" alt="Transactions" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Transactions</h5>
                    </div>
                </div>
            </div>
            <!-- Payments Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('payments.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage1.jpg') }}" class="card-img-top" alt="Payments" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Payments</h5>
                    </div>
                </div>
            </div>
            <!-- Messages Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('messages.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/messages.avif') }}" class="card-img-top" alt="Messages" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title mb-0">Messages</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('styles')
{{-- Extracted to resources/css/manager-dashboard.css --}}
@vite('resources/css/manager-dashboard.css')
<style>
/* Ensure proper 3x2 grid layout */
.row .col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
}

/* Card styling improvements */
.card {
    height: 100%;
    min-height: 280px;
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 1rem;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.15) !important;
}

.card-img-top {
    height: 180px !important;
    object-fit: cover;
    object-position: center;
    border-radius: 1rem 1rem 0 0;
}

.card-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 1.5rem 1rem;
    text-align: center;
}

.card-title {
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
    color: #198754;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card {
        min-height: 250px;
    }
    
    .card-img-top {
        height: 150px !important;
    }
}

/* Ensure proper spacing */
.mb-4 {
    margin-bottom: 1.5rem !important;
}

/* Container improvements */
.container {
    max-width: 1200px;
    padding-left: 2rem;
    padding-right: 2rem;
}

@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endpush
@endsection 