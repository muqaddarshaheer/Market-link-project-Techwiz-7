<div id="{{ $id ?? 'map' }}" class="map-box"></div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const points = @json($points);
        if (!points.length || typeof L === 'undefined') return;
        const map = L.map(@json($id ?? 'map')).setView([points[0].lat, points[0].lng], points.length > 1 ? 12 : 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        const bounds = [];
        points.forEach(p => {
            const marker = L.marker([p.lat, p.lng]).addTo(map);
            const dir = `https://www.openstreetmap.org/directions?to=${p.lat}%2C${p.lng}`;
            marker.bindPopup(`<div class="map-pop"><strong>${p.title}</strong><div>${p.subtitle || ''}</div><a target="_blank" rel="noopener" href="${dir}">Open directions</a></div>`);
            bounds.push([p.lat, p.lng]);
        });
        if (bounds.length > 1) map.fitBounds(bounds, {padding: [24, 24]});
        setTimeout(() => map.invalidateSize(), 200);
        if (@json($id ?? 'map') === 'picker') {
            map.on('click', (e) => {
                const lat = document.getElementById('lat');
                const lng = document.getElementById('lng');
                if (!lat || !lng) return;
                lat.value = e.latlng.lat.toFixed(6);
                lng.value = e.latlng.lng.toFixed(6);
                L.marker(e.latlng).addTo(map);
            });
        }
    });
</script>
@endpush
