document.addEventListener("DOMContentLoaded", function () {
    const fetchData = async () => {
      try {
        const response = await fetch('/admin/graph/carpools');
        if (!response.ok) {
          throw new Error(`Erreur lors de la récupération des données: ${response.statusText}`);
        }
  
        const data = await response.json();
  
        const monthSelector = document.getElementById('monthSelect');
        data.forEach((monthData, index) => {
          const option = document.createElement('option');
          option.value = index;
          option.textContent = monthData.month;
          monthSelector.appendChild(option);
        });
  
        // Show chart of the the first month available
        if (data.length > 0) {
          renderGraph(data[0]); // Call function renderGraph grom `reservGraph.js`
        }
  
        // Update chart when admin change month
        monthSelector.addEventListener('change', (event) => {
          const selectedIndex = parseInt(event.target.value, 10);
          renderGraph(data[selectedIndex]); // Call function renderGraph from `reservGraph.js`
        });
      } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
      }
    };
  
    fetchData();
  });