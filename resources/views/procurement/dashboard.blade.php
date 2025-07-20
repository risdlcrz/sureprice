@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Procurement Dashboard</h1>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Active RFQs</h3>
                    <p class="display-6 text-primary mb-0">{{ $recentQuotations->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Active Orders</h3>
                    <p class="display-6 text-info mb-0">{{ $recentPurchaseOrders->whereIn('status', ['approved', 'processing'])->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Total Suppliers</h3>
                    <p class="display-6 text-success mb-0">{{ \App\Models\Supplier::count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-lg w-100 rounded-pill"><i class="bi bi-plus-lg"></i> Create RFQ</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-success btn-lg w-100 rounded-pill"><i class="bi bi-folder2-open"></i> Manage Orders</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-info btn-lg w-100 rounded-pill"><i class="bi bi-people"></i> Supplier Management</a>
        </div>
    </div>
    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Purchase Requests -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Requests</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseRequests as $request)
                        <a href="{{ route('purchase-requests.show', $request->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $request->request_number }}</h6>
                                    <p class="mb-0">Status: <span class="badge bg-{{ $request->status_color }}">{{ ucfirst($request->status) }}</span></p>
                                    <p class="mb-0">Total: ₱{{ number_format($request->total_amount, 2) }}</p>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted"><small>{{ $request->created_at->diffForHumans() }}</small></div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0">No recent purchase requests</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <!-- Recent Purchase Orders -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseOrders as $order)
                        <a href="{{ route('purchase-orders.show', $order->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $order->po_number }}</h6>
                                    <p class="mb-0">Supplier: {{ $order->supplier->name }}</p>
                                    <p class="mb-0">Status: <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span></p>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted"><small>{{ $order->created_at->diffForHumans() }}</small></div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0">No recent purchase orders</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Access Cards -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Quick Access</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.project-dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage9.jpg') }}" class="card-img-top flex-grow-1" alt="Project & Procurement" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project & Procurement</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.project-history') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/projectdash1.jpg') }}" class="card-img-top flex-grow-1" alt="Project History" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Project History</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.inventory-dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/historydash3.jpg') }}" class="card-img-top flex-grow-1" alt="Inventory" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Inventory</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.analytics-dashboard') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage10.png') }}" class="card-img-top flex-grow-1" alt="Analytics" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Analytics</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm d-flex flex-column" onclick="window.location.href = '{{ route('procurement.logs') }}';" style="cursor:pointer;">
                    <img src="{{ asset('images/ppimage1.jpg') }}" class="card-img-top flex-grow-1" alt="Logs" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body text-center">
                        <h5 class="card-title">Logs</h5>
                    </div>
                </div>
            </div>
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
    @vite(['resources/css/procurement/dashboard.css'])
@endpush
@endsection 