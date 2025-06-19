document.addEventListener('DOMContentLoaded', async () => {
    const startInput = document.getElementById('create_carpool_form_address_start');
    const endInput = document.getElementById('create_carpool_form_address_end');

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
        if (start && end) {
            const startCoord = await geocode(start);
            const endCoord = await geocode(end);
            if (startCoord && endCoord) {
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
        }
    }

    startInput.addEventListener('input', showRoute);
    endInput.addEventListener('input', showRoute);
    showRoute();
});