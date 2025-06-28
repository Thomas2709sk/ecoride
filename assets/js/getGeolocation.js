// Place ce code dans un fichier importé par ton importmap

function getLocation() {
    const x = document.getElementById("geo-output");
    x.textContent = "Demande de localisation en cours...";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        x.textContent = "La géolocalisation n'est pas supportée par ce navigateur.";
    }

    function success(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        // Utilisation du service Geocoder de Google
        const geocoder = new google.maps.Geocoder();
        const latlng = { lat: lat, lng: lon };

        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === "OK" && results[0]) {
                document.getElementById("search_carpool_form_address_start").value = results[0].formatted_address;
                x.textContent = "";
            } else {
                x.textContent = "Impossible de trouver une adresse.";
            }
        });
    }

    function error(err) {
        if (err.code === 1) x.textContent = "Permission de géolocalisation refusée.";
        else if (err.code === 2) x.textContent = "Position non disponible.";
        else if (err.code === 3) x.textContent = "La demande de géolocalisation a expiré.";
        else x.textContent = "Erreur de géolocalisation.";
    }
}

// Pour l'appel inline depuis le bouton HTML
window.getLocation = getLocation;

// Optionnel : fonction d'init vide, requise par Google Maps si tu ne fais pas de carte
window.initMap = function() {};