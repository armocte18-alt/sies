import L from 'leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const CDMX_CENTER = [19.365, -99.15];

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

/**
 * Inicializa el mapa de ubicación de sucursales.
 * @param {string} elementId
 * @param {{ nombre: string, clave: string, lat: number, lng: number }[]} markers
 */
function initSucursalesMap(elementId, markers) {
    const el = document.getElementById(elementId);

    if (!el || el.dataset.mapInitialized) {
        return;
    }

    el.dataset.mapInitialized = 'true';

    const map = L.map(el, { scrollWheelZoom: false }).setView(CDMX_CENTER, 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    if (markers.length > 0) {
        const group = L.featureGroup();

        markers.forEach((marker) => {
            L.marker([marker.lat, marker.lng])
                .bindPopup(`<strong>${escapeHtml(marker.nombre)}</strong><br>${escapeHtml(marker.clave)}`)
                .addTo(group);
        });

        group.addTo(map);

        const fit = () => map.invalidateSize().fitBounds(group.getBounds().pad(0.2));

        // El contenedor puede no tener su tamaño final (fuentes, layout en
        // grid) en el momento en que Leaflet mide el mapa; sin esto, el
        // encuadre inicial sale descentrado o recortado.
        fit();
        requestAnimationFrame(fit);
        setTimeout(fit, 300);

        if (typeof ResizeObserver !== 'undefined') {
            new ResizeObserver(() => map.invalidateSize()).observe(el);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('sucursales-map');

    if (!el) {
        return;
    }

    let markers = [];

    try {
        markers = JSON.parse(el.dataset.markers || '[]');
    } catch {
        markers = [];
    }

    initSucursalesMap('sucursales-map', markers);
});
