<x-app-layout>
    <div id="map-container" style="position: relative; width: 100vw; height: 95vh; overflow: hidden;">
        <div id="map" style="width: 100%; height: 100%;"></div>
    </div>

    {{-- Leaflet CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map');
            const boundsPoland = L.latLngBounds([[49.0, 14.0], [55.0, 24.5]]);
            map.fitBounds(boundsPoland);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
        });
    </script>
</x-app-layout>
