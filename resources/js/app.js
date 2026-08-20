import 'flowbite/dist/flowbite.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import './model-3d-viewer.js';
import '@google/model-viewer';

// Leaflet's default marker icon path is resolved relative to the current
// page URL, not the bundled asset location, so it 404s on any route deeper
// than the site root (e.g. /properties/search). Point it at the actual
// bundled asset URLs Vite produced for these images instead.
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});
