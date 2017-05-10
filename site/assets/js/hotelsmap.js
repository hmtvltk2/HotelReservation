function init_map(tmapId,hotels,markerImage,marker2Path,pinImagePath){
    var mapOptions = {
        zoom: 2,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    var mapdiv = document.getElementById("hotelMap-"+tmapId);
    if(mapdiv){
    mapdiv.style.width = '100%';
    mapdiv.style.height = '600px';
    var map = new google.maps.Map(mapdiv, mapOptions);
    setMarkers(map,hotels,markerImage,marker2Path,pinImagePath);
    }
}

function setMarkers(map, locations,markerImage,marker2Path,pinImagePath) {
    // Add markers to the map

    // Marker sizes are expressed as a Size of X,Y
    // where the origin of the image (0,0) is located
    // in the top left of the image.

    // Origins, anchor positions and coordinates of the marker
    // increase in the X direction to the right and in
    // the Y direction down.


    var bounds = new google.maps.LatLngBounds();

    var markers = [];

    for (var i = 0; i < locations.length; i++) {
        var hotel = locations[i];
        var markerMap;
        if(hotel[6] == "1"){
            markerMap = marker2Path
        }else if(hotel[6] == "0"){
            markerMap = markerImage;
        }else{
            //default case
            markerMap = markerImage;
        }
        var image = new google.maps.MarkerImage(
            markerMap,
            new google.maps.Size(32, 32),
            new google.maps.Point(0,0),
            new google.maps.Point(15, 30));


        var shape = {
            coord: [1, 1, 1, 20, 18, 20, 18 , 1],
            type: 'poly'
        };


        if(hotel[5] != '0') {
            pinImage = new google.maps.MarkerImage(hotel[5],
                // This marker is 20 pixels wide by 32 pixels tall.
                new google.maps.Size(22, 32),
                // The origin for this image is 0,0.
                new google.maps.Point(0,0),
                // The anchor for this image is the base of the flagpole at 0,32.
                new google.maps.Point(0, 32));
        }else{
            var ms_ie = false;
            var ua = window.navigator.userAgent;
            var old_ie = ua.indexOf('MSIE ');
            var new_ie = ua.indexOf('Trident/');

            if ((old_ie > -1) || (new_ie > -1)) {
                ms_ie = true;
            }

            if ( ms_ie ) {

                pinImage = new google.maps.MarkerImage(marker2Path,
                    // This marker is 20 pixels wide by 32 pixels tall.
                    new google.maps.Size(32, 32),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 0,32.
                    new google.maps.Point(0, 32));
            }
        }

        var myLatLng = new google.maps.LatLng(hotel[1], hotel[2]);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: image,
            shape: shape,
            title: hotel[0],
            zIndex: hotel[3]
        });

        markers.push(marker);

        var contentBody = hotel[4];
        var infowindow = new google.maps.InfoWindow({
            content: contentBody,
            maxWidth: 200
        });


        google.maps.event.addListener(marker, 'click', function(contentBody) {
            return function(){
                infowindow.setContent(contentBody);//set the content
                infowindow.open(map,this);
            }
        }(contentBody));

        bounds.extend(myLatLng);
    }
    var markerCluster = new MarkerClusterer(map, markers,mcOptions);
    var pinImage = new google.maps.MarkerImage(pinImagePath,
        new google.maps.Size(32, 42),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    
    bounds.extend(myLatLng);
    map.fitBounds(bounds);

    var listener = google.maps.event.addListener(map, "idle", function() {
        if (map.getZoom() > 16) map.setZoom(16);
        google.maps.event.removeListener(listener);
    });
}
