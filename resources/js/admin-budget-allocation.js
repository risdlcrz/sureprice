document.addEventListener('DOMContentLoaded', function() {
    if (typeof initSpendingChart === 'function') initSpendingChart();
    if (typeof initBreakdownChart === 'function') initBreakdownChart();
    if (typeof initBudgetDonut === 'function') initBudgetDonut();
});

// Chart initialization functions
function initSpendingChart() {
    const ctx = document.getElementById('spendingChart').getContext('2d');
    const monthlyData = window.budgetMonthlyData;
    const weeklyData = window.budgetWeeklyData;
    window.spendingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Monthly Spending',
                data: monthlyData.values,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value.toLocaleString(); }
                    }
                }
            }
        }
    });
}
function initBreakdownChart() {
    const ctx = document.getElementById('costBreakdownChart').getContext('2d');
    const categoryData = window.budgetCategoryData;
    window.breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.values,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } }
        }
    });
}
function initBudgetDonut() {
    const ctx = document.getElementById('budgetDonut').getContext('2d');
    const totalSpent = window.budgetTotalSpent;
    const remaining = window.budgetRemaining;
    window.budgetDonut = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Spent', 'Remaining'],
            datasets: [{
                data: [totalSpent, remaining],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(232, 232, 232, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '80%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } }
        }
    });
}
function toggleChartView(type) {
    const monthlyData = window.budgetMonthlyData;
    const weeklyData = window.budgetWeeklyData;
    const data = type === 'monthly' ? monthlyData : weeklyData;
    window.spendingChart.data.labels = data.labels;
    window.spendingChart.data.datasets[0].data = data.values;
    window.spendingChart.data.datasets[0].label = type === 'monthly' ? 'Monthly Spending' : 'Weekly Spending';
    window.spendingChart.update();
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('active');
        }
    });
}
function toggleBreakdownView(type) {
    const categoryData = window.budgetCategoryData;
    const supplierData = window.budgetSupplierData;
    const data = type === 'category' ? categoryData : supplierData;
    window.breakdownChart.data.labels = data.labels;
    window.breakdownChart.data.datasets[0].data = data.values;
    window.breakdownChart.update();
    const buttons = document.querySelectorAll('.card-header .btn-group button');
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('active');
        }
    });
} 