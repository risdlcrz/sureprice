@extends('layouts.app')



@section('content')
    <h1 class="text-center my-4">Project & Procurement Dashboard</h1>

    <div class="container-fluid ">
        <!-- Project Management Section -->
        <h2 class="mb-4">Project Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage1.jpg') }}" class="card-img-top" alt="Create Contract">
                    <div class="card-body">
                        <h5 class="card-title">Create Contract</h5>
                        <p class="card-text">Start a new contract and set up initial terms and conditions.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100">Create New Contract</a>
                    </div>
                </div>
            </div>

            <!-- View Contracts Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage2.jpg') }}" class="card-img-top" alt="View Contracts">
                    <div class="card-body">
                        <h5 class="card-title">View Contracts</h5>
                        <p class="card-text">Access and manage existing contracts, track status and approvals.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>

            <!-- Project Timeline Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage3.jpg') }}" class="card-img-top" alt="Project Timeline">
                    <div class="card-body">
                        <h5 class="card-title">Project Timeline</h5>
                        <p class="card-text">View and manage project schedules and timelines.</p>
                        
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="contractSearch" 
                                   placeholder="Search contracts..."
                                   autocomplete="off">
                            <div id="contractSearchResults" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>

                        <div id="selectedContract" class="mb-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2" id="contractTitle"></h6>
                                    <p class="card-text" id="contractDetails"></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge" id="contractStatus"></span>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                                            Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-info w-100">View Timeline</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procurement Section -->
        <h2 class="mt-5 mb-4">Procurement Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Purchase Requests Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage4.jpg') }}" class="card-img-top" alt="Purchase Requests">
                    <div class="card-body">
                        <h5 class="card-title">Purchase Requests</h5>
                        <p class="card-text">Create and manage purchase requests for materials and supplies.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Request
                        </a>
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage5.jpg') }}" class="card-img-top" alt="Purchase Orders">
                    <div class="card-body">
                        <h5 class="card-title">Purchase Orders</h5>
                        <p class="card-text">Create and manage purchase orders from approved purchase requests.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Order
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inquiries Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('Images/ppimage6.jpg') }}" class="card-img-top" alt="Inquiries">
                    <div class="card-body">
                        <h5 class="card-title">Inquiries</h5>
                        <p class="card-text">Submit and track material inquiries and procurement requests.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('inquiries.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Inquiry
                        </a>
                        <a href="{{ route('inquiries.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Supplier Invitations Card -->
            <!-- Removed the entire card block for Supplier Invitations -->

            <!-- Quotation Management Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage8.svg') }}" class="card-img-top" alt="Quotation Management">
                    <div class="card-body">
                        <h5 class="card-title">Quotation Management</h5>
                        <p class="card-text">Create and manage RFQs, compare supplier quotations, and track responses.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New RFQ
                        </a>
                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Materials Management Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage9.svg') }}" class="card-img-top" alt="Materials Management">
                    <div class="card-body">
                        <h5 class="card-title">Materials Management</h5>
                        <p class="card-text">Manage materials inventory, specifications, and pricing information.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('materials.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Material
                        </a>
                        <a href="{{ route('materials.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Suppliers Management Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage10.png') }}" class="card-img-top" alt="Suppliers">
                    <div class="card-body">
                        <h5 class="card-title">Suppliers Management</h5>
                        <p class="card-text">Manage supplier information, relationships, performance tracking, and send invitations to new suppliers.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-paper-plane"></i> Invite Supplier
                        </a>
                        <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View Invitations
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-user-plus"></i> Sign Up as Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="mb-4">Recent Activities</h2>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Contracts</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentContracts ?? [] as $contract)
                            <a href="{{ route('contracts.show', $contract->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $contract->project_name }}</h6>
                                    <small>{{ $contract->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $contract->client_name }}</p>
                                <small class="text-muted">Total Amount: ₱{{ number_format($contract->total_amount, 2) }}</small>
                            </a>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent contracts</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentPurchaseOrders ?? [] as $purchaseOrder)
                            <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $purchaseOrder->po_number }}</h6>
                                    <small>{{ $purchaseOrder->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    Supplier: {{ $purchaseOrder->supplier->name }}<br>
                                    Status: <span class="badge bg-{{ $purchaseOrder->status_color }}">{{ ucfirst($purchaseOrder->status) }}</span>
                                </p>
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

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Purchase Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentPurchaseRequests ?? [] as $pr)
                            <a href="{{ route('purchase-requests.show', $pr->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $pr->pr_number }}</h6>
                                    <small>{{ $pr->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    Department: {{ $pr->department }}<br>
                                    Status: <span class="badge bg-{{ $pr->status_color }}">{{ ucfirst($pr->status) }}</span>
                                </p>
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
        </div>
    </div>

    @push('styles')
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css' rel='stylesheet' />
<style>
.progress {
    border-radius: 0.5rem;
}

.progress-bar {
    font-size: 0.9rem;
    font-weight: 500;
}

.calendar-card {
    min-height: 600px;
}

#calendar {
    height: 100%;
}

.project-search {
    margin-bottom: 1rem;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
    cursor: pointer;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}
.card-img-top {
    height: 200px;
    object-fit: cover;
}

.card-body {
    padding: 1.5rem;
 }

.card-footer {
    background: none;
    border-top: none;
    padding: 1rem;
}

.card-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0;
    text-align: center;
}

.btn {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.list-group-item {
    transition: background-color 0.2s;
}
.list-group-item:hover {
    background-color: rgba(0,0,0,0.02);
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('contractSearch');
    const searchResults = document.getElementById('contractSearchResults');
    const selectedContract = document.getElementById('selectedContract');
    const contractTitle = document.getElementById('contractTitle');
    const contractDetails = document.getElementById('contractDetails');
    const contractStatus = document.getElementById('contractStatus');
    let contracts = [];

    // Fetch contracts when the page loads
    fetch('/api/contracts/timeline')
        .then(response => response.json())
        .then(data => {
            contracts = data;
        })
        .catch(error => console.error('Error:', error));

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        const filteredContracts = contracts.filter(contract => {
            const searchableText = [
                contract.title,
                contract.extendedProps.client,
                contract.extendedProps.contractor,
                contract.extendedProps.scope
            ].join(' ').toLowerCase();
            return searchableText.includes(searchTerm);
        });

        searchResults.innerHTML = '';
        filteredContracts.forEach(contract => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action';
            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${contract.title}</h6>
                    <small>${formatDate(contract.start)}</small>
                </div>
                <p class="mb-1">${contract.extendedProps.scope.substring(0, 100)}...</p>
                <small>Client: ${contract.extendedProps.client}</small>
            `;
            item.addEventListener('click', (e) => {
                e.preventDefault();
                selectContract(contract);
            });
            searchResults.appendChild(item);
        });

        searchResults.style.display = filteredContracts.length > 0 ? 'block' : 'none';
    });

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    function selectContract(contract) {
        contractTitle.textContent = contract.title;
        contractDetails.innerHTML = `
            <strong>Start:</strong> ${formatDate(contract.start)}<br>
            <strong>End:</strong> ${formatDate(contract.end)}<br>
            <strong>Client:</strong> ${contract.extendedProps.client}<br>
            <strong>Total Amount:</strong> ₱${new Intl.NumberFormat().format(contract.extendedProps.total_amount)}
        `;
        
        contractStatus.textContent = contract.extendedProps.status.toUpperCase();
        contractStatus.className = `badge bg-${getStatusColor(contract.extendedProps.status)}`;
        
        selectedContract.style.display = 'block';
        searchResults.style.display = 'none';
        searchInput.value = '';
    }

    function getStatusColor(status) {
        switch(status) {
            case 'draft': return 'warning';
            case 'approved': return 'success';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
});

function clearSelection() {
    document.getElementById('selectedContract').style.display = 'none';
    document.getElementById('contractSearch').value = '';
}
</script>
@endpush
@endsection
