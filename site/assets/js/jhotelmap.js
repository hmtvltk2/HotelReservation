function loadJHotelMap(mapData) {
    if (typeof google === 'object' && typeof google.maps === 'object') {
        //library already called
        initializeHotelMap(mapData);
        google.maps.event.addDomListener(window, 'load', initializeHotelMap);
    } else {
        loadScript(mapData);
    }
}

function initializeHotelMap(mapData) {
    var myLatlng = new google.maps.LatLng(mapData['lat'],mapData['long']);
    var myOptions = {
        zoom: 12,
        center: myLatlng,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var mapdiv = document.getElementById(mapData['mapDivId']);


    var map = new google.maps.Map(mapdiv, myOptions);
    var image = mapData['markerIcon'];
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(mapData['lat'],mapData['long']),
        map: map,
        title: mapData['markerTitle'],
        clickable: false,
        icon: image
    });

}
function loadScript(mapData) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement("script");
    script.type = 'text/javascript';
    script.charset = 'utf-8';
    script.src = "https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&callback=initializeHotelMap"+ mapData['map_key'];
    script.defer = true;
    script.async = true;
    script.onload = function () {
        initializeHotelMap(mapData);
        google.maps.event.addDomListener(window, 'load', initializeHotelMap);
    };
    head.appendChild(script);
}