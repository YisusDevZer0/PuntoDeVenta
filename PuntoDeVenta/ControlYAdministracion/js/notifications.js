// Sistema de Notificaciones
class NotificationSystem {
    constructor() {
        this.hasPermission = false;
        this.init();
    }

    async init() {
        // Verificar si el navegador soporta notificaciones
        if (!("Notification" in window)) {
            console.log("Este navegador no soporta notificaciones del sistema");
            return;
        }

        // Verificar permisos existentes
        if (Notification.permission === "granted") {
            this.hasPermission = true;
        } else if (Notification.permission !== "denied") {
            // Solicitar permisos si no están denegados
            const permission = await Notification.requestPermission();
            this.hasPermission = permission === "granted";
        }

        // Cargar estilos CSS si no están cargados
        this.loadCSS();
    }

    // Cargar CSS de notificaciones
    loadCSS() {
        if (!document.getElementById('notifications-styles')) {
            const link = document.createElement('link');
            link.id = 'notifications-styles';
            link.rel = 'stylesheet';
            link.href = 'css/notifications.css';
            document.head.appendChild(link);
        }
    }

    // Mostrar notificación toast
    showToast(message, type = 'info', duration = 3000) {
        // Crear contenedor de toasts si no existe
        const container = document.querySelector('.toast-container') || this.createToastContainer();
        
        // Crear elemento toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${this.getTypeTitle(type)}</strong>
                <button type="button" class="btn-close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        // Agregar al contenedor
        container.appendChild(toast);
        
        // Mostrar toast
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Configurar cierre automático
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
        
        // Manejar botón de cierre
        const closeBtn = toast.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            });
        }
    }

    // Mostrar notificación nativa del sistema
    showSystemNotification(title, options = {}) {
        if (!this.hasPermission) {
            return;
        }

        const defaultOptions = {
            icon: 'img/notification-icon.png',
            badge: 'img/notification-badge.png',
            vibrate: [200, 100, 200],
            requireInteraction: false
        };

        const notification = new Notification(title, { ...defaultOptions, ...options });
        
        // Manejar clic en la notificación
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    }

    // Utilidades privadas
    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    getTypeTitle(type) {
        const titles = {
            'success': '¡Éxito!',
            'error': 'Error',
            'warning': 'Advertencia',
            'info': 'Información'
        };
        return titles[type] || 'Notificación';
    }
}

// Crear una instancia global del sistema de notificaciones
window.notificationSystem = new NotificationSystem();

// Función global para mostrar notificaciones
window.mostrarNotificacion = function(titulo, mensaje, tipo = 'info') {
    window.notificationSystem.showToast(mensaje, tipo);
    
    if (window.notificationSystem.hasPermission) {
        window.notificationSystem.showSystemNotification(titulo, { body: mensaje });
    }
}; 