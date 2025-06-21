@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Price Trend Analysis</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Options</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('procurement.analytics.price-analysis') }}" method="GET">
                <div class="mb-3">
                    <label for="material_ids" class="form-label">Select Materials to Display</label>
                    <select name="material_ids[]" id="material_ids" class="form-control" multiple="multiple">
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" {{ in_array($material->id, $selectedMaterialIds ?? []) ? 'selected' : '' }}>
                                {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <a href="{{ route('procurement.analytics.price-analysis') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear Filter
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Material Price Trends Over Time</h5>
        </div>
        <div class="card-body">
            @if(count($priceData) > 0)
                <canvas id="priceTrendChart"></canvas>
            @else
                <div class="text-center p-5">
                    <p class="text-muted">
                        @if(empty($selectedMaterialIds))
                            Please select one or more materials from the filter above to display the price trend chart.
                        @else
                            No historical price data found for the selected material(s).
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#material_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select materials to view their price trends',
            allowClear: true
        });

        const ctx = document.getElementById('priceTrendChart');
        if (ctx) {
            const priceData = JSON.parse('@json($priceData)');

            const datasets = priceData.map((material, index) => {
                const color = `hsl(${(index * 137.508) % 360}, 50%, 50%)`;
                return {
                    label: material.label,
                    data: material.data,
                    borderColor: color,
                    backgroundColor: color + '33', // Add some transparency
                    tension: 0.1
                };
            });

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    datasets: datasets
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Unit Price'
                            },
                            ticks: {
                                callback: function(value, index, values) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += '₱' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush