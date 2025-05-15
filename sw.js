// Service Worker para manejar notificaciones push
self.addEventListener('push', function(event) {
    if (!event.data) return;

    const data = event.data.json();
    const options = {
        body: data.body || '',
        icon: data.icon || '/assets/img/logo.png',
        badge: data.badge || '/assets/img/logo.png',
        vibrate: [200, 100, 200],
        data: data.data || {},
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Manejar clic en la notificación
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action) {
        // Manejar acciones específicas si se definieron
        console.log('Acción seleccionada:', event.action);
    }

    // Enfocar la ventana o abrir una nueva si no está abierta
    event.waitUntil(
        clients.matchAll({type: 'window'}).then(function(clientList) {
            if (clientList.length > 0) {
                let client = clientList[0];
                for (let i = 0; i < clientList.length; i++) {
                    if (clientList[i].focused) {
                        client = clientList[i];
                    }
                }
                return client.focus();
            }
            return clients.openWindow('/');
        })
    );
});

// Manejar la instalación del Service Worker
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('notifications-cache-v1').then(function(cache) {
            return cache.addAll([
                '/assets/img/logo.png',
                '/'
            ]);
        })
    );
});

// Manejar la activación del Service Worker
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName.startsWith('notifications-cache-') &&
                           cacheName !== 'notifications-cache-v1';
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
}); 