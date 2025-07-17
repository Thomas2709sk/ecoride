document.addEventListener("DOMContentLoaded", function () {
  const fetchData = async () => {
    try {
      const response = await fetch('/admin/credits/graph/credits');
      if (!response.ok) {
  
        return;
      }

      const data = await response.json();

      // Remplir le menu de sélection avec les mois disponibles
      const monthSelector = document.getElementById('monthSelector');
      if (!monthSelector) return; 

      data.forEach((monthData, index) => {
        const option = document.createElement('option');
        option.value = index; 
        option.textContent = monthData.month; 
        monthSelector.appendChild(option);
      });

      // Afficher le graphique du premier mois par défaut
      if (data.length > 0 && typeof renderCreditsGraph === 'function') {
        renderCreditsGraph(data[0]);
      }

      // Mettre à jour le graphique lorsque l'utilisateur change de mois
      monthSelector.addEventListener('change', (event) => {
        const selectedIndex = parseInt(event.target.value, 10);
        if (data[selectedIndex] && typeof renderCreditsGraph === 'function') {
          renderCreditsGraph(data[selectedIndex]);
        }
      });
    } catch (error) {

    }
  };

  fetchData();
});