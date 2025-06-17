@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Respond to Quotation</h4>
                </div>
                <div class="card-body">
                    @if($existingResponse)
                    <div class="alert alert-info">
                        <h5>You have already submitted a response for this quotation.</h5>
                        <p>Your current status: <strong>{{ ucfirst($existingResponse->status) }}</strong></p>
                        <p>Total Quoted Amount: <strong>₱{{ number_format($existingResponse->total_amount, 2) }}</strong></p>
                        <p>Notes: {{ $existingResponse->notes ?? 'N/A' }}</p>
                        <p>You can re-submit your response below if needed.</p>
                    </div>
                    @endif

                    <form action="{{ route('supplier.quotations.respond', $quotation) }}" method="POST">
                        @csrf

                        <h5>Quoted Materials</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Requested Quantity</th>
                                        <th>Current Material Price</th>
                                        <th>Your Quoted Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materialsInQuotation as $material)
                                    <tr>
                                        <td>{{ $material->name }} ({{ $material->code }})</td>
                                        <td>{{ $material->requested_quantity }} {{ $material->unit }}</td>
                                        <td>₱{{ number_format($material->price, 2) }}</td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm" 
                                                   name="materials[{{ $material->id }}][unit_price]" 
                                                   value="{{ old('materials.'.$material->id.'.unit_price', $existingResponse ? ($existingResponse->items->where('material_id', $material->id)->first()->unit_price ?? $material->price) : $material->price) }}" 
                                                   min="0" step="0.01" required>
                                            <input type="hidden" name="materials[{{ $material->id }}][quantity]" value="{{ $material->requested_quantity }}">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No materials requested for this quotation.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $existingResponse->notes ?? '') }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Submit Response</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 