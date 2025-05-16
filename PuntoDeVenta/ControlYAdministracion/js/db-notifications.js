// Integración del sistema de notificaciones con la base de datos
// Nota: Usa el objeto notificationSystem disponible globalmente

class DatabaseNotifications {
    constructor() {
        this.notificationsCache = [];
        this.updateInterval = 60000; // 1 minuto
        this.init();
    }

    init() {
        this.setupMenuToggle();
        this.loadNotifications();
        
        // Actualizar periódicamente
        setInterval(() => this.loadNotifications(), this.updateInterval);
        
        // Agregar contador y contenedor si no existen
        this.ensureUIElements();
    }

    // Cargar notificaciones desde el servidor
    async loadNotifications() {
        try {
            const response = await fetch('api/get_notificaciones.php');
            const data = await response.json();
            
            this.updateCounter(data.total);
            this.updateNotificationMenu(data.notificaciones);
            this.showNewNotifications(data.notificaciones);
            
            // Actualizar caché
            this.notificationsCache = data.notificaciones;
        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
        }
    }

    // Actualizar contador de notificaciones
    updateCounter(count) {
        const counter = document.getElementById('notification-counter');
        if (counter) {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    // Actualizar menú desplegable de notificaciones
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
                    <div class="small text-gray-500">hace ${notif.TiempoTranscurrido}</div>
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

    // Mostrar nuevas notificaciones
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
                // Mostrar toast
                window.notificationSystem.showToast(
                    notif.Mensaje,
                    this.getNotificationTypeConfig(notif.Tipo).toastType
                );
                
                // Mostrar notificación nativa si tenemos permiso
                if (window.notificationSystem.hasPermission) {
                    window.notificationSystem.showSystemNotification(
                        this.getNotificationTitle(notif.Tipo),
                        { 
                            body: notif.Mensaje,
                            tag: `notif-${notif.ID_Notificacion}`
                        }
                    );
                }
            } else {
                console.warn('Sistema de notificaciones no disponible');
            }
        });
    }

    // Marcar notificación como leída
    async markAsRead(id) {
        try {
            const formData = new FormData();
            formData.append('id', id);
            
            const response = await fetch('api/marcar_notificacion_leida.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Recargar notificaciones
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error al marcar como leída:', error);
        }
    }

    // Utilidades
    getNotificationTypeConfig(type) {
        const typeLC = type ? type.toLowerCase() : 'sistema';
        
        const configs = {
            'inventario': { 
                icon: 'box', 
                color: 'warning', 
                toastType: 'warning' 
            },
            'caducidad': { 
                icon: 'calendar', 
                color: 'danger', 
                toastType: 'error' 
            },
            'caja': { 
                icon: 'cash-register', 
                color: 'info', 
                toastType: 'info' 
            },
            'venta': { 
                icon: 'tags', 
                color: 'success', 
                toastType: 'success' 
            },
            'sistema': { 
                icon: 'cog', 
                color: 'primary', 
                toastType: 'info' 
            }
        };
        
        return configs[typeLC] || { 
            icon: 'bell', 
            color: 'primary', 
            toastType: 'info' 
        };
    }

    getNotificationTitle(type) {
        const typeLC = type ? type.toLowerCase() : 'sistema';
        
        const titles = {
            'inventario': 'Alerta de Inventario',
            'caducidad': 'Producto por Caducar',
            'caja': 'Corte de Caja Pendiente',
            'venta': 'Venta Importante',
            'sistema': 'Sistema'
        };
        
        return titles[typeLC] || 'Notificación';
    }

    // Asegurar que existan los elementos UI necesarios
    ensureUIElements() {
        // Verificar si ya existe el elemento de notificaciones en la navbar
        if (!document.getElementById('notification-bell')) {
            this.createNotificationElements();
        }
    }
    
    // Setup para mostrar/ocultar menú de notificaciones
    setupMenuToggle() {
        document.addEventListener('click', function(e) {
            const bell = document.getElementById('notification-bell');
            const menu = document.getElementById('notification-dropdown');
            
            if (!bell || !menu) return;
            
            // Si se hizo clic en la campana, alternar menú
            if (bell.contains(e.target)) {
                menu.classList.toggle('show');
                return;
            }
            
            // Si se hizo clic fuera del menú, ocultarlo
            if (!menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    }
    
    // Crear elementos UI para notificaciones si no existen
    createNotificationElements() {
        // Buscar la navbar
        const navbar = document.querySelector('.navbar-nav') || document.querySelector('nav ul');
        
        if (!navbar) {
            console.warn('No se encontró la navbar para insertar el elemento de notificaciones');
            return;
        }
        
        // Crear elemento de notificaciones
        const notifElement = document.createElement('li');
        notifElement.className = 'nav-item dropdown no-arrow mx-1';
        notifElement.innerHTML = `
            <a class="nav-link dropdown-toggle" href="#" id="notification-bell" role="button">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter" id="notification-counter">0</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
                id="notification-dropdown" aria-labelledby="notification-bell">
                <h6 class="dropdown-header">
                    Centro de Notificaciones
                </h6>
                <div id="notification-list">
                    <div class="dropdown-item text-center">Cargando...</div>
                </div>
            </div>
        `;
        
        navbar.appendChild(notifElement);
    }
}

// Crear una instancia global
window.dbNotifications = new DatabaseNotifications();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // La instancia ya está creada, pero podemos hacer setup adicional aquí
    console.log('Sistema de notificaciones de base de datos inicializado');
}); 