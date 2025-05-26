class NotificationSystem {
    constructor() {
        this.notificationList = document.getElementById('notification-list');
        this.notificationCounter = document.getElementById('notification-counter');
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.init();
    }

    init() {
        this.loadNotifications();
        setInterval(() => this.loadNotifications(), 60000);
        this.setupEventListeners();
    }

    setupEventListeners() {
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown();
            });
        }
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
            const response = await fetch('api/get_notificaciones.php', { credentials: 'include' });
            const data = await response.json();
            if (data.success === false || data.error === true) {
                this.showToast('No autorizado para ver notificaciones', 'error');
                return;
            }
            this.updateNotificationList(data.notificaciones || data.notifications || []);
            this.updateNotificationCounter(data.total || data.unread_count || 0);
        } catch (error) {
            this.showToast('No autorizado para ver notificaciones', 'error');
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
                    <span style="font-size: 0.85em; color: #6c757d; font-weight: 500; letter-spacing: 0.5px;">${notif.NombreSucursal || notif.Nombre_Sucursal || ''}</span>
                    <span style="font-size: 1em; color: #222; font-weight: 500;">${notif.Mensaje || notif.mensaje || ''}</span>
                </div>
            `;
            item.addEventListener('click', () => this.markAsRead(notif.ID_Notificacion || notif.id));
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
            await fetch('api/marcar_notificacion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notification_id: notificationId })
            });
            this.loadNotifications();
        } catch (error) {}
    }

    showToast(message, type = 'info', duration = 3000) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.position = 'fixed';
            container.style.bottom = '30px';
            container.style.right = '30px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        while (container.children.length >= 3) {
            container.removeChild(container.firstChild);
        }
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.background = '#222';
        toast.style.color = '#fff';
        toast.style.padding = '10px 18px';
        toast.style.marginTop = '10px';
        toast.style.borderRadius = '6px';
        toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
        toast.style.opacity = '0.95';
        toast.style.fontSize = '1em';
        toast.style.transition = 'opacity 0.3s';
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 1000);
        }, duration);
    }
    showSystemNotification() {}
}
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
}); 