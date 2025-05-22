// Versión del service worker
const SW_VERSION = '1.0.0';

// Service Worker para manejar notificaciones push

// Caché name
const CACHE_NAME = 'punto-venta-cache-v1';

// Assets a cachear - Actualizados con rutas relativas correctas
const urlsToCache = [
  './',
  './index.php',
  './css/style.css',
  './js/script.js',
  './img/notification-icon.png',
  './img/notification-badge.png'
];

// Evento de instalación
self.addEventListener('install', function(event) {
  console.log('Service Worker instalado versión:', SW_VERSION);
  self.skipWaiting(); // Asegurar que se active inmediatamente
  
  // Crear carpetas necesarias si no existen
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Cache abierto');
        
        // Usar fetch individual para cada recurso para evitar que falle todo el grupo
        const cachePromises = urlsToCache.map(url => {
          // Intentar cachear cada recurso individualmente
          return fetch(url)
            .then(response => {
              if (!response.ok) {
                throw new Error(`Error al cachear ${url}: ${response.status} ${response.statusText}`);
              }
              return cache.put(url, response);
            })
            .catch(error => {
              console.warn(`No se pudo cachear el recurso ${url}:`, error.message);
              // Continuar con el siguiente recurso, no interrumpir el proceso
              return Promise.resolve();
            });
        });
        
        return Promise.all(cachePromises);
      })
      .catch(function(err) {
        console.error('Error en caché', err);
      })
  );
});

// Evento de activación
self.addEventListener('activate', function(event) {
  console.log('Service Worker activado versión:', SW_VERSION);
  
  // Limpiar cachés antiguos
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.filter(function(cacheName) {
          return cacheName !== CACHE_NAME;
        }).map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    }).then(() => {
      return self.clients.claim(); // Tomar el control de los clientes inmediatamente
    })
  );
});

// Manejar eventos push
self.addEventListener('push', function(event) {
    if (!event.data) {
        console.log('Push event sin datos');
        return;
    }

    try {
        const data = event.data.json();
        console.log('Datos recibidos:', data);

        const options = {
            body: data.mensaje || 'Nueva notificación',
            icon: data.icon || '/ControlYAdministracion/assets/img/logo.png',
            badge: data.badge || '/ControlYAdministracion/assets/img/logo.png',
            data: {
                url: data.url || '/ControlYAdministracion/',
                tipo: data.tipo || 'sistema',
                timestamp: data.timestamp || Date.now()
            },
            actions: data.actions || [
                {
                    action: 'abrir',
                    title: 'Abrir'
                },
                {
                    action: 'cerrar',
                    title: 'Cerrar'
                }
            ],
            requireInteraction: true,
            vibrate: [200, 100, 200]
        };

        event.waitUntil(
            self.registration.showNotification(data.titulo || 'Notificación', options)
        );
    } catch (error) {
        console.error('Error al procesar notificación:', error);
    }
});

// Manejar clic en la notificación
self.addEventListener('notificationclick', function(event) {
    console.log('Notificación clickeada:', event);

    event.notification.close();

    if (event.action === 'cerrar') {
        return;
    }

    // Por defecto, abrir la URL de la notificación
    const urlToOpen = event.notification.data.url || '/ControlYAdministracion/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        .then(function(clientList) {
            // Si hay una ventana abierta, enfocarla
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // Si no hay ventana abierta, abrir una nueva
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Manejar el cierre de notificaciones
self.addEventListener('notificationclose', function(event) {
  console.log('Notificación cerrada', event);
}); 