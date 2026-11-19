{{-- Project & Procurement dashboard (admin/manager and procurement). Data: App\Support\DashboardStats.
     $showProjects adds the Project Management section (contracts, projects, timeline). --}}
@extends('layouts.app')

@section('title', 'Project & Procurement')

@php
    // [title, description, image, [[label, route, params, style], ...]]
    $projectCards = [
        ['Create Contract', 'Start a new contract and set up initial terms and conditions.', 'ppimage1.jpg', [['Create New Contract', 'contracts.create', [], 'primary']]],
        ['View Contracts', 'Access and manage existing contracts, track status and approvals.', 'ppimage2.jpg', [['View All Contracts', 'contracts.index', [], 'secondary']]],
        ['View Projects', 'Browse and manage all projects created from contracts.', 'projectdash1.jpg', [['View All Projects', 'projects.index', [], 'success']]],
        ['Project Timeline', 'Visualize project schedules, milestones, and deadlines.', 'ppimage3.jpg', [['View Timeline', 'project-timeline.index', [], 'outline-success']]],
    ];
    $procurementCards = [
        ['Purchase Requests', 'Create and manage purchase requests for materials and supplies.', 'ppimage4.jpg', [['+ New Request', 'purchase-requests.create', [], 'primary'], ['View All', 'purchase-requests.index', [], 'outline-secondary']]],
        ['Purchase Orders', 'Create and manage purchase orders from approved purchase requests.', 'ppimage5.jpg', [['+ New Order', 'purchase-orders.create', [], 'primary'], ['View All', 'purchase-orders.index', [], 'outline-secondary']]],
        ['Inquiries', 'Submit and track material inquiries and procurement requests.', 'ppimage7.jpg', [['+ New Inquiry', 'inquiries.create', [], 'primary'], ['View All', 'inquiries.index', [], 'outline-secondary']]],
        ['Quotation Management', 'Create and manage RFQs, compare supplier quotations, and track responses.', 'ppimage8.jpg', [['+ New RFQ', 'quotations.create', [], 'primary'], ['View All', 'quotations.index', [], 'outline-secondary']]],
        ['Materials Management', 'Manage materials inventory, specifications, and pricing information.', 'ppimage9.jpg', [['+ New Material', 'materials.create', [], 'primary'], ['View All', 'materials.index', [], 'outline-secondary']]],
        ['Supplier Management', 'Manage supplier information, relationships, performance tracking, and send invitations to new suppliers.', 'ppimage10.png', [['Invite Supplier', 'supplier-invitations.create', [], 'primary'], ['View Invitations', 'supplier-invitations.index', [], 'outline-secondary'], ['View Suppliers', 'information-management.index', ['type' => 'company'], 'outline-primary']]],
        ['Material Requests', 'Request materials, check inventory, and trigger procurement if needed.', 'ppimage6.jpg', [['View All (' . $pendingMaterialRequests . ' pending)', 'material-requests.index', [], 'outline-danger']]],
    ];
    $sections = array_filter(['Project Management' => $showProjects ? $projectCards : null, 'Procurement Management' => $procurementCards]);
    $recent = [
        'Recent Contracts' => [$recentContracts, fn ($c) => ($c->contract_number ?? 'Contract #' . $c->id) . ' · ₱' . number_format($c->total_amount, 2)],
        'Recent Purchase Orders' => [$recentPurchaseOrders, fn ($o) => 'PO ' . $o->po_number],
        'Recent Purchase Requests' => [$recentPurchaseRequests, fn ($r) => 'PR ' . $r->request_number],
    ];
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header title="Project & Procurement Dashboard" icon="tasks" />

    @foreach($sections as $heading => $cards)
        <section class="mb-5">
            <h2 class="h5 mb-3">{{ $heading }}</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($cards as [$title, $text, $image, $actions])
                    <div class="col">
                        <div class="card h-100 dashboard-tile">
                            <img src="{{ asset('images/' . $image) }}" class="card-img-top" alt="{{ $title }}"
                                 onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                            <div class="card-body">
                                <h5 class="card-title">{{ $title }}</h5>
                                <p class="card-text text-muted small mb-0">{{ $text }}</p>
                            </div>
                            <div class="card-footer d-flex flex-wrap gap-2">
                                @foreach($actions as [$label, $route, $params, $style])
                                    <a href="{{ route($route, $params) }}" class="btn btn-{{ $style }} btn-sm flex-grow-1">{{ $label }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <section class="mb-5">
        <h2 class="h5 mb-3">Quick Stats</h2>
        <div class="row g-4">
            @foreach([
                ['Total Budget', '₱' . number_format($totalBudget, 2), 'primary'],
                ['Committed to Suppliers', '₱' . number_format($totalSpent, 2), 'success'],
                ['Contracts', number_format($contractCount), 'info'],
                ['Purchase Orders', number_format($purchaseOrderCount), 'warning'],
            ] as [$label, $value, $tone])
                <div class="col-md-3 col-6">
                    <div class="card text-center p-3">
                        <div class="fw-bold fs-4 text-{{ $tone }}">{{ $value }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mb-5">
        <h2 class="h5 mb-3">Recent Activities</h2>
        <div class="row g-4">
            @foreach($recent as $heading => [$items, $describe])
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">{{ $heading }}</div>
                        <ul class="list-group list-group-flush">
                            @forelse($items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $describe($item) }}</span>
                                    <span class="text-muted small">{{ $item->created_at?->diffForHumans() }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nothing yet</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/projects-dashboard.css')
@endpush
