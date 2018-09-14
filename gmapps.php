<!DOCTYPE html>
<html>
  <head>
    <title>Google Maps - Endereço com draggable e search input :)</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
    </style>
</head>
<body>
<div class="container">
	 
	  <div class="row container">
	             	Endereço: <input id="endereco" class="controls" type="text" placeholder="digite seu endereço" style="width:700px;"><br>
					Latitude: <input type="text" class="controls" name="latitude" id="latitude">
	             	Longitude: <input type="text" class="controls" name="longitude" id="longitude">	             	
	                
				<br><br>
			    <div id="map" style="height: 500px;width: 800px"></div>
				<br>			    
	      		</div>
	    </div>	
</div>
<script>
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

        var marker;
        var infowindow;
        var geocoder;
        var map = null;
        var input;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -12.837151, lng:  -42.022961},
                zoom: 14,
                streetViewControl: false,
                gestureHandling: 'greedy'
            });

            input = /** @type {!HTMLInputElement} */(
                document.getElementById('endereco'));

            infowindow = new google.maps.InfoWindow(); // janela de informação
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(latLng);

                    marker = new google.maps.Marker({
                        position: latLng,
                        title: 'Arraste para o seu endereço.',
                        animation: google.maps.Animation.DROP,
                        map: map,
                        draggable: true
                    });

                    infowindow.setContent('<div><strong>Você pode pesquisar seu endereço ou arrastar o marcador para ele.</strong><br>');
                    infowindow.open(map, marker);

                    geocoder = new google.maps.Geocoder();

                    google.maps.event.addListener(marker, 'dragend', function() {
                        geocoder.geocode({latLng: marker.getPosition()}, function(responses) {
                            if (responses && responses.length > 0) {
                                infowindow.setContent(
                                    "<div class='place'> <strong>" + responses[0].formatted_address
                                    + "<br /></strong> <small>"
                                    + "Latitude: " + marker.getPosition().lat() + "<br>"
                                    + "Longitude: " + marker.getPosition().lng() + "</small></div>"
                                );

                                $("#latitude").val(marker.getPosition().lat());
                                $("#longitude").val(marker.getPosition().lng());
                                $("#endereco").val(responses[0].formatted_address);



                                infowindow.open(map, marker);
                            } else {
                                alert('Error: Google Maps could not determine the address of this location.');
                                infowindow.setContent(
                                    "Erro ao obter endereço!"
                                );

                                $("#latitude").val(null);
                                $("#longitude").val(null);
                                $("#endereco").val(null);
                            }
                        });

                    });
                },
                function errorCallback(error) {
                    console.log(error)
                },
                {
                    maximumAge:6000,
                    timeout:5000
                }
            );

            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("Nenhum endereço disponível para: '" + place.name + "'");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(14);  // Why 17? Because it looks good.
                }
                /* marker.setIcon(/** @type {google.maps.Icon} ({
                   url: place.icon,
                   size: new google.maps.Size(71, 71),
                   origin: new google.maps.Point(0, 0),
                   anchor: new google.maps.Point(17, 34),
                   scaledSize: new google.maps.Size(35, 35)
                 }));*/
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                var item_Lat = place.geometry.location.lat();
                var item_Lng = place.geometry.location.lng();
                var item_Location = place.formatted_address;
//alert("Lat= "+item_Lat+"_____Lang="+item_Lng+"_____Location="+item_Location);
                $("#latitude").val(item_Lat);
                $("#longitude").val(item_Lng);
                $("#endereco").val(item_Location);

                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }

                infowindow.setContent(
                    "<div class='place'> <strong>" + address
                    + "<br /></strong> <small>"
                    + "Latitude: " + item_Lat + "<br>"
                    + "Longitude: " + item_Lng + "</small></div>"
                );
                infowindow.open(map, marker);
            });
            autocomplete.setTypes([]);
		}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCR5PFyvraK8Cqbu-vQu7UAR-NkcABHNuw&libraries=places&callback=initMap"
        async defer></script>
</body>
</html>
