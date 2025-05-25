showNewNotifications(notifications) {
    // Detectar nuevas notificaciones (no en caché)
    const newNotifications = notifications.filter(notif => 
        !this.notificationsCache.some(cached => 
            cached.ID_Notificacion === notif.ID_Notificacion
        )
    );
    
    if (newNotifications.length === 0) return;
    
    // Mostrar notificaciones nuevas
    newNotifications.forEach(notif => {
        // Mostrar toast usando la instancia global
        if (window.notificationSystem) {
            // Mostrar toast con nombre de sucursal
            window.notificationSystem.showToast(
                `${notif.NombreSucursal}: ${notif.Mensaje}`,
                this.getNotificationTypeConfig(notif.Tipo).toastType
            );
            
            // Mostrar notificación nativa si tenemos permiso
            if (window.notificationSystem.hasPermission) {
                window.notificationSystem.showSystemNotification(
                    this.getNotificationTitle(notif.Tipo),
                    { 
                        body: `${notif.NombreSucursal}: ${notif.Mensaje}`,
                        tag: `notif-${notif.ID_Notificacion}`
                    }
                );
            }
        } else {
            console.warn('Sistema de notificaciones no disponible');
        }
    });
} 