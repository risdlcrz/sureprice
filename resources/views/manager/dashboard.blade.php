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
            <!-- Project & Procurement Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('admin.project') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage9.jpg') }}" class="card-img-top flex-grow-1" alt="Project & Procurement" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project & Procurement</h5>
                    </div>
                </div>
            </div>
            <!-- Project History Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('history.dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/projectdash1.jpg') }}" class="card-img-top flex-grow-1" alt="Project History" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project History</h5>
                    </div>
                </div>
            </div>
            <!-- Inventory Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('inventory.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/historydash3.jpg') }}" class="card-img-top flex-grow-1" alt="Inventory" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Inventory</h5>
                    </div>
                </div>
            </div>
            <!-- Transactions Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('admin.transactions') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage10.png') }}" class="card-img-top flex-grow-1" alt="Transactions" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transactions</h5>
                    </div>
                </div>
            </div>
            <!-- Payments Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('payments.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage1.jpg') }}" class="card-img-top flex-grow-1" alt="Payments" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Payments</h5>
                    </div>
                </div>
            </div>
            <!-- Messages Card -->
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('messages.index') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/messages.avif') }}" class="card-img-top flex-grow-1" alt="Messages" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Messages</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card, .quick-access-card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s, transform 0.2s;
}
.card:hover, .quick-access-card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
    transform: translateY(-4px) scale(1.02);
}
.card-img-top {
    border-radius: 1.25rem 1.25rem 0 0;
    object-fit: cover;
    transition: box-shadow 0.2s, transform 0.2s;
}
.card-body {
    padding: 1.5rem 1.2rem;
}
.btn-primary, .btn-outline-success, .btn-outline-info {
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.7em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #2196f322;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #21cbf3 0%, #2196f3 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #2196f344;
}
.btn-outline-success {
    border: 2px solid #43e97b;
    color: #43e97b;
    background: #fff;
}
.btn-outline-success:hover {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%) !important;
    color: #fff !important;
    border: none;
    box-shadow: 0 4px 16px #43e97b44;
}
.btn-outline-info {
    border: 2px solid #21cbf3;
    color: #21cbf3;
    background: #fff;
}
.btn-outline-info:hover {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
    box-shadow: 0 4px 16px #2196f344;
}
</style>
@endpush
@endsection 