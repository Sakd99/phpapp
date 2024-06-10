<x-filament-panels::page>
    <div id="map" class="w-full rounded-lg" style=" height: 500px;"
    ></div>


    <script>
        function initMap() {
            var centerCoords = {lat: 33.32773314982571, lng: 44.408398854064906};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: centerCoords
            });

            // Generate random markers near the center
            var numMarkers = 10;
            var markers = [];
            for (var i = 0; i < numMarkers; i++) {
                var lat = centerCoords.lat + (Math.random() - 0.5) / 10; // Random offset within 0.05 latitude
                var lng = centerCoords.lng + (Math.random() - 0.5) / 10; // Random offset within 0.05 longitude
                var marker = new google.maps.Marker({
                    position: {lat: lat, lng: lng},
                    map: map
                });
                markers.push(marker);
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google.maps.key')}}&callback=initMap"
            async defer></script>


</x-filament-panels::page>
