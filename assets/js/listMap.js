
document.addEventListener('DOMContentLoaded', function () {
    const tabList = document.getElementById('tabList');
    const tabMap  = document.getElementById('tabMap');
    const listView = document.getElementById('listView');
    const mapView = document.getElementById('mapView');
    let mapInstance = null;

    tabList.addEventListener('click', function() {
        tabList.classList.add('active');
        tabMap.classList.remove('active');
        listView.style.display = '';
        mapView.style.display = 'none';
    });

    tabMap.addEventListener('click', function() {
        tabMap.classList.add('active');
        tabList.classList.remove('active');
        listView.style.display = 'none';
        mapView.style.display = '';
        if (!mapInstance) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    mapInstance = new google.maps.Map(document.getElementById('mapView'), {
                        center: {lat: lat, lng: lng},
                        zoom: 13
                    });
                    new google.maps.Marker({
    position: {lat: lat, lng: lng},
    map: mapInstance,
    title: "Vous êtes ici",
    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
});
                }, function(error) {
                    alert("Impossible d'obtenir votre position : " + error.message);
                });
            } else {
                alert("La géolocalisation n'est pas supportée sur votre navigateur.");
            }
        }
    });
});
