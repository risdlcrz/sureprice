@extends('layouts.app')

@php
    $mr = $materialRequest;
    $items = collect($mr->items);
    $grouped = $items->groupBy(fn ($item) => ($item->material->name ?? 'Unknown material') . '|' . $item->unit);
    $totalRequested = $items->sum('quantity');
    $totalFulfilled = $items->sum('fulfilled_quantity');
    $fulfilledPct = $totalRequested > 0 ? min(100, round($totalFulfilled / $totalRequested * 100)) : 0;

    // Supplier for an item: its own supplier, else the client's pick on the quotation / contract.
    $quotationRequest = $mr->quotationRequest ?? $mr->contract?->quotationRequest;
    $selectedSuppliers = $quotationRequest?->selected_suppliers ?? [];
    $rfqs = null;
    $supplierFor = function ($item) use ($mr, $quotationRequest, $selectedSuppliers, &$rfqs) {
        if ($item->supplier) {
            return $item->supplier->company_name;
        }
        if (!$quotationRequest) {
            return null;
        }
        $id = $selectedSuppliers[$item->material_id] ?? $selectedSuppliers[(string) $item->material_id] ?? null;
        if ($id && ($supplier = \App\Models\Supplier::find($id))) {
            return $supplier->company_name;
        }
        if ($mr->quotationRequest) { // RFQ pivot fallback (only for requests raised from a quotation)
            $rfqs ??= \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $quotationRequest->request_number . '%')->with(['materials', 'suppliers'])->get();
            foreach ($rfqs as $rfq) {
                $mat = $rfq->materials->firstWhere('id', $item->material_id);
                if ($mat?->pivot?->selected_supplier_id && ($supplier = $rfq->suppliers->firstWhere('id', $mat->pivot->selected_supplier_id))) {
                    return $supplier->company_name;
                }
            }
        }

        return null;
    };
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Material Request" icon="dolly">
        <a href="{{ route('material-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Material request</div>
                    <h2 class="req-hero__title">MR-{{ $mr->id }} <x-status-badge :status="$mr->status" /></h2>
                </div>
                <div class="req-hero__amount req-hero__progress">
                    <x-progress :value="$fulfilledPct" height="10" />
                    <span>{{ $fulfilledPct }}% filled from stock</span>
                </div>
            </div>

            <dl class="req-meta">
                <div><dt>Requested by</dt><dd>{{ $mr->requestedBy?->name ?? '—' }}</dd></div>
                @if($mr->contract)
                    <div>
                        <dt>Contract</dt>
                        <dd><a href="{{ route('contracts.show', $mr->contract) }}">{{ $mr->contract->contract_number }}</a><span class="req-sub">{{ $mr->contract->title }}</span></dd>
                    </div>
                @endif
                @if($mr->quotationRequest)
                    <div>
                        <dt>From quotation request</dt>
                        <dd>
                            @if(in_array(auth()->user()->role, ['admin', 'manager'], true))
                                <a href="{{ route('admin.quotation.review', $mr->quotationRequest->id) }}">{{ $mr->quotationRequest->request_number }}</a>
                            @else
                                {{ $mr->quotationRequest->request_number }}
                            @endif
                        </dd>
                    </div>
                @endif
                <div><dt>Created</dt><dd>{{ $mr->created_at->format('M d, Y H:i') }}</dd></div>
                @if($purchaseRequest)
                    <div>
                        <dt>Purchase request</dt>
                        <dd><a href="{{ route('purchase-requests.show', $purchaseRequest) }}">{{ $purchaseRequest->request_number }}</a><span class="req-sub">for items short in stock</span></dd>
                    </div>
                @endif
            </dl>

            @if($mr->notes)
                <div class="mt-4">
                    <div class="form-label">Notes</div>
                    <div class="req-note">{{ $mr->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    @if($purchaseRequest)
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                <div><strong>Some items weren't in stock.</strong> Purchase request {{ $purchaseRequest->request_number }} was created for the shortfall.</div>
            </div>
            <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-outline-secondary">View purchase request</a>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Material / supplier</th>
                        <th class="req-num">Requested</th>
                        <th class="req-num">From stock</th>
                        <th class="req-num">To purchase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grouped as $key => $rows)
                        @php
                            [$matName, $unit] = explode('|', $key);
                            $requested = $rows->sum('quantity');
                            $fulfilled = $rows->sum('fulfilled_quantity');
                        @endphp
                        <tr class="req-group-row">
                            <td colspan="4">{{ $matName }} <span class="fw-normal text-muted">· {{ $unit }}</span></td>
                        </tr>
                        @foreach($rows as $item)
                            @php $supplierName = $supplierFor($item); $short = $item->quantity - $item->fulfilled_quantity; @endphp
                            <tr>
                                <td class="req-indent">
                                    @if($supplierName)
                                        <span class="text-body fw-semibold">{{ $supplierName }}</span> <span class="req-sub d-inline">· client's choice</span>
                                    @else
                                        <span class="badge bg-warning">No supplier</span>
                                    @endif
                                </td>
                                <td class="req-num">{{ number_format($item->quantity, 2) }}</td>
                                <td class="req-num">{{ number_format($item->fulfilled_quantity, 2) }}</td>
                                <td class="req-num">
                                    @if($short > 0)
                                        <span class="badge bg-warning">{{ number_format($short, 2) }}</span>
                                    @else
                                        <span class="text-muted">0.00</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($rows->count() > 1)
                            <tr class="req-subtotal-row">
                                <td class="text-end">Subtotal</td>
                                <td class="req-num">{{ number_format($requested, 2) }} {{ $unit }}</td>
                                <td class="req-num">{{ number_format($fulfilled, 2) }} {{ $unit }}</td>
                                <td class="req-num">{{ number_format($requested - $fulfilled, 2) }} {{ $unit }}</td>
                            </tr>
                        @endif
                    @empty
                        <x-empty-row cols="4">No items on this request.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
