document.addEventListener('DOMContentLoaded', async () => {
    const startInput = document.getElementById('create_carpool_form_address_start');
    const endInput = document.getElementById('create_carpool_form_address_end');
    const startLat = document.getElementById('create_carpool_form_startLat');
    const startLon = document.getElementById('create_carpool_form_startLon');
    const endLat = document.getElementById('create_carpool_form_endLat');
    const endLon = document.getElementById('create_carpool_form_endLon');

    if (!startInput || !endInput) {
        console.error("Champs d'adresses introuvables !");
        return;
    }

    const geocode = async (address) => {
        const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address));
        const data = await res.json();
        if (data.length > 0) return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
        return null;
    };

    let map = L.map('map').setView([48.8566, 2.3522], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: "© OpenStreetMap"
    }).addTo(map);

    let routingControl = null;

    async function showRoute() {
        const start = startInput.value;
        const end = endInput.value;

        // Reset fields before new search
        if (startLat) startLat.value = '';
        if (startLon) startLon.value = '';
        if (endLat) endLat.value = '';
        if (endLon) endLon.value = '';

        if (start && end) {
            const startCoord = await geocode(start);
            const endCoord = await geocode(end);
            if (startCoord && endCoord) {
                // MAJ des champs coordonnées
                if (startLat) startLat.value = startCoord[0];
                if (startLon) startLon.value = startCoord[1];
                if (endLat) endLat.value = endCoord[0];
                if (endLon) endLon.value = endCoord[1];

                if (routingControl) map.removeControl(routingControl);
                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(startCoord[0], startCoord[1]),
                        L.latLng(endCoord[0], endCoord[1])
                    ],
                    routeWhileDragging: false,
                    draggableWaypoints: false,
                    addWaypoints: false
                }).addTo(map);
            }
        } else {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
        }
    }

    startInput.addEventListener('input', showRoute);
    endInput.addEventListener('input', showRoute);
    showRoute();
});