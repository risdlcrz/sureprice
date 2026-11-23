@extends('layouts.app')

@php
    $pr = $purchaseRequest;
    $user = auth()->user();
    $orders = $pr->purchaseOrder ?? collect();
    $hasPO = $orders->isNotEmpty();
    $rejected = in_array($pr->status, ['rejected', 'cancelled'], true);
    $adminDone = $pr->admin_approved || in_array($pr->status, ['pending_supplier_approval', 'approved'], true);
    $supplierDone = $pr->supplier_approved || $pr->status === 'approved';

    // Approval tracker: [label, state (done|current|failed|todo), date]
    $steps = [
        ['Created', 'done', $pr->created_at],
        ['Admin approval', $adminDone ? 'done' : ($rejected ? 'failed' : 'current'), $pr->admin_approved_at],
        ['Supplier approval', $supplierDone ? 'done' : ($rejected && $adminDone ? 'failed' : ($adminDone ? 'current' : 'todo')), $pr->supplier_approved_at],
        ['Purchase order', $hasPO ? 'done' : ($adminDone && $supplierDone ? 'current' : 'todo'), $orders->first()?->created_at],
    ];
    $stepIcons = ['done' => 'fa-check', 'current' => 'fa-hourglass-half', 'failed' => 'fa-times', 'todo' => ''];

    // Supplier the client picked for each material during the quotation process (cheapest pick wins).
    $quotationRequest = $pr->materialRequest?->quotationRequest;
    $rfqs = $quotationRequest
        ? \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $quotationRequest->request_number . '%')->with('materials')->get()
        : collect();
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Purchase Request" icon="file-invoice">
        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
        @if($pr->status === 'pending')
            <a href="{{ route('purchase-requests.edit', $pr) }}" class="btn btn-outline-secondary"><i class="fas fa-pen me-1"></i> Edit</a>
        @endif
        @if($pr->admin_approved && $pr->supplier_approved && !$hasPO)
            <a href="{{ route('purchase-orders.create', ['purchase_request_id' => $pr->id]) }}" class="btn btn-success">
                <i class="fas fa-file-invoice me-1"></i> Create Purchase Order
            </a>
        @endif
    </x-page-header>

    {{-- Summary --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">{{ $pr->is_project_related ? 'Contract related' : 'Standalone' }} request</div>
                    <h2 class="req-hero__title">{{ $pr->request_number }} <x-status-badge :status="$pr->status" /></h2>
                </div>
                <div class="req-hero__amount">
                    <strong>{{ $pr->total_amount ? '₱' . number_format($pr->total_amount, 2) : '—' }}</strong>
                    <span>{{ $pr->items?->count() ?? 0 }} {{ Str::plural('item', $pr->items?->count() ?? 0) }} · estimated total</span>
                </div>
            </div>

            <ol class="req-steps mb-4" aria-label="Approval progress">
                @foreach($steps as [$label, $state, $date])
                    <li class="req-steps__item is-{{ $state }}" @if($state === 'current') aria-current="step" @endif>
                        <span class="req-steps__dot">@if($stepIcons[$state])<i class="fas {{ $stepIcons[$state] }}" aria-hidden="true"></i>@else{{ $loop->iteration }}@endif</span>
                        <span class="req-steps__label">{{ $label }}</span>
                        <span class="req-steps__date">{{ $date ? $date->format('M d, Y') : ($state === 'failed' ? ucfirst($pr->status) : ($state === 'current' ? 'Waiting' : '')) }}</span>
                    </li>
                @endforeach
            </ol>

            <dl class="req-meta">
                <div>
                    <dt>Requested by</dt>
                    <dd>{{ $pr->requestedBy?->name ?? '—' }}</dd>
                </div>
                @if($pr->is_project_related)
                    <div>
                        <dt>Contract</dt>
                        <dd>
                            @if($pr->contract)
                                <a href="{{ route('contracts.show', $pr->contract) }}">{{ $pr->contract->contract_number }}</a>
                                @if($pr->contract->client)<span class="req-sub">{{ $pr->contract->client->name }}</span>@endif
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                @endif
                @if($pr->materialRequest)
                    <div>
                        <dt>From material request</dt>
                        <dd><a href="{{ route('material-requests.show', $pr->materialRequest) }}">{{ $pr->materialRequest->request_number ?? 'MR-' . $pr->material_request_id }}</a></dd>
                    </div>
                @endif
                @if($quotationRequest)
                    <div>
                        <dt>From quotation request</dt>
                        <dd>
                            @if(in_array($user->role, ['admin', 'manager'], true))
                                <a href="{{ route('admin.quotation.review', $quotationRequest->id) }}">{{ $quotationRequest->request_number }}</a>
                            @else
                                {{ $quotationRequest->request_number }}
                            @endif
                        </dd>
                    </div>
                @endif
                <div>
                    <dt>Created</dt>
                    <dd>{{ $pr->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt>Last updated</dt>
                    <dd>{{ $pr->updated_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>

            @if($pr->notes)
                <div class="mt-4">
                    <div class="form-label">Notes</div>
                    <div class="req-note">{{ $pr->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- What happens next --}}
    @if(($pr->status === 'pending_admin_approval' && $user->role === 'admin') || ($pr->status === 'pending' && ($user->hasRole('procurement') || $user->role === 'admin')))
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                <div><strong>Waiting for approval.</strong><br>Once approved, the request goes to the supplier for their approval.</div>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('purchase-requests.reject', $pr) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Reject this purchase request?')"><i class="fas fa-ban me-1"></i> Reject</button>
                </form>
                <form action="{{ route('purchase-requests.approve', $pr) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this purchase request?')"><i class="fas fa-check me-1"></i> Approve</button>
                </form>
            </div>
        </div>
    @elseif($pr->status === 'pending_supplier_approval')
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-truck" aria-hidden="true"></i>
                <div>
                    <strong>Waiting for the supplier.</strong> The admin approved this request
                    @if($pr->admin_approved_at) on {{ $pr->admin_approved_at->format('M d, Y H:i') }}@endif; the supplier now needs to confirm it.
                </div>
            </div>
            @if($user->hasRole('supplier') && !$pr->supplier_approved && $pr->items->every(fn ($item) => $item->preferred_supplier_id === $user->company?->id))
                <form action="{{ route('purchase-requests.supplier.approve', $pr) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Approve as supplier</button>
                </form>
            @endif
        </div>
    @elseif($pr->status === 'approved')
        <div class="req-callout req-callout--success mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <div>
                    <strong>Fully approved.</strong>
                    @if($pr->admin_approved_at) Admin approved {{ $pr->admin_approved_at->format('M d, Y H:i') }}.@endif
                    @if($pr->supplier_approved_at) Supplier approved {{ $pr->supplier_approved_at->format('M d, Y H:i') }}.@endif
                    @unless($hasPO) A purchase order can now be created.@endunless
                </div>
            </div>
        </div>
    @elseif($rejected)
        <div class="req-callout req-callout--danger mb-4">
            <div class="req-callout__text">
                <i class="fas fa-times-circle" aria-hidden="true"></i>
                <div><strong>This request was {{ $pr->status }}.</strong> No purchase order will be created from it.</div>
            </div>
        </div>
    @endif

    {{-- Items --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Description</th>
                        <th class="req-num">Quantity</th>
                        <th class="req-num">Unit price</th>
                        <th class="req-num">Amount</th>
                        <th>Supplier</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pr->items ?? [] as $item)
                        @php
                            $selectedSupplier = null;
                            $selectedSupplierPrice = null;
                            foreach ($rfqs as $rfq) {
                                $material = $rfq->materials->firstWhere('id', $item->material_id);
                                if ($material && $material->pivot && $material->pivot->selected_supplier_id) {
                                    $unitPrice = $material->pivot->unit_price ?? null;
                                    if (!$selectedSupplier || ($unitPrice !== null && ($selectedSupplierPrice === null || $unitPrice < $selectedSupplierPrice))) {
                                        $selectedSupplier = \App\Models\Supplier::find($material->pivot->selected_supplier_id);
                                        $selectedSupplierPrice = $unitPrice;
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $item->material?->name ?? '—' }}</td>
                            <td class="req-wrap">{{ $item->description ?: '—' }}</td>
                            <td class="req-num">{{ $item->quantity ? number_format($item->quantity, 2) : '—' }} <span class="text-muted">{{ $item->unit }}</span></td>
                            <td class="req-num">{{ $item->estimated_unit_price ? '₱' . number_format($item->estimated_unit_price, 2) : '—' }}</td>
                            <td class="req-num">{{ $item->total_amount ? '₱' . number_format($item->total_amount, 2) : '—' }}</td>
                            <td class="req-wrap">
                                @if($selectedSupplier)
                                    {{ $selectedSupplier->company_name }}
                                    @if($selectedSupplierPrice)<span class="req-sub">Quoted ₱{{ number_format($selectedSupplierPrice, 2) }}</span>@endif
                                @elseif($item->preferred_supplier_id)
                                    {{ $item->preferredSupplier?->company_name ?? optional(\App\Models\Supplier::find($item->preferred_supplier_id))->company_name ?? 'Unknown supplier' }}
                                @else
                                    <span class="badge bg-warning">No supplier selected</span>
                                @endif
                            </td>
                            <td class="req-wrap">{{ $item->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <x-empty-row cols="7">No items on this request.</x-empty-row>
                    @endforelse
                </tbody>
                @if($pr->items?->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="req-num">{{ $pr->total_amount ? '₱' . number_format($pr->total_amount, 2) : '—' }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Purchase orders --}}
    @if($hasPO)
        <div class="card mb-4">
            <div class="card-header"><h3 class="req-section-title"><i class="fas fa-file-invoice-dollar"></i> Purchase orders</h3></div>
            <div class="table-responsive">
                <table class="table req-table">
                    <thead>
                        <tr>
                            <th>PO number</th>
                            <th>Supplier</th>
                            <th class="req-num">Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $po)
                            @php $poPayment = $po->payments->last(); @endphp
                            <tr>
                                <td><a href="{{ route('purchase-orders.show', $po) }}" class="req-ref">{{ $po->po_number }}</a></td>
                                <td>{{ $po->supplier?->company_name ?? '—' }}</td>
                                <td class="req-num">{{ $po->total_amount ? '₱' . number_format($po->total_amount, 2) : '—' }}</td>
                                <td><x-status-badge :status="$po->status" /></td>
                                <td>
                                    @if($poPayment)
                                        <x-status-badge :status="$poPayment->status" />
                                    @else
                                        <span class="badge bg-secondary">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ $po->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="req-actions">
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        @if($user->isAdmin() && (!$poPayment || $poPayment->status === 'rejected'))
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#submitPaymentModal{{ $po->id }}">Pay</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($user->isAdmin())
            @foreach($orders as $po)
                @php $poPayment = $po->payments->last(); @endphp
                @if(!$poPayment || $poPayment->status === 'rejected')
                    <x-modal-dialog id="submitPaymentModal{{ $po->id }}">
                        <x-slot:title>Pay supplier · {{ $po->po_number }}</x-slot:title>
                        <form method="POST" action="{{ route('purchase-orders.payments.store', $po) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <x-field name="amount" id="amount{{ $po->id }}" type="number" label="Amount (₱)" :value="$po->total_amount" col="md-6" required step="0.01" min="0" />
                                    <x-field name="payment_method" id="payment_method{{ $po->id }}" label="Payment method" col="md-6" required>
                                        <select name="payment_method" id="payment_method{{ $po->id }}" class="form-select" required>
                                            <option value="bank_transfer">Bank transfer</option>
                                            <option value="check">Check</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </x-field>
                                    <x-field name="admin_reference_number" id="admin_reference_number{{ $po->id }}" label="Reference number" col="md-6" required />
                                    <x-field name="admin_paid_date" id="admin_paid_date{{ $po->id }}" type="date" label="Date paid" :value="now()->toDateString()" col="md-6" required />
                                    <x-field name="admin_proof" id="admin_proof{{ $po->id }}" type="file" label="Proof of payment" col="md-12" required upload="documents" />
                                    <x-field name="admin_notes" id="admin_notes{{ $po->id }}" type="textarea" label="Notes (optional)" col="md-12" rows="2" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Submit for verification</button>
                            </div>
                        </form>
                    </x-modal-dialog>
                @endif
            @endforeach
        @endif
    @endif
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
