@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Purchase Requests" icon="file-invoice" subtitle="Requests for materials that need to be bought from suppliers.">
        <a href="{{ route('purchase-requests.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> New Purchase Request
        </a>
    </x-page-header>

    <x-requests.kpis :filters="App\Models\PurchaseRequest::STATUS_FILTERS" :counts="$counts" route="purchase-requests.index" />

    <div class="card">
        <x-requests.toolbar route="purchase-requests.index" placeholder="Search by request #, contract or requester"
            :pills="['type' => ['project' => 'Contract related', 'standalone' => 'Standalone']]" :pill-counts="$typeCounts" />

        <div class="table-responsive">
            <table class="table table-hover req-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Contract</th>
                        <th>Requested by</th>
                        <th class="req-num">Items</th>
                        <th class="req-num">Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $canDecide = auth()->user()->hasRole('procurement') || auth()->user()->role === 'admin'; @endphp
                    @forelse($purchaseRequests as $pr)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-requests.show', $pr) }}" class="req-ref">{{ $pr->request_number }}</a>
                                <span class="req-sub">{{ $pr->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="req-wrap">
                                @if($pr->is_project_related && $pr->contract)
                                    <a href="{{ route('contracts.show', $pr->contract) }}">{{ $pr->contract->contract_number ?? 'Contract' }}</a>
                                    <span class="req-sub">{{ $pr->contract->title ?? $pr->contract->name ?? '' }}</span>
                                @elseif($pr->is_project_related)
                                    <span class="text-muted">Contract related</span>
                                @else
                                    <span class="badge bg-light">Standalone</span>
                                @endif
                            </td>
                            <td>{{ $pr->requestedBy?->name ?? '—' }}</td>
                            <td class="req-num">{{ $pr->items_count }}</td>
                            <td class="req-num">₱{{ number_format($pr->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$pr->status" /></td>
                            <td class="text-end">
                                <div class="req-actions">
                                    <a href="{{ route('purchase-requests.show', $pr) }}" class="btn btn-sm btn-outline-secondary req-icon-btn" title="View" aria-label="View {{ $pr->request_number }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($pr->status === 'pending')
                                        <a href="{{ route('purchase-requests.edit', $pr) }}" class="btn btn-sm btn-outline-secondary req-icon-btn" title="Edit" aria-label="Edit {{ $pr->request_number }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @if($canDecide)
                                            <form action="{{ route('purchase-requests.approve', $pr) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success req-icon-btn" title="Approve" aria-label="Approve {{ $pr->request_number }}"
                                                    onclick="return confirm('Approve purchase request {{ $pr->request_number }}?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('purchase-requests.reject', $pr) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning req-icon-btn" title="Reject" aria-label="Reject {{ $pr->request_number }}"
                                                    onclick="return confirm('Reject purchase request {{ $pr->request_number }}?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('purchase-requests.destroy', $pr) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger req-icon-btn" title="Delete" aria-label="Delete {{ $pr->request_number }}"
                                                onclick="return confirm('Delete purchase request {{ $pr->request_number }}? This cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="7">No purchase requests match these filters.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchaseRequests->hasPages())
            <div class="req-footer">{{ $purchaseRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
