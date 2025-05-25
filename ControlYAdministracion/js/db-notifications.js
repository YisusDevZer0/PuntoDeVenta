updateNotificationMenu(notifications) {
    const container = document.getElementById('notification-list');
    if (!container) return;

    // Limpiar contenedor
    container.innerHTML = '';

    if (notifications.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-center">No hay notificaciones</div>';
        return;
    }

    // Agregar notificaciones al menú
    notifications.forEach(notif => {
        const item = document.createElement('a');
        item.href = '#';
        item.className = 'dropdown-item d-flex align-items-center';
        item.dataset.id = notif.ID_Notificacion;
        
        // Configurar icono según tipo
        const iconConfig = this.getNotificationTypeConfig(notif.Tipo);
        
        item.innerHTML = `
            <div class="mr-3">
                <div class="icon-circle bg-${iconConfig.color}">
                    <i class="fas fa-${iconConfig.icon} text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-gray-500">
                    <span class="font-weight-bold">${notif.NombreSucursal}</span> - hace ${notif.TiempoTranscurrido}
                </div>
                <span class="font-weight-bold">${notif.Mensaje}</span>
            </div>
        `;
        
        // Evento para marcar como leída
        item.addEventListener('click', (e) => {
            e.preventDefault();
            this.markAsRead(notif.ID_Notificacion);
        });
        
        container.appendChild(item);
    });
    
    // Agregar enlace "Ver todas"
    const viewAll = document.createElement('a');
    viewAll.className = 'dropdown-item text-center small text-gray-500';
    viewAll.href = 'GestionNotificaciones.php';
    viewAll.textContent = 'Ver todas las notificaciones';
    container.appendChild(viewAll);
} 