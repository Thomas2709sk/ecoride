function runRealInitMap() {
    const mapDiv = document.getElementById('map');
    if (!mapDiv) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger text-center my-4';
        alertDiv.textContent = "Carte indisponible : élément #map manquant.";
        document.body.innerHTML = "";
        document.body.appendChild(alertDiv);
        return;
    }

    const startAddress = mapDiv.getAttribute('data-address-start');
    const endAddress = mapDiv.getAttribute('data-address-end');

    if (!startAddress || !endAddress) {
        mapDiv.innerHTML = "";
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-warning text-center my-4';
        alertDiv.textContent = "Adresse de départ ou d'arrivée manquante.";
        mapDiv.appendChild(alertDiv);
        return;
    }

    const map = new google.maps.Map(mapDiv, {
        zoom: 6,
        center: { lat: 46.603354, lng: 1.888334 },
    });

    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    directionsService.route(
        {
            origin: startAddress,
            destination: endAddress,
            travelMode: google.maps.TravelMode.DRIVING,
        },
        (response, status) => {
            if (status === "OK") {
                directionsRenderer.setDirections(response);
                const route = response.routes[0].legs[0];
                const distance = route.distance.text;
                const duration = route.duration.text;

                const infoDiv = document.getElementById('route-info');
                if (infoDiv) {
                    // Nettoyer le contenu précédent
                    infoDiv.innerHTML = "";

                    // Distance
                    const strongDistance = document.createElement('strong');
                    strongDistance.textContent = "Distance :";
                    infoDiv.appendChild(strongDistance);
                    infoDiv.append(` ${distance}`);

                    infoDiv.appendChild(document.createElement('br'));

                    // Durée
                    const strongDuration = document.createElement('strong');
                    strongDuration.textContent = "Durée :";
                    infoDiv.appendChild(strongDuration);
                    infoDiv.append(` ${duration}`);
                }
            } else {
                mapDiv.innerHTML = "";
                const alertDiv = document.createElement('div');
                alertDiv.className = "alert alert-danger text-center my-4";
                alertDiv.textContent = `Impossible de calculer l'itinéraire : ${status}`;
                mapDiv.appendChild(alertDiv);
            }
        }
    );
}

window.initMap = runRealInitMap;

if (window.google && window.google.maps) {
    window.initMap();
}