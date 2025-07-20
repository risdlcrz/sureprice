@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4 min-vh-100">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Price Trend Analysis</h1>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card shadow-lg rounded p-4">
                    <div class="d-flex flex-wrap align-items-center mb-3 gap-3">
                        <label for="materialSelect" class="form-label mb-0 me-2 fw-bold">Select Material:</label>
                        <select id="materialSelect" class="form-select" style="width: 300px;">
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <h5 class="mb-3 fw-bold text-primary">Price Trend (PHP)</h5>
                    <canvas id="priceTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <h3 class="fw-bold text-center mb-4 text-secondary text-uppercase">Descriptive Analytics</h3>
        <div class="row g-4 mb-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Orders Per Month</h6>
                    <canvas id="ordersPerMonthChart" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Most Used Materials Annually</h6>
                    <canvas id="mostUsedMaterialsPie" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100 rounded p-3">
                    <h6 class="card-title fw-bold text-info">Most Consumed Material This Month</h6>
                    <canvas id="mostConsumedThisMonthBar" height="180"></canvas>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <h3 class="fw-bold text-center mb-4 text-secondary text-uppercase">Product Prices</h3>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card shadow rounded p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm bg-white align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Last Price (PHP)</th>
                                    <th>Updated Price (PHP)</th>
                                    <th>Price Change</th>
                                    <th>Forecasted Price (Next Period)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($materials as $material)
                                @php
                                    $history = $material->price_history_for_analysis ?? collect();
                                    $historyArr = array_values($history->all());
                                    $lastPrice = count($historyArr) > 1 ? $historyArr[count($historyArr)-2] : '-';
                                    $updatedPrice = count($historyArr) ? $historyArr[count($historyArr)-1] : '-';
                                    $priceChange = is_numeric($lastPrice) && is_numeric($updatedPrice) ? $updatedPrice - $lastPrice : null;
                                @endphp
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $lastPrice }}</td>
                                    <td>{{ $updatedPrice }}</td>
                                    <td class="price-change" style="color: {{ $priceChange < 0 ? 'green' : ($priceChange > 0 ? 'red' : 'black') }};">
                                        {{ $priceChange > 0 ? '+' : '' }}{{ $priceChange ?? '-' }}
                                    </td>
                                    <td><strong>{{ $material->forecasted_price ?? 'N/A' }}</strong></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/admin-price-analysis.js'])
@endpush
