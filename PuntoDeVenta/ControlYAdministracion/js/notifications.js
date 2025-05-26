// Sistema de Notificaciones
class NotificationSystem {
    constructor() {
        this.notificationList = document.getElementById('notification-list');
        this.notificationCounter = document.getElementById('notification-counter');
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        
        this.init();
    }
    
    init() {
        // Cargar notificaciones iniciales
        this.loadNotifications();
        
        // Configurar actualización periódica
        setInterval(() => this.loadNotifications(), 60000); // Actualizar cada minuto
        
        // Configurar eventos
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Manejar clic en el botón de notificaciones
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown();
            });
        }
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!this.notificationBell?.contains(e.target) && 
                !this.notificationDropdown?.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }
    
    toggleDropdown() {
        if (this.notificationDropdown) {
            this.notificationDropdown.classList.toggle('show');
        }
    }
    
    closeDropdown() {
        if (this.notificationDropdown) {
            this.notificationDropdown.classList.remove('show');
        }
    }
    
    async loadNotifications() {
        try {
            const response = await fetch('api/get_notifications.php');
            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationList(data.notifications);
                this.updateNotificationCounter(data.unread_count);
            } else {
                console.error('Error al cargar notificaciones:', data.message);
            }
        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
        }
    }
    
    updateNotificationList(notifications) {
        if (!this.notificationList) return;
        this.notificationList.innerHTML = '';

        if (!notifications || notifications.length === 0) {
            this.notificationList.innerHTML = `
                <div class="dropdown-item text-center text-muted" style="font-size:0.95em;">No hay notificaciones</div>
            `;
            return;
        }

        notifications.forEach(notif => {
            // Diseño minimalista: solo sucursal y mensaje
            const item = document.createElement('div');
            item.className = 'notification-item';
            item.style.padding = '0.7em 1em';
            item.style.borderBottom = '1px solid #f0f0f0';
            item.style.background = 'none';
            item.style.cursor = 'pointer';
            item.style.transition = 'background 0.2s';
            item.onmouseover = () => item.style.background = '#f7f7fa';
            item.onmouseout = () => item.style.background = 'none';

            item.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span style="font-size: 0.85em; color: #6c757d; font-weight: 500; letter-spacing: 0.5px;">${notif.sucursal || notif.Sucursal || ''}</span>
                    <span style="font-size: 1em; color: #222; font-weight: 500;">${notif.mensaje || notif.Mensaje || ''}</span>
                </div>
            `;

            // Marcar como leída al hacer clic
            item.addEventListener('click', () => this.markAsRead(notif.id || notif.ID_Notificacion));

            this.notificationList.appendChild(item);
        });
    }
    
    updateNotificationCounter(count) {
        if (this.notificationCounter) {
            this.notificationCounter.textContent = count;
            this.notificationCounter.style.display = count > 0 ? 'block' : 'none';
        }
    }
    
    async markAsRead(notificationId) {
        try {
            const response = await fetch('api/mark_notification_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ notification_id: notificationId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar la lista y el contador
                this.loadNotifications();
            } else {
                console.error('Error al marcar notificación como leída:', data.message);
            }
        } catch (error) {
            console.error('Error al marcar notificación como leída:', error);
        }
    }
    
    getNotificationConfig(type) {
        const configs = {
            'inventario': { 
                icon: 'box', 
                color: 'warning'
            },
            'caducidad': { 
                icon: 'calendar', 
                color: 'danger'
            },
            'caja': { 
                icon: 'cash-register', 
                color: 'info'
            },
            'venta': { 
                icon: 'tags', 
                color: 'success'
            },
            'sistema': { 
                icon: 'cog', 
                color: 'primary'
            }
        };
        
        return configs[type?.toLowerCase()] || { 
            icon: 'bell', 
            color: 'primary'
        };
    }
}

// Inicializar el sistema de notificaciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
}); 