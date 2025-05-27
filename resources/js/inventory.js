const ctx = document.getElementById('inventoryStatusChart').getContext('2d');
    const storedData = JSON.parse(localStorage.getItem('chartData') || '{"labels":[],"data":[],"colors":[]}');

    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: storedData.labels,
        datasets: [{
          label: 'Average Stock (%)',
          data: storedData.data,
          borderColor: '#28a745',
          backgroundColor: 'rgba(40, 167, 69, 0.1)',
          tension: 0.4,
          pointRadius: 6,
          pointBackgroundColor: storedData.colors
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } }
        }
      }
    });

    function saveChartData() {
      const chartData = {
        labels: chart.data.labels,
        data: chart.data.datasets[0].data,
        colors: chart.data.datasets[0].pointBackgroundColor
      };
      localStorage.setItem('chartData', JSON.stringify(chartData));
    }

    function saveSwitchStates() {
      const states = {};
      document.querySelectorAll('.custom-switch input').forEach(input => {
        states[input.dataset.id] = input.checked;
      });
      localStorage.setItem('switchStates', JSON.stringify(states));
    }

    function loadSwitchStates() {
      const states = JSON.parse(localStorage.getItem('switchStates') || '{}');
      document.querySelectorAll('.custom-switch input').forEach(input => {
        if (states.hasOwnProperty(input.dataset.id)) {
          input.checked = states[input.dataset.id];
        }
      });
    }

    function updateGraphAndStats() {
      const rows = document.querySelectorAll('table tbody tr');
      const outOfStockEl = document.getElementById('outOfStockCount');
      const lowStockEl = document.getElementById('lowStockCount');
      const criticalList = document.getElementById('criticalItemList');
      const lowStockList = document.getElementById('lowStockItemList');

      let outOfStock = 0;
      let lowStock = 0;
      let totalPercentages = [];

      criticalList.innerHTML = '';
      lowStockList.innerHTML = '';

      rows.forEach(row => {
        const product = row.cells[0].textContent.trim();
        const total = parseInt(row.cells[1].textContent.trim());
        const available = parseInt(row.cells[2].textContent.trim());
        const percent = total > 0 ? (available / total) * 100 : 0;
        const alertCheckbox = row.querySelector('.custom-switch input');

        totalPercentages.push(percent);

        if (available === 0) {
          outOfStock++;
          const li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          li.textContent = product;
          criticalList.appendChild(li);
        } else if (available < 20 && alertCheckbox.checked) {
          lowStock++;
          const li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          li.textContent = product;
          lowStockList.appendChild(li);
        }
      });

      outOfStockEl.textContent = `${outOfStock} SKUs`;
      lowStockEl.textContent = `${lowStock} SKUs`;

      const average = Math.round(totalPercentages.reduce((a, b) => a + b, 0) / totalPercentages.length);
      const color = average < 20 ? '#dc3545' : average < 50 ? '#ffc107' : average < 80 ? '#0d6efd' : '#198754';
      const now = new Date().toLocaleTimeString();

      chart.data.labels.push(now);
      chart.data.datasets[0].data.push(average);
      chart.data.datasets[0].pointBackgroundColor.push(color);

      if (chart.data.labels.length > 10) {
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
        chart.data.datasets[0].pointBackgroundColor.shift();
      }

      chart.update();
      saveChartData();
    }

    document.getElementById('toggleAll').addEventListener('click', function () {
      const checkboxes = document.querySelectorAll('.custom-switch input');
      const allChecked = Array.from(checkboxes).every(cb => cb.checked);
      checkboxes.forEach(cb => cb.checked = !allChecked);
      saveSwitchStates();
      updateGraphAndStats();
    });

    document.querySelectorAll('.custom-switch input').forEach(input => {
      input.addEventListener('change', () => {
        saveSwitchStates();
        updateGraphAndStats();
      });
    });

    loadSwitchStates();
    updateGraphAndStats();
    setInterval(updateGraphAndStats, 604800000);