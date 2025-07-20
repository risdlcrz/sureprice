import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart');
    if (monthlySalesCtx) {
        // You must pass the data from the backend to JS via a data attribute or window object
        const monthlySalesData = JSON.parse(monthlySalesCtx.dataset.monthlySales || '[]');
        const labels = Object.keys(monthlySalesData);
        const data = Object.values(monthlySalesData);
        new Chart(monthlySalesCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales (₱)',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}); 