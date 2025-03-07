const CACHE_NAME = 'punto-de-venta-cache-v1';
const urlsToCache = [
  '/PuntoDeVenta/PuntoDeVentaFarmacias/styles.css', // Archivo CSS
  '/PuntoDeVenta/PuntoDeVentaFarmacias/js/ControlDeTicketsVentas.js', // Archivo JavaScript
  '/PuntoDeVenta/PuntoDeVentaFarmacias/js/FinalizaLasVentasSucursales.js', // Archivo JavaScript
  '/PuntoDeVenta/PuntoDeVentaFarmacias/js/BuscaDataPacientes.js', // Archivo JavaScript
  '/PuntoDeVenta/PuntoDeVentaFarmacias/js/BusquedaProductos.js', // Archivo JavaScript
  '/PuntoDeVenta/PuntoDeVentaFarmacias/logo.png',  // Imagen
  '/PuntoDeVenta/PuntoDeVentaFarmacias/favicon.ico' // Favicon
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Intentando cachear recursos estáticos:', urlsToCache);
        return cache.addAll(urlsToCache)
          .then(() => {
            console.log('Recursos estáticos cacheados correctamente');
          })
          .catch((error) => {
            console.error('Error al cachear recursos estáticos:', error);
          });
      })
  );
});

self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Estrategia: Cache First para recursos estáticos
  if (url.pathname.endsWith('.css') || url.pathname.endsWith('.js') || url.pathname.endsWith('.png') || url.pathname.endsWith('.ico')) {
    event.respondWith(
      caches.match(event.request)
        .then((response) => {
          if (response) {
            console.log('Recurso estático encontrado en caché:', event.request.url);
            return response;
          }
          console.log('Recurso estático no encontrado en caché, solicitando al servidor:', event.request.url);
          return fetch(event.request);
        })
    );
  }

  // Estrategia: Network First para páginas dinámicas
  else {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          console.log('Página dinámica solicitada al servidor:', event.request.url);
          return response;
        })
        .catch(() => {
          console.log('Error al solicitar la página dinámica, mostrando página de respaldo');
          return caches.match('/PuntoDeVenta/PuntoDeVentaFarmacias/offline.html'); // Página de respaldo
        })
    );
  }
});