@php
                $ShipmentStopIndexCount = $shipment->ShipmentStop->count()-1;
            @endphp
            <script>
                function initMap() {
                    const directionsService = new google.maps.DirectionsService();
                    const directionsDisplay = new google.maps.DirectionsRenderer();
        
                    const map = new google.maps.Map(document.getElementById('map'), {
                        // center: { lat: 26.855907, lng: 75.809552 },
                        zoom: 15
                    });
                    const userLocation = {
                        lat: {{$shipment->shipmentDriverScheduleDetails->truckDriver->current_lat ?? 'null'}},
                        lng: {{$shipment->shipmentDriverScheduleDetails->truckDriver->current_lng ?? 'null'}}
                    };
                    const userMarker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: '{{$shipment->shipmentDriverScheduleDetails->current_location}}',
                        icon: {
                            url: '{{  $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail ?->driver_picture ?  Config('constants.DRIVER_PICTURE_PATH') . $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail->driver_picture : 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png'}}', // Use the custom user image as the marker icon
                            scaledSize: new google.maps.Size(50, 50) // Set the desired width and height
                        }
        
                    });
        
                
                    // Apply the custom-styled-marker class to the marker's icon element
                    userMarker.getIcon().url = ''; // Clear the icon URL
                    userMarker.getIcon().size = new google.maps.Size(50, 50);
                    userMarker.getIcon().origin = new google.maps.Point(0, 0);
                    userMarker.getIcon().anchor = new google.maps.Point(16, 16); // Adjust the anchor point to the center
                    userMarker.setZIndex(1000); // Ensure the marker is displayed above other elements
                    userMarker.setLabel(null); // Remove the default label
                    userMarker.setOpacity(1); // Set the opacity to 1 (fully visible)
                    userMarker.getIcon().scaledSize = new google.maps.Size(50, 50);
                    userMarker.getIcon().url = '{{  $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail ?->driver_picture ?  Config('constants.DRIVER_PICTURE_PATH') . $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail->driver_picture : 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png'}}'; // Restore the default marker image URL
        
                    directionsDisplay.setMap(map);
                    const directionsPanel = document.getElementById('directions-panel');
                    directionsDisplay.setPanel(directionsPanel);
                    const startLocation = { lat: {{$shipment->company_latitude}}, lng: {{$shipment->company_longitude}} }; 
                    const endLocation = { lat: {{$shipment->ShipmentStop[$ShipmentStopIndexCount]->dropoff_latitude}}, lng: {{$shipment->ShipmentStop[$ShipmentStopIndexCount]->dropoff_longitude}} }; // Los Angeles, CA
                    @if($shipment->ShipmentStop->count() == 1 )
                        const waypoints = [
                        ]
                    @else
                        const waypoints = [
                            @foreach($shipment->ShipmentStop as $key => $stop )
                                @if($key == $ShipmentStopIndexCount)
                                    @break
                                @endif
                                { location: { lat: {{$stop->dropoff_latitude}}, lng: {{$stop->dropoff_longitude}} } },
                            @endforeach
                        ];
                    @endif
        
                    const request = {
                        origin: startLocation,
                        destination: endLocation,
                        waypoints: waypoints,
                        travelMode: google.maps.TravelMode.DRIVING
                    };
        
                    directionsService.route(request, function(response, status) {
                        if (status === google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                            google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
                                $("#map img[src='https://maps.gstatic.com/mapfiles/transparent.png']:first").parent().addClass('driver-location-div');
                                $("#map img[src='{{  $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail ?->driver_picture ?  Config('constants.DRIVER_PICTURE_PATH') . $shipment->shipmentDriverScheduleDetails->truckDriver->userDriverDetail->driver_picture : 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png'}}']:first").parent().addClass('driver-location-div-img');
                            });
                        } else {
                            console.error('Directions request failed: ' + status);
                        }
                    });
                }
            </script>
            <style>
                /* Define a class for the custom-styled marker */
                .custom-styled-marker {
                    width: 32px; /* Set the width of the marker */
                    height: 32px; /* Set the height of the marker */
                    border-radius: 50%; /* Create a round border */
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Add a drop shadow */
                    overflow: hidden; /* Clip the square marker to the rounded shape */
                }

                /* Style the marker image inside the custom-styled-marker class */
                .custom-styled-marker img {
                    width: 100%; /* Ensure the image fills the rounded container */
                    height: 100%; /* Ensure the image fills the rounded container */
                }
            </style>
            <!-- Include the Google Maps JavaScript API with your API key -->
            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap"></script>