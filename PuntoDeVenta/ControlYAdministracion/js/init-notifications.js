// Archivo de inicialización para el sistema de notificaciones
// Soluciona problemas de importación de módulos ES6 en navegadores antiguos

// Cargar CSS
document.addEventListener('DOMContentLoaded', function() {
    // Cargar CSS de notificaciones
    if (!document.getElementById('notifications-styles')) {
        const link = document.createElement('link');
        link.id = 'notifications-styles';
        link.rel = 'stylesheet';
        link.href = 'css/notifications.css';
        document.head.appendChild(link);
    }

    // Cargar scripts de manera segura
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = src;
        script.onload = callback;
        document.body.appendChild(script);
    }

    // Inicializar sistema de notificaciones sin depender de módulos ES6
    window.initNotificationSystem = function() {
        // Verificar permisos de notificaciones
        let hasPermission = false;
        
        if ("Notification" in window) {
            if (Notification.permission === "granted") {
                hasPermission = true;
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function(permission) {
                    hasPermission = permission === "granted";
                });
            }
        }
        
        // Clase simplificada para navegadores sin soporte de ES6 modules
        class SimpleNotificationSystem {
            constructor() {
                this.hasPermission = hasPermission;
                this.notificationsCache = [];
                this.loadNotifications();
                
                // Actualizar cada minuto
                setInterval(() => this.loadNotifications(), 60000);
            }
            
            async loadNotifications() {
                try {
                    const response = await fetch('api/get_notificaciones.php');
                    
                    // Verificar si la respuesta es exitosa
                    if (!response.ok) {
                        console.error(`Error HTTP: ${response.status} - ${response.statusText}`);
                        return;
                    }
                    
                    // Verificar el tipo de contenido
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        console.error(`Tipo de contenido no válido: ${contentType}`);
                        console.error('Respuesta:', await response.text());
                        return;
                    }

                    // Analizar la respuesta JSON
                    const data = await response.json();
                    
                    // Verificar si hay errores en la respuesta
                    if (data.error) {
                        console.error('Error en la respuesta del servidor:', data.message);
                        return;
                    }
                    
                    // Verificar que los datos son válidos
                    if (!data.notificaciones || !Array.isArray(data.notificaciones)) {
                        console.error('Formato de datos inválido:', data);
                        return;
                    }
                    
                    // Procesar notificaciones
                    this.updateCounter(data.total || 0);
                    this.updateNotificationMenu(data.notificaciones);
                    this.showNewNotifications(data.notificaciones);
                    
                    // Cache para no mostrar duplicados
                    this.notificationsCache = data.notificaciones;
                } catch (error) {
                    console.error('Error al cargar notificaciones:', error);
                }
            }
            
            updateCounter(count) {
                const counter = document.getElementById('notification-counter');
                if (counter) {
                    counter.textContent = count;
                    counter.style.display = count > 0 ? 'inline-block' : 'none';
                }
            }
            
            updateNotificationMenu(notifications) {
                const container = document.getElementById('notification-list');
                if (!container) return;
                
                container.innerHTML = '';
                
                if (notifications.length === 0) {
                    container.innerHTML = '<div class="dropdown-item text-center">No hay notificaciones</div>';
                    return;
                }
                
                notifications.forEach(notif => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'dropdown-item d-flex align-items-center';
                    item.dataset.id = notif.ID_Notificacion;
                    
                    // Config para íconos
                    const config = this.getNotificationConfig(notif.Tipo);
                    
                    item.innerHTML = `
                        <div class="mr-3">
                            <div class="icon-circle bg-${config.color}">
                                <i class="fas fa-${config.icon} text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">hace ${notif.TiempoTranscurrido}</div>
                            <span class="font-weight-bold">${notif.Mensaje}</span>
                        </div>
                    `;
                    
                    // Marcar como leída al hacer clic
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.markAsRead(notif.ID_Notificacion);
                    });
                    
                    container.appendChild(item);
                });
                
                // Enlace "Ver todas"
                const viewAll = document.createElement('a');
                viewAll.className = 'dropdown-item text-center small text-gray-500';
                viewAll.href = 'GestionNotificaciones.php';
                viewAll.textContent = 'Ver todas las notificaciones';
                container.appendChild(viewAll);
            }
            
            showNewNotifications(notifications) {
                // Solo para nuevas notificaciones
                const newOnes = notifications.filter(notif => 
                    !this.notificationsCache.some(cached => 
                        cached.ID_Notificacion === notif.ID_Notificacion
                    )
                );
                
                if (newOnes.length === 0) return;
                
                newOnes.forEach(notif => {
                    // Toast en pantalla
                    this.showToast(notif.Mensaje, this.getNotificationConfig(notif.Tipo).toastType);
                    
                    // Notificación nativa si hay permiso
                    if (this.hasPermission) {
                        const title = this.getNotificationTitle(notif.Tipo);
                        const options = { 
                            body: notif.Mensaje,
                            icon: '/PuntoDeVenta/ControlYAdministracion/img/logo.png'
                        };
                        new Notification(title, options);
                    }
                });
            }
            
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
                        this.loadNotifications();
                    }
                } catch (error) {
                    console.error('Error al marcar como leída:', error);
                }
            }
            
            showToast(message, type = 'info', duration = 3000) {
                // Crear contenedor de toasts si no existe
                const container = document.querySelector('.toast-container') || this.createToastContainer();
                
                // Crear elemento toast
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-header">
                        <strong class="me-auto">${this.getNotificationTitle(type)}</strong>
                        <button type="button" class="btn-close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                `;
                
                container.appendChild(toast);
                
                // Mostrar toast
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
                
                // Cerrar al hacer clic
                const closeBtn = toast.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 300);
                    });
                }
            }
            
            createToastContainer() {
                const container = document.createElement('div');
                container.className = 'toast-container';
                document.body.appendChild(container);
                return container;
            }
            
            getNotificationConfig(type) {
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
                const typeLC = typeof type === 'string' ? type.toLowerCase() : 'notificación';
                
                const titles = {
                    'inventario': 'Alerta de Inventario',
                    'caducidad': 'Producto por Caducar',
                    'caja': 'Corte de Caja Pendiente',
                    'venta': 'Venta Importante',
                    'sistema': 'Sistema',
                    'warning': 'Advertencia',
                    'error': 'Error',
                    'success': 'Éxito',
                    'info': 'Información'
                };
                
                return titles[typeLC] || 'Notificación';
            }
        }
        
        // Inicializar y guardar en window para acceso global
        window.notificationSystem = new SimpleNotificationSystem();
        
        // Configurar toggles de menús
        document.addEventListener('click', function(e) {
            const bell = document.getElementById('notification-bell');
            const menu = document.getElementById('notification-dropdown');
            
            if (!bell || !menu) return;
            
            if (bell.contains(e.target)) {
                menu.classList.toggle('show');
                return;
            }
            
            if (!menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
        
        // Asegurar que existan elementos UI necesarios
        if (!document.getElementById('notification-bell')) {
            const navbar = document.querySelector('.navbar-nav') || document.querySelector('nav ul');
            
            if (navbar) {
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
                
                navbar.insertBefore(notifElement, navbar.lastElementChild);
            }
        }
    };

    // Iniciar sistema
    initNotificationSystem();
}); 