<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 
<script>    
    function initMap(latitude,longitude){
        var ac = new google.maps.places.Autocomplete(document.getElementById('exampleInputaddress'));
        if((latitude == undefined || longitude == undefined) || (latitude == null || longitude == null)){
            var latitude = "26.8289443";
            var longitude = "75.8056178";  
        }
        ac.addListener('place_changed', () => {
                $('#map').show()
                var place = ac.getPlace();
                const latitude = place.geometry.location.lat();
                const longitude = place.geometry.location.lng();
                
                $('#lat').val(latitude)
                $('#lng').val(longitude)
                var latLng = new google.maps.LatLng(latitude,longitude);
                var mapOptions = {
                center: latLng,
                zoom: 10,
                zoomControl:true,
                scrollwheel:true,
                disableDoubleClickZoom:true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map(document.getElementById("map"), mapOptions);
                var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: "Location Marker"
            });
        });
        var latLng = new google.maps.LatLng(latitude,longitude);
        var mapOptions = {
        center: latLng,
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map"), mapOptions);
        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: "Location Marker"
        });
    }
</script>