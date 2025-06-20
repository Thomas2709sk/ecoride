document.addEventListener('DOMContentLoaded', async () => {
    const mapDiv = document.getElementById('map');
    const start = mapDiv.getAttribute('data-address-start');
    const end = mapDiv.getAttribute('data-address-end');

    async function geocode(address) {
        const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address));
        const data = await res.json();
        if (data.length > 0) return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
        return null;
    }

    const startCoord = await geocode(start);
    const endCoord = await geocode(end);

    if (!startCoord || !endCoord) {
        mapDiv.innerHTML = "Adresse introuvable.";
        return;
    }

    const map = L.map('map').setView(startCoord, 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: "© OpenStreetMap"
    }).addTo(map);

    L.Routing.control({
        waypoints: [
            L.latLng(startCoord[0], startCoord[1]),
            L.latLng(endCoord[0], endCoord[1])
        ],
        routeWhileDragging: false,
        draggableWaypoints: false,
        addWaypoints: false,
        show: false
    }).addTo(map);
});