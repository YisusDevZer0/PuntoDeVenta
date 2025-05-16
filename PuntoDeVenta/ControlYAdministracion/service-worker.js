// Versión del service worker
const SW_VERSION = '1.0.0';

// Service Worker para manejar notificaciones push

// Caché name
const CACHE_NAME = 'punto-venta-cache-v1';

// Assets a cachear
const urlsToCache = [
  '/',
  '/index.php',
  '/css/style.css',
  '/js/script.js',
  '/img/logo.png'
];

// Evento de instalación
self.addEventListener('install', function(event) {
  console.log('Service Worker instalado versión:', SW_VERSION);
  self.skipWaiting(); // Asegurar que se active inmediatamente
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Cache abierto');
        return cache.addAll(urlsToCache);
      })
      .catch(function(err) {
        console.error('Error en caché', err);
      })
  );
});

// Evento de activación
self.addEventListener('activate', function(event) {
  console.log('Service Worker activado versión:', SW_VERSION);
  return self.clients.claim(); // Tomar el control de los clientes inmediatamente
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.filter(function(cacheName) {
          return cacheName !== CACHE_NAME;
        }).map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    })
  );
});

// Manejar eventos push
self.addEventListener('push', function(event) {
  console.log('Notificación push recibida:', event);
  
  let data = { 
    mensaje: 'Nueva notificación', 
    tipo: 'sistema',
    url: self.registration.scope
  };
  
  try {
    if (event.data) {
      data = event.data.json();
    }
  } catch (error) {
    console.error('Error al procesar datos de la notificación:', error);
  }
  
  // Configurar la notificación
  const options = {
    body: data.mensaje,
    icon: '/img/notification-icon.png',
    badge: '/img/notification-badge.png',
    vibrate: [100, 50, 100],
    data: {
      url: data.url || self.registration.scope,
      timestamp: new Date().getTime(),
      tipo: data.tipo
    },
    actions: [
      {
        action: 'open',
        title: 'Ver detalles'
      },
      {
        action: 'close',
        title: 'Cerrar'
      }
    ],
    // Cerrar automáticamente después de 30 segundos
    requireInteraction: false,
    silent: false
  };

  // Personalizar título según el tipo
  let title = 'Punto de Venta';
  
  switch (data.tipo) {
    case 'inventario':
      title = 'Alerta de Inventario';
      options.icon = '/img/inventory-icon.png';
      break;
    case 'caducidad':
      title = 'Alerta de Caducidad';
      options.icon = '/img/expiry-icon.png';
      break;
    case 'caja':
      title = 'Alerta de Caja';
      options.icon = '/img/cash-icon.png';
      break;
    case 'venta':
      title = 'Alerta de Venta';
      options.icon = '/img/sales-icon.png';
      break;
  }

  // Mostrar la notificación
  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Manejar clics en notificaciones
self.addEventListener('notificationclick', function(event) {
  console.log('Se hizo clic en notificación', event);
  
  // Cerrar la notificación
  event.notification.close();
  
  // Manejar acción
  if (event.action === 'open') {
    // Abrir la URL asociada a la notificación
    const urlToOpen = event.notification.data?.url || self.registration.scope;
    
    event.waitUntil(
      clients.matchAll({
        type: 'window',
        includeUncontrolled: true
      }).then(function(clientList) {
        // Verificar si ya hay una ventana abierta y enfocarla
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
  }
});

// Manejar el cierre de notificaciones
self.addEventListener('notificationclose', function(event) {
  console.log('Notificación cerrada', event);
}); 