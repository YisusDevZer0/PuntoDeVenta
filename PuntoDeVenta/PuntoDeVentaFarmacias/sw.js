const CACHE_NAME = 'punto-de-venta-cache-v1';

self.addEventListener('install', (event) => {
  console.log('Service Worker instalado');
  // No se cachea nada durante la instalación
  event.waitUntil(Promise.resolve());
});

self.addEventListener('fetch', (event) => {
  console.log('Solicitud interceptada:', event.request.url);
  // No se usa la caché, todas las solicitudes se pasan al servidor
  event.respondWith(fetch(event.request));
});