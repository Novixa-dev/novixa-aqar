import 'flowbite/dist/flowbite.js';

// Leaflet (maps), three.js (the 3D model viewer) and <model-viewer> are each
// used on one or two pages but together made up most of the bundle every page
// downloaded. They now load on demand, as separate chunks.
window.loadLeaflet = () => import('./leaflet.js').then((module) => module.default);
window.loadModel3DViewer = () => import('./model-3d-viewer.js').then((module) => module.Model3DViewer);

// <model-viewer> is a custom element: elements already in the page upgrade as
// soon as its definition loads.
if (document.querySelector('model-viewer')) {
    import('@google/model-viewer');
}
