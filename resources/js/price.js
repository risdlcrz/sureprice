const ctx = document.getElementById('priceTrendChart').getContext('2d');
    const productData = [
      { product: 'Paint - White', lastPrice: 500, updatedPrice: 450 },
      { product: 'Brush - Large', lastPrice: 250, updatedPrice: 270 },
      { product: 'Tape - 1in', lastPrice: 150, updatedPrice: 140 },
      { product: 'Paint - Blue', lastPrice: 600, updatedPrice: 620 },
      { product: 'Brush - Small', lastPrice: 200, updatedPrice: 190 },
    ];

    const labels = productData.map(data => data.product);
    const lastPrices = productData.map(data => data.lastPrice);
    const updatedPrices = productData.map(data => data.updatedPrice);

    const priceTrendChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Price Trend (PHP)',
          data: updatedPrices,
          borderColor: '#007bff',
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 6,
        }, {
          label: 'Last Price (PHP)',
          data: lastPrices,
          borderColor: '#dc3545',
          backgroundColor: 'rgba(220, 53, 69, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 6,
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: false,
            ticks: {
              stepSize: 50,
            }
          }
        }
      }
    });

    const priceChangeElements = document.querySelectorAll('.price-change');
    priceChangeElements.forEach((el, index) => {
      const lastPrice = productData[index].lastPrice;
      const updatedPrice = productData[index].updatedPrice;
      const priceChange = updatedPrice - lastPrice;

      el.textContent = priceChange;
      el.style.color = priceChange < 0 ? 'green' : 'red';
    });