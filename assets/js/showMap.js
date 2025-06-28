document.addEventListener('DOMContentLoaded', async () => {
    const startInput = document.getElementById('create_carpool_form_address_start');
    const endInput = document.getElementById('create_carpool_form_address_end');
    const startLat = document.getElementById('create_carpool_form_startLat');
    const startLon = document.getElementById('create_carpool_form_startLon');
    const endLat = document.getElementById('create_carpool_form_endLat');
    const endLon = document.getElementById('create_carpool_form_endLon');
    const routeInfoDiv = document.getElementById('route-info');
    const durationInput = document.getElementById('create_carpool_form_duration');

    if (!startInput || !endInput) {
        console.error("Champs d'adresses introuvables !");
        return;
    }

    let map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 48.8566, lng: 2.3522 },
        zoom: 6
    });

    let directionsService = new google.maps.DirectionsService();
    let directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

    // Géocodage Google
    async function geocode(address) {
        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    resolve([location.lat(), location.lng()]);
                } else {
                    resolve(null);
                }
            });
        });
    }

    async function showRoute() {
        const start = startInput.value;
        const end = endInput.value;

        // Reset fields before new search
        if (startLat) startLat.value = '';
        if (startLon) startLon.value = '';
        if (endLat) endLat.value = '';
        if (endLon) endLon.value = '';
        if (routeInfoDiv) routeInfoDiv.textContent = '';
        if (durationInput) durationInput.value = '';

        if (start && end) {
            const startCoord = await geocode(start);
            const endCoord = await geocode(end);

            if (startCoord && endCoord) {

                if (startLat) startLat.value = startCoord[0];
                if (startLon) startLon.value = startCoord[1];
                if (endLat) endLat.value = endCoord[0];
                if (endLon) endLon.value = endCoord[1];

                const request = {
                    origin: { lat: startCoord[0], lng: startCoord[1] },
                    destination: { lat: endCoord[0], lng: endCoord[1] },
                    travelMode: google.maps.TravelMode.DRIVING
                };
                directionsService.route(request, (result, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);

                        const leg = result.routes[0].legs[0];

                        if (routeInfoDiv) {
                            routeInfoDiv.innerHTML = `
                                <strong>Distance :</strong> ${leg.distance.text} <br>
                                <strong>Durée :</strong> ${leg.duration.text}
                            `;
                        }

                        // MAJ input hidden durée (en secondes)
                        if (durationInput) durationInput.value = leg.duration.value;
                    } else {
                        directionsRenderer.set('directions', null);
                        if (routeInfoDiv) routeInfoDiv.textContent = '';
                        if (durationInput) durationInput.value = '';
                    }
                });
            }
        } else {
            directionsRenderer.set('directions', null);
            if (routeInfoDiv) routeInfoDiv.textContent = '';
            if (durationInput) durationInput.value = '';
        }
    }

    startInput.addEventListener('input', showRoute);
    endInput.addEventListener('input', showRoute);
    showRoute();
});