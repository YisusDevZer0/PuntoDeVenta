self.addEventListener('push', function(event) {
  const data = event.data.json();
  
  const options = {
    body: data.mensaje,
    icon: '../images/logo.png',
    badge: '../images/badge.png',
    data: {
      url: data.url || window.location.origin
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