const CACHE_NAME = 'punto-de-venta-cache-v1';
const urlsToCache = [
  '/PuntoDeVenta/PuntoDeVentaFarmacias/RealizarVentas.php',
  '/PuntoDeVenta/PuntoDeVentaFarmacias/styles.css', // Si tienes un archivo CSS
  '/PuntoDeVenta/PuntoDeVentaFarmacias/scripts.js', // Si tienes un archivo JS
  '/PuntoDeVenta/PuntoDeVentaFarmacias/logo.png'    // Si tienes un logo
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => response || fetch(event.request))
  );
});