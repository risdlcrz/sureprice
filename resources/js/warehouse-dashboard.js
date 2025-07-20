import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyMovementsChart').getContext('2d');
    const monthlyData = JSON.parse(document.getElementById('monthlyMovementsChart').dataset.monthlyMovements || '[]');
    const months = monthlyData.map(item => {
        const date = new Date();
        date.setMonth(item.month - 1);
        return date.toLocaleString('default', { month: 'short' });
    });
    const incomingData = monthlyData.map(item => item.incoming);
    const outgoingData = monthlyData.map(item => item.outgoing);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Incoming',
                    data: incomingData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Outgoing',
                    data: outgoingData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
}); 