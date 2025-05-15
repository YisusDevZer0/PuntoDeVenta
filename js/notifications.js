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

        // Registrar service worker para notificaciones push
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registrado:', registration);
            } catch (error) {
                console.error('Error al registrar Service Worker:', error);
            }
        }
    }

    // Mostrar notificación toast
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${this.getTypeTitle(type)}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;

        const container = document.querySelector('.toast-container') || this.createToastContainer();
        container.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: duration
        });
        bsToast.show();

        // Limpiar el toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Mostrar notificación nativa del sistema
    showSystemNotification(title, options = {}) {
        if (!this.hasPermission) {
            this.showToast('No tenemos permiso para mostrar notificaciones del sistema', 'warning');
            return;
        }

        const defaultOptions = {
            icon: '/assets/img/logo.png',
            badge: '/assets/img/logo.png',
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

    // Enviar notificación push (requiere configuración del servidor)
    async sendPushNotification(userId, title, options = {}) {
        try {
            const response = await fetch('/api/notifications/push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userId,
                    title,
                    options
                })
            });

            if (!response.ok) {
                throw new Error('Error al enviar notificación push');
            }

            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Error al enviar notificación push', 'error');
        }
    }

    // Utilidades privadas
    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
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

// Exportar una instancia única del sistema de notificaciones
const notificationSystem = new NotificationSystem();
export default notificationSystem; 