@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Budget Allocation & Expenditures</h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Budget vs. Expenditure per Contract</h5>
        </div>
        <div class="card-body">
            <canvas id="budgetChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('budgetChart').getContext('2d');
        const chartData = JSON.parse('@json($chartData)');

        const labels = chartData.map(item => item.label);
        const budgetData = chartData.map(item => item.budget);
        const expenditureData = chartData.map(item => item.expenditure);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Budget',
                        data: budgetData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenditure',
                        data: expenditureData,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
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
    });
</script>
@endpush 