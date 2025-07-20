import Chart from 'chart.js/auto';

// Dummy data for chart and legend
const rankings = window.rankingsData || [];
const topSuppliers = rankings.slice(0, 3);
const ctx = document.getElementById('topSuppliersChart')?.getContext('2d');
if (ctx && topSuppliers.length > 0) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topSuppliers.map(s => s.supplier.company_name),
            datasets: [{
                label: 'Overall Score',
                data: topSuppliers.map(s => s.score),
                backgroundColor: ['#FFD700', '#C0C0C0', '#CD7F32'],
                borderColor: ['#FFD700', '#C0C0C0', '#CD7F32'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    // Create custom legend
    const legendContainer = document.getElementById('topSuppliersLegend');
    const medals = ['🥇', '🥈', '🥉'];
    topSuppliers.forEach((supplier, index) => {
        const div = document.createElement('div');
        div.className = 'mb-3';
        div.innerHTML = `
            <div class="d-flex align-items-center">
                <span class="me-2">${medals[index]}</span>
                <div>
                    <h6 class="mb-0">${supplier.supplier.company_name}</h6>
                    <small class="text-muted">Score: ${supplier.score.toFixed(2)}</small>
                </div>
            </div>
        `;
        legendContainer?.appendChild(div);
    });
}
// Expose rankings data from Blade
if (typeof rankings === 'undefined' && window.rankingsData === undefined) {
    window.rankingsData = [];
} 