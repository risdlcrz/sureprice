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
                    <img src="{{ asset('resources/images/ppimage1.svg') }}" class="card-img-top" alt="Create Contract">
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
                    <img src="{{ asset('resources/images/ppimage2.svg') }}" class="card-img-top" alt="View Contracts">
                    <div class="card-body">
                        <h5 class="card-title">View Contracts</h5>
                        <p class="card-text">Access and manage your contracts, track status.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>

            <!-- Project Timeline Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('resources/images/ppimage3.svg') }}" class="card-img-top" alt="Project Timeline">
                    <div class="card-body">
                        <h5 class="card-title">Project Timeline</h5>
                        <p class="card-text">View and manage project schedules and timelines.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="contractSearch" placeholder="Search contracts..." autocomplete="off">
                            <div id="contractSearchResults" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                        </div>
                        <div id="selectedContract" class="mb-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2" id="contractTitle"></h6>
                                    <p class="card-text" id="contractDetails"></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge" id="contractStatus"></span>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Clear</button>
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
    </div>
@endsection 