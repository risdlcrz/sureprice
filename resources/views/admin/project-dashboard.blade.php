@extends('layouts.app')

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
</style>
@endpush

@section('content')
    <div class="sidebar">
    @include('include.header_project')
    </div>

    <div class="content">
    <h1 class="text-center my-4">Project Dashboard</h1>

    <div class="container-fluid">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-contract.jpg') }}" class="card-img-top" alt="Create Contract">
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
                    <img src="{{ asset('images/view-contracts.jpg') }}" class="card-img-top" alt="View Contracts">
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
                    <img src="{{ asset('images/timeline.jpg') }}" class="card-img-top" alt="Project Timeline">
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
                        <a href="{{ route('project.timeline') }}" class="btn btn-info w-100">View Timeline</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Modal -->
        <div class="modal fade" id="timelineModal" tabindex="-1" aria-labelledby="timelineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timelineModalLabel">Project Timeline</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="project-search">
                            <input type="text" class="form-control" id="projectSearch" placeholder="Search for a project...">
                        </div>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
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
                                <small class="text-muted">Budget: ₱{{ number_format($contract->budget_allocation, 2) }}</small>
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
        </div>
    </div>
    </div>

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
            <strong>Budget:</strong> ₱${new Intl.NumberFormat().format(contract.extendedProps.budget)}
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
