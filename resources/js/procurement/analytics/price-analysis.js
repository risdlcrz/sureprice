import $ from 'jquery';
import 'select2';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';

document.addEventListener('DOMContentLoaded', function () {
    $('#material_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select materials to view their price trends',
        allowClear: true
    });

    const ctx = document.getElementById('priceTrendChart');
    if (ctx) {
        const priceData = JSON.parse(ctx.dataset.priceData || '[]');

        const datasets = priceData.map((material, index) => {
            const color = `hsl(${(index * 137.508) % 360}, 50%, 50%)`;
            return {
                label: material.label,
                data: material.data,
                borderColor: color,
                backgroundColor: color + '33',
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
                            callback: function(value) {
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