const CACHE_NAME = 'punto-de-venta-cache-v1';
const urlsToCache = [
  '/PuntoDeVenta/PuntoDeVentaFarmacias/RealizarVentas.php',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Intentando cachear recursos:', urlsToCache);
        return cache.addAll(urlsToCache);
      })
      .catch((error) => {
        console.error('Error al cachear recursos:', error);
      })
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        if (response) {
          console.log('Recurso encontrado en caché:', event.request.url);
          return response;
        }
        console.log('Recurso no encontrado en caché, solicitando al servidor:', event.request.url);
        return fetch(event.request);
      })
  );
});