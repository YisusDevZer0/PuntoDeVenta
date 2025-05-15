self.addEventListener('push', function(event) {
  const data = event.data.json();
  
  const options = {
    body: data.mensaje,
    icon: 'img/notification-icon.png',
    badge: 'img/notification-badge.png',
    data: {
      url: data.url || self.registration.scope
    }
  };

  event.waitUntil(
    self.registration.showNotification('Punto de Venta', options)
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
}); 