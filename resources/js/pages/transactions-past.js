// Extracted from resources/views/transactions/past.blade.php
const __page = window.__page || {};
const freqLabels = __page.past0_0;
const freqData = __page.past0_1;
const monthLabels = __page.past0_2;
const monthData = __page.past0_3;

const ctx1 = document.getElementById('frequencyChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: freqLabels,
        datasets: [{
            label: 'Frequency of Purchases/Services',
            data: freqData,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

const ctx2 = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Monthly Purchases/Services',
            data: monthData,
            fill: false,
            borderColor: 'rgba(255, 99, 132, 0.8)',
            backgroundColor: 'rgba(255, 99, 132, 0.4)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true
    }
});

const catLabels = __page.past0_4;
const catData = __page.past0_5;
const ctx3 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx3, {
    type: 'pie',
    data: {
        labels: catLabels,
        datasets: [{
            label: 'Category Distribution',
            data: catData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
