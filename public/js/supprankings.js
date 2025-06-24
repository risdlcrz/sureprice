
        const configLine = {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rating Trend',
                    data: ratings,
                    fill: false,
                    borderColor: 'green',
                    tension: 0.1
                }]
            }
        };

        const configBar = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Company Rating',
                    data: ratings,
                    backgroundColor: 'rgba(2,145,45,0.7)',
                    borderColor: 'rgba(2,145,45,1)',
                    borderWidth: 1
                }]
            }
        };

        new Chart(document.getElementById('lineChart'), configLine);
        new Chart(document.getElementById('barChart'), configBar);