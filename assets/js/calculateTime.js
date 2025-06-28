document.addEventListener('DOMContentLoaded', () => {
    const beginInput = document.getElementById('create_carpool_form_begin');
    const endInput = document.getElementById('create_carpool_form_end');
    const startAddress = document.getElementById('create_carpool_form_address_start');
    const endAddress = document.getElementById('create_carpool_form_address_end');


    function computeArrivalTime(durationSec) {
        const beginStr = beginInput.value;
        if (!beginStr) return;

        const [hours, minutes] = beginStr.split(':').map(Number);
        const beginDate = new Date();
        beginDate.setHours(hours, minutes, 0, 0);

        const endDate = new Date(beginDate.getTime() + durationSec * 1000);
        const endHours = String(endDate.getHours()).padStart(2, '0');
        const endMinutes = String(endDate.getMinutes()).padStart(2, '0');
        const endStr = `${endHours}:${endMinutes}`;
        endInput.value = endStr;
    }


    function handleDirectionsResult(result) {
        if (result && result.routes && result.routes.length > 0) {
            const leg = result.routes[0].legs[0];
            const durationSec = leg.duration.value;
            computeArrivalTime(durationSec);
        }
    }


    beginInput.addEventListener('input', () => {
        if (window.latestDirectionsResult) {
            handleDirectionsResult(window.latestDirectionsResult);
        }
    });

   
    async function showRoute() {

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({ map: window.gmap });

        const start = startAddress.value;
        const end = endAddress.value;
        if (start && end) {
            const request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    window.latestDirectionsResult = result;
                    handleDirectionsResult(result);
                } else {
                    directionsRenderer.set('directions', null);
                    window.latestDirectionsResult = null;
                    endInput.value = '';
                }
            });
        }
    }

    startAddress.addEventListener('input', showRoute);
    endAddress.addEventListener('input', showRoute);

    showRoute();
});