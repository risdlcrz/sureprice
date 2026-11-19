@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Past Transactions</h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="frequencyChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Top 5 Suppliers by Transaction Count</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($topSuppliers as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $s['supplier'] }}
                                <span class="badge bg-primary rounded-pill">{{ $s['count'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Category Distribution</div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Past Transactions</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material/Service</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                            <td>{{ $item->material->name ?? 'Unknown' }}</td>
                            <td>{{ $item->supplier->company_name ?? 'Unknown' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->estimated_unit_price, 2) }}</td>
                            <td>₱{{ number_format($item->total_amount, 2) }}</td>
                            <td>{{ $item->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>window.__page = Object.assign(window.__page || {}, { past0_0: {{ Js::from($frequency->pluck('material')) }}, past0_1: {{ Js::from($frequency->pluck('count')) }}, past0_2: {{ Js::from($monthly->pluck('month')) }}, past0_3: {{ Js::from($monthly->pluck('count')) }}, past0_4: {{ Js::from($categoryDist->pluck('category')) }}, past0_5: {{ Js::from($categoryDist->pluck('count')) }} });</script>
@vite('resources/js/pages/transactions-past.js')
@endpush 