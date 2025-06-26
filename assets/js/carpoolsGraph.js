
window.renderGraph = function ({ labels, counts, month }) {
    const chartsContainer = document.getElementById('chartsContainer');
  

    while (chartsContainer.firstChild) {
      chartsContainer.removeChild(chartsContainer.firstChild);
    }

    const filteredCounts = counts.filter(count => count > 0);
  

    const allSame = filteredCounts.every(count => count === filteredCounts[0]);
  
    let barColors;
    if (allSame || filteredCounts.length === 0) {
      barColors = counts.map(() => 'rgba(128, 128, 128, 0.8)');
    } else {

      const minReservations = Math.min(...filteredCounts);
      const maxReservations = Math.max(...filteredCounts);
  

      barColors = counts.map(count => {
        if (count === minReservations && count > 0) {
          return 'rgba(255, 99, 132, 0.8)'; 
        } else if (count === maxReservations) {
          return 'rgba(144, 238, 144, 0.8)';
        } else {
          return 'rgba(54, 162, 235, 0.8)';
        }
      });
    }
  

    const canvasContainer = document.createElement('div');
    canvasContainer.classList.add('chart-container');
  

    const canvas = document.createElement('canvas');
    canvasContainer.appendChild(canvas);
  

    chartsContainer.appendChild(canvasContainer);

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: month,
          data: counts,
          backgroundColor: barColors,
          borderColor: barColors.map(color => color.replace('0.8', '1')),
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
             max: 5,
            ticks: {
              stepSize: 1 
            },
            suggestedMax: Math.max(...filteredCounts) + 1
          },
          x: {
            ticks: {
              autoSkip: true,
              maxTicksLimit: labels.length
            }
          }
        }
      }
    });
  };