// Extracted from resources/views/warehouse/reports/analytics.blade.php
const __page = window.__page || {};
// Modern color palette
const palette = [
    '#3b82f6', '#06b6d4', '#f59e42', '#f43f5e', '#a78bfa', '#10b981', '#fbbf24', '#6366f1', '#f87171', '#34d399', '#f472b6', '#60a5fa', '#facc15', '#4ade80', '#818cf8'
];

// Inventory Levels Chart
const inventoryLevelsCtx = document.getElementById('inventoryLevelsChart').getContext('2d');
const inventoryLabels = JSON.parse('__page.analytics1_0');
const inventoryData = JSON.parse('__page.analytics1_1');
new Chart(inventoryLevelsCtx, {
    type: 'bar',
    data: {
        labels: inventoryLabels,
        datasets: [{
            label: 'Current Stock',
            data: inventoryData,
            backgroundColor: palette[0],
            borderRadius: 8,
            maxBarThickness: 32
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: palette[0],
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});

// Most Used Materials per Project Chart
const mostUsedCtx = document.getElementById('mostUsedMaterialsChart').getContext('2d');
const mostUsedRaw = JSON.parse('__page.analytics1_2');
const projectGroups = {};
mostUsedRaw.forEach(item => {
    if (!projectGroups[item.reference_number]) projectGroups[item.reference_number] = [];
    projectGroups[item.reference_number].push({
        label: item.material ? item.material.name : 'Unknown',
        value: item.total_used
    });
});
const projectLabels = Object.keys(projectGroups);
const materialLabels = [...new Set(mostUsedRaw.map(item => item.material ? item.material.name : 'Unknown'))];
const datasets = projectLabels.map((project, i) => ({
    label: project,
    data: materialLabels.map(mat => {
        const found = projectGroups[project].find(m => m.label === mat);
        return found ? found.value : 0;
    }),
    backgroundColor: palette[i % palette.length],
    borderRadius: 8,
    maxBarThickness: 28
}));
new Chart(mostUsedCtx, {
    type: 'bar',
    data: {
        labels: materialLabels,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: '#06b6d4',
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});

// Monthly Usage Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
const monthlyRaw = JSON.parse('__page.analytics1_3');
const months = [...new Set(monthlyRaw.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`))];
const materialNames = [...new Set(monthlyRaw.map(item => item.material ? item.material.name : 'Unknown'))];
const materialMap = {};
materialNames.forEach(name => { materialMap[name] = Array(months.length).fill(0); });
monthlyRaw.forEach(item => {
    const idx = months.indexOf(`${item.year}-${String(item.month).padStart(2, '0')}`);
    const name = item.material ? item.material.name : 'Unknown';
    if (idx !== -1) materialMap[name][idx] = item.total_used;
});
const trendDatasets = materialNames.map((name, i) => ({
    label: name,
    data: materialMap[name],
    borderColor: palette[i % palette.length],
    backgroundColor: palette[i % palette.length] + '33',
    fill: true,
    tension: 0.4,
    pointRadius: 4,
    pointHoverRadius: 7
}));
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: trendDatasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: '#f59e42',
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});
