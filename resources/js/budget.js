import Chart from 'chart.js/auto';

const spendingChart = new Chart(document.getElementById('spendingChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25'],
            datasets: [{
                label: 'PHP Spent',
                data: [5000, 8200, 12700, 15450, 21450, 27500],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱' + value
                    }
                }
            }
        }
    });

    const costBreakdownChart = new Chart(document.getElementById('costBreakdownChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Office Supplies', 'Transportation', 'Utilities', 'Miscellaneous'],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

// helper toggles -------------------------------------------------------------
function toggleChartView(period) {
    // In real application this would fetch new data; here we simply update label
    if (period === 'weekly') {
        spendingChart.data.labels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        spendingChart.data.datasets[0].data = [1200,1300,1250,1400,1350,1500,1600];
    } else {
        spendingChart.data.labels = ['Jan','Feb','Mar','Apr','May','Jun'];
        spendingChart.data.datasets[0].data = [5000,8200,12700,15450,21450,27500];
    }
    spendingChart.update();
}

function toggleBreakdownView(type) {
    // placeholder: swap data set by supplier/category
    if (type === 'supplier') {
        costBreakdownChart.data.labels = ['Supplier A','Supplier B','Supplier C'];
        costBreakdownChart.data.datasets[0].data = [50,30,20];
    } else {
        costBreakdownChart.data.labels = ['Office Supplies','Transportation','Utilities','Miscellaneous'];
        costBreakdownChart.data.datasets[0].data = [40,25,20,15];
    }
    costBreakdownChart.update();
}