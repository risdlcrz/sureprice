@extends('layouts.app')

@section('content')
{{-- @formatter:off --}}
{{-- stylelint-disable --}}
<div class="container mt-4">
    @if(auth()->user() && auth()->user()->party && auth()->user()->party->banned)
        <div class="alert alert-danger mb-4">
            <strong>You have been banned from the system.</strong>
            @if(auth()->user()->party->ban_reason)
                <br>Reason: {{ auth()->user()->party->ban_reason }}
            @endif
            <br>Please contact support for more information.
        </div>
    @endif
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Contracts</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <div>
            <div class="btn-group me-2">
                <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('contracts.index', ['status' => 'draft']) }}" class="btn btn-outline-secondary {{ request('status') === 'draft' ? 'active' : '' }}">
                    Draft
                </a>
                <a href="{{ route('contracts.index', ['status' => 'approved']) }}" class="btn btn-outline-secondary {{ request('status') === 'approved' ? 'active' : '' }}">
                    Approved
                </a>
                <a href="{{ route('contracts.index', ['status' => 'rejected']) }}" class="btn btn-outline-secondary {{ request('status') === 'rejected' ? 'active' : '' }}">
                    Rejected
                </a>
            </div>
            @if(auth()->user() && auth()->user()->party && auth()->user()->party->banned)
                <button class="btn btn-primary" disabled title="You are banned and cannot create new contracts."><i class="bi bi-plus-lg"></i> New Contract</button>
            @else
                <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> New Contract
                </a>
            @endif
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <x-modal-dialog id="deleteModal">
        <x-slot:title>Delete Contract</x-slot:title>
                <div class="modal-body">
                    Are you sure you want to delete this contract? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
    </x-modal-dialog>

    <!-- Status Update Modal -->
    <x-modal-dialog id="statusModal">
        <x-slot:title>Update Contract Status</x-slot:title>
                <div class="modal-body">
                    <p>Are you sure you want to change the status of this contract?</p>
                    <div class="btn-group w-100 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary status-btn" data-status="draft">Draft</button>
                        <button type="button" class="btn btn-outline-primary status-btn" data-status="active">Active</button>
                        <button type="button" class="btn btn-outline-info status-btn" data-status="partially_paid">Partially Paid</button>
                        <button type="button" class="btn btn-outline-success status-btn" data-status="fully_paid">Fully Paid</button>
                        <button type="button" class="btn btn-outline-danger status-btn" data-status="overdue">Overdue</button>
                        <button type="button" class="btn btn-outline-warning status-btn" data-status="suspended">Suspended</button>
                        <button type="button" class="btn btn-outline-dark status-btn" data-status="terminated">Terminated</button>
                        <button type="button" class="btn btn-outline-secondary status-btn" data-status="expired">Expired</button>
                        <button type="button" class="btn btn-outline-success status-btn" data-status="renewed">Renewed</button>
                    </div>
                </div>
    </x-modal-dialog>

    <div class="card contracts-table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table contracts-table table-hover">
                    <thead>
                        <tr>
                            <th>Contract ID</th>
                            <th>Client</th>
                            <th>Contractor</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Materials Cost</th>
                            <th>Labor Cost</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Signatures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_number }}</td>
                                <td>
                                    {{ $contract->client->name }}
                                    @if($contract->client->company_name)
                                        <br>
                                        <small class="text-muted">{{ $contract->client->company_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $contract->contractor->name }}
                                    @if($contract->contractor->company_name)
                                        <br>
                                        <small class="text-muted">{{ $contract->contractor->company_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $contract->start_date->format('M d, Y') }}</td>
                                <td>{{ $contract->end_date->format('M d, Y') }}</td>
                                <td>₱{{ number_format($contract->total_amount - $contract->labor_cost, 2) }}</td>
                                <td>₱{{ number_format($contract->labor_cost, 2) }}</td>
                                <td>₱{{ number_format($contract->total_amount, 2) }}</td>
                                <td>
                                    <x-status-badge :status="$contract->status" />
                                    @if($contract->payments && $contract->payments->count())
                                        @php
                                            $total = $contract->total_amount;
                                            $paid = $contract->total_paid;
                                            $percent = $total > 0 ? round(($paid / $total) * 100) : 0;
                                            $progressClass = $percent >= 100 ? 'bg-success' : 'bg-info';
                                            $progressStyle = "width: {$percent}%";
                                        @endphp
                                        <div class="progress mt-1">
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" style="{{ $progressStyle }}" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $percent }}%
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($contract->contractor_signature)
                                            <span class="badge bg-success mb-1">
                                                <i class="fas fa-check"></i> Contractor
                                            </span>
                                        @else
                                            <span class="badge bg-warning mb-1">
                                                <i class="fas fa-clock"></i> Contractor
                                            </span>
                                        @endif
                                        @if($contract->client_signature)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Client
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Client
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('contracts.show', $contract->id) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($contract->canBeEdited())
                                            <a href="{{ route('contracts.edit', $contract->id) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    data-contract-id="{{ $contract->id }}"
                                                    onclick="confirmDelete(this.dataset.contractId)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            <span class="badge bg-success" title="Completed contracts cannot be edited or deleted">
                                                <i class="bi bi-lock"></i> Locked
                                            </span>
                                        @endif
                                    </div>
                                    @if($contract->canBeDeleted())
                                        <form id="delete-form-{{ $contract->id }}" 
                                              action="{{ route('contracts.destroy', $contract->id) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-row cols="11">No contracts found.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
@vite('resources/css/pages/admin-contracts-index.css')
@endpush

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { index0_0: {{ Js::from((string) (url('contracts'))) }} });</script>
@vite('resources/js/pages/admin-contracts-index.js')
@endpush
@endsection 