@extends('layouts.app')

@push('styles')
<style>
.quotation-card {
    max-width: 800px;
    margin: 40px auto;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    background: #fff;
    padding: 32px 24px 24px 24px;
}
.quotation-card h2 {
    font-weight: 700;
    color: #198754;
}
.quotation-card .lead {
    font-size: 1.15rem;
    color: #444;
}
.quotation-card .badge.bg-primary {
    font-size: 1rem;
    padding: 0.5em 1em;
    border-radius: 8px;
}
.quotation-card .badge.bg-warning {
    color: #fff;
    background: #f59e42;
}
.quotation-card .table {
    margin-top: 24px;
    border-radius: 10px;
    overflow: hidden;
}
.quotation-card .table th, .quotation-card .table td {
    vertical-align: middle;
    text-align: center;
}
.quotation-card .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f8f9fa;
}
.quotation-card .btn-primary {
    background: #2563eb;
    border: none;
    border-radius: 24px;
    padding: 0.75em 2em;
    font-weight: 600;
    transition: background 0.2s;
}
.quotation-card .btn-primary:hover {
    background: #1d4ed8;
}
.quotation-card .btn-secondary {
    background: #6c757d;
    border: none;
    border-radius: 24px;
    padding: 0.75em 2em;
    font-weight: 600;
    transition: background 0.2s;
}
.quotation-card .btn-secondary:hover {
    background: #495057;
}
</style>
@endpush

@section('content')
<div class="card shadow-sm quotation-card">
    <div class="card-body">
        <h2 class="mb-4 text-success text-center">Quotation Request Submitted!</h2>
        <p class="lead text-center">Thank you for your request. Our team will review your details and contact you soon.</p>
        
        @if($quotationRequest)
            <div class="text-center mb-4">
                <h5>Request Number: <span class="badge bg-primary">{{ $quotationRequest->request_number }}</span></h5>
                <p class="text-muted">Status: <span class="badge bg-{{ $quotationRequest->status_color }}">{{ $quotationRequest->status_label }}</span></p>
            </div>
        @endif
        
        <hr>
        <h4 class="mb-3 text-center">Quotation Request Details</h4>
        
        @if($quotationRequest && $quotationRequest->rooms->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Room</th>
                            <th>Dimensions</th>
                            <th>Area</th>
                            <th>Volume</th>
                            <th>Scopes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotationRequest->rooms as $room)
                            <tr>
                                <td><strong>{{ $room->name }}</strong></td>
                                <td>{{ $room->length }}m × {{ $room->width }}m × {{ $room->height }}m</td>
                                <td>{{ $room->area }} sqm</td>
                                <td>{{ $room->volume }} cubic m</td>
                                <td>
                                    @if($room->scopes->count() > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($room->scopes as $scope)
                                                <li><strong>{{ $scope->scope_name }}</strong> ({{ $scope->scope_category }})</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No scopes selected</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($sessionData)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Room</th>
                            <th>Dimensions</th>
                            <th>Area</th>
                            <th>Volume</th>
                            <th>Scopes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessionData['rooms'] as $room)
                            @php
                                $area = $room['length'] * $room['width'];
                                $volume = $area * $room['height'];
                            @endphp
                            <tr>
                                <td><strong>{{ $room['name'] }}</strong></td>
                                <td>{{ $room['length'] }}m × {{ $room['width'] }}m × {{ $room['height'] }}m</td>
                                <td>{{ $area }} sqm</td>
                                <td>{{ $volume }} cubic m</td>
                                <td>
                                    @if(isset($room['scope']) && count($room['scope']) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($room['scope'] as $scopeCode => $scopeData)
                                                @if(!empty($scopeData['materials']))
                                                    <li><strong>{{ $scopeCode }}</strong></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No scopes selected</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">
                No quotation request data found.
            </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('client.quotation.create') }}" class="btn btn-primary me-2">Request Another Quotation</a>
            <a href="{{ route('client.quotation.index') }}" class="btn btn-success me-2">View All Requests</a>
            <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>
@endsection 