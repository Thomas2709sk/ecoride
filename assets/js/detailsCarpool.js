function runRealInitMap() {
    const mapDiv = document.getElementById('map');
    if (!mapDiv) {
        document.body.innerHTML = "<div class='alert alert-danger text-center my-4'>Carte indisponible : élément #map manquant.</div>";
        return;
    }

    const startAddress = mapDiv.getAttribute('data-address-start');
    const endAddress = mapDiv.getAttribute('data-address-end');

    if (!startAddress || !endAddress) {
        mapDiv.innerHTML = "<div class='alert alert-warning text-center my-4'>Adresse de départ ou d'arrivée manquante.</div>";
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
                    infoDiv.innerHTML = `<strong>Distance :</strong> ${distance}<br><strong>Durée :</strong> ${duration}`;
                }
            } else {
                mapDiv.innerHTML = `<div class="alert alert-danger text-center my-4">Impossible de calculer l'itinéraire : ${status}</div>`;
            }
        }
    );
}

window.initMap = runRealInitMap;

if (window.google && window.google.maps) {
    window.initMap();
}