// Custom JS extracted from admin/price-analysis.blade.php

$(document).ready(function() {
    $('#materialSelect').select2({
        placeholder: 'Select a material',
        allowClear: true
    });

    // Always treat IDs as strings for comparison
    const materials = window.materialsData || [];
    let selectedMaterialId = $('#materialSelect').val() || (materials[0] ? String(materials[0].id) : null);

    function rollingForecasts(prices) {
        // prices: array of numbers (actuals)
        let forecasts = [null, null]; // first two points can't be forecasted
        for (let i = 2; i < prices.length; i++) {
            // Use only data up to i-1 to forecast i
            const x = Array.from({length: i}, (_, k) => k + 1);
            const y = prices.slice(0, i);
            const n = x.length;
            const sumX = x.reduce((a, b) => a + b, 0);
            const sumY = y.reduce((a, b) => a + b, 0);
            let sumXY = 0, sumX2 = 0;
            for (let j = 0; j < n; j++) {
                sumXY += x[j] * y[j];
                sumX2 += x[j] * x[j];
            }
            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
            const intercept = (sumY - slope * sumX) / n;
            const nextX = i + 1;
            const forecast = slope * nextX + intercept;
            forecasts.push(forecast);
        }
        return forecasts;
    }

    function renderPriceChart(materialId) {
        const material = materials.find(m => String(m.id) === String(materialId));
        if (!material) return;
        const history = material.price_history_for_analysis ? Object.entries(material.price_history_for_analysis) : [];
        const chartLabels = history.map(([date, price]) => date);
        const chartData = history.map(([date, price]) => price);
        const forecasted = material.forecasted_price;
        let forecastLabels = [...chartLabels];
        let forecastData = [...chartData];
        // Rolling forecast line
        let rollingForecastData = rollingForecasts(chartData);
        // Optionally add the next period forecast as a single point
        let forecastDataset = Array(chartData.length).fill(null);
        if (forecasted !== null && chartLabels.length > 0) {
            const lastDate = new Date(chartLabels[chartLabels.length-1]);
            const nextDate = new Date(lastDate);
            nextDate.setMonth(lastDate.getMonth() + 1);
            const forecastLabel = nextDate.toISOString().slice(0, 7);
            forecastLabels.push(forecastLabel);
            forecastData.push(null); // keep history line from connecting to forecast
            rollingForecastData.push(null); // keep forecast line from connecting to next period
            forecastDataset.push(forecasted);
        }

        if (window.priceTrendChartInstance) window.priceTrendChartInstance.destroy();
        const ctx = document.getElementById('priceTrendChart').getContext('2d');
        window.priceTrendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: forecastLabels,
                datasets: [
                    {
                        label: material.name + ' Price History',
                        data: forecastData,
                        borderColor: 'rgba(30, 136, 229, 1)',
                        backgroundColor: 'rgba(30, 136, 229, 0.1)',
                        fill: true,
                        tension: 0.4,
                        spanGaps: false
                    },
                    {
                        label: material.name + ' Rolling Forecast',
                        data: rollingForecastData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderDash: [5, 5],
                        pointRadius: 4,
                        fill: false,
                        tension: 0.4,
                        spanGaps: false
                    },
                    {
                        label: material.name + ' Next Period Forecast',
                        data: forecastDataset,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderDash: [2, 2],
                        pointRadius: 6,
                        fill: false,
                        tension: 0.4,
                        spanGaps: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    }

    // Initial render
    renderPriceChart(selectedMaterialId);

    $('#materialSelect').on('change', function() {
        selectedMaterialId = $(this).val();
        renderPriceChart(selectedMaterialId);
    });
});

// --- Sample Data for Additional Charts ---
// Replace with real data from your backend as needed
const ordersPerMonthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
const ordersPerMonthData = [12, 19, 3, 5, 2, 3];
new Chart(document.getElementById('ordersPerMonthChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ordersPerMonthLabels,
        datasets: [{
            label: 'Orders',
            data: ordersPerMonthData,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: { responsive: true }
});

const mostUsedMaterialsLabels = ['Adhesive', 'Caulk', 'Conduit', 'Drywall tape'];
const mostUsedMaterialsData = [300, 250, 200, 150];
new Chart(document.getElementById('mostUsedMaterialsPie').getContext('2d'), {
    type: 'pie',
    data: {
        labels: mostUsedMaterialsLabels,
        datasets: [{
            data: mostUsedMaterialsData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ]
        }]
    },
    options: { responsive: true }
});

const mostConsumedThisMonthLabels = ['Adhesive', 'Caulk', 'Conduit', 'Drywall tape'];
const mostConsumedThisMonthData = [50, 40, 30, 20];
new Chart(document.getElementById('mostConsumedThisMonthBar').getContext('2d'), {
    type: 'bar',
    data: {
        labels: mostConsumedThisMonthLabels,
        datasets: [{
            label: 'Quantity Consumed',
            data: mostConsumedThisMonthData,
            backgroundColor: 'rgba(255, 159, 64, 0.7)'
        }]
    },
    options: { responsive: true }
}); 