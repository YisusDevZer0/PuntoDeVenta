<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <!-- <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Search">
                </form> -->
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item">
                        <a href="Mensajes" class="nav-link">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Mensajes</span>
                        </a>
                    </div>
                    <div class="nav-item dropdown" id="notification-nav-item">
                        <a href="#" class="nav-link dropdown-toggle" id="notification-bell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificaciones</span>
                            <span class="badge badge-danger badge-counter" id="notification-counter">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" id="notification-dropdown" aria-labelledby="notification-bell">
                            <h6 class="dropdown-header bg-primary text-white">
                                Centro de Notificaciones
                            </h6>
                            <div id="notification-list" class="notification-list">
                                <div class="dropdown-item text-center">Cargando notificaciones...</div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="GestionNotificaciones.php" class="dropdown-item text-center small text-gray-500">
                                Ver todas las notificaciones
                            </a>
                        </div>
                    </div>
                    <div class="nav-item dropdown" id="clock-nav-item">
                        <a href="#" class="nav-link dropdown-toggle" id="clock-bell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-clock me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Asistencias</span>
                            <span class="badge badge-primary badge-counter" id="clock-counter">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" id="clock-dropdown" aria-labelledby="clock-bell">
                            <h6 class="dropdown-header bg-primary text-white">
                                Registro de Asistencias
                            </h6>
                            <div id="clock-list" class="clock-list">
                                <div class="dropdown-item text-center">Cargando registros...</div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="GestionAsistencias.php" class="dropdown-item text-center small text-gray-500">
                                Ver historial de asistencias
                            </a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $row['Nombre_Apellidos']?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="MiPerfilDeUsuarioYMas" class="dropdown-item">Mi perfil</a>
                            <!-- <a href="Ajustes" class="dropdown-item">Ajustes</a> -->
                            <a href="Cierre" class="dropdown-item">Cerrar sesion</a>
                        </div>
                    </div>
                </div>
            </nav>
            <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona el enlace de cierre de sesión
        const logoutLink = document.querySelector('.dropdown-item[href="Cierre"]');
        
        // Agrega un evento de clic al enlace
        logoutLink.addEventListener('click', function(event) {
            // Previene el comportamiento predeterminado del enlace
            event.preventDefault();
            
            // Muestra el SweetAlert2 para confirmar el cierre de sesión
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro de que deseas cerrar sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // Si el usuario confirma, redirige a la página de cierre de sesión
                if (result.isConfirmed) {
                    window.location.href = "cerrar_sesion.php"; // Reemplaza con la URL de tu página de cierre de sesión
                }
            });
        });
    });
</script>

<!-- Estilos para ambos sistemas -->
<style>
    /* Estilos comunes */
    .nav-link {
        position: relative;
        display: inline-block;
    }
    
    .badge-counter {
        position: absolute;
        transform: scale(0.7);
        transform-origin: top right;
        right: 0.25rem;
        top: 0.25rem;
        color: white;
        font-size: 0.75rem;
        padding: 0.25em 0.4em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.35rem;
    }
    
    .badge-danger {
        background-color: #e74a3b;
    }
    
    .badge-primary {
        background-color: #4e73df;
    }
    
    /* Estilos para el dropdown de notificaciones */
    #notification-dropdown {
        min-width: 280px;
        max-width: 350px;
        padding: 0;
        margin: 0;
        font-size: 0.85rem;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    #notification-dropdown .dropdown-header {
        background-color: #4e73df;
        color: #fff;
        padding: 0.75rem 1rem;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
    }
    
    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }
    
    .notification-item:hover {
        background-color: #f8f9fc;
    }
    
    .notification-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-time {
        font-size: 0.75rem;
        color: #858796;
    }
    
    .notification-message {
        font-weight: 600;
        color: #3a3b45;
        margin: 0;
    }

    /* Estilos para el dropdown del reloj */
    #clock-dropdown {
        min-width: 280px;
        max-width: 350px;
        padding: 0;
        margin: 0;
        font-size: 0.85rem;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    #clock-dropdown .dropdown-header {
        background-color: #4e73df;
        color: #fff;
        padding: 0.75rem 1rem;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
    }
    
    .clock-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .clock-item {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }
    
    .clock-item:hover {
        background-color: #f8f9fc;
    }
    
    .clock-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .clock-content {
        flex: 1;
    }
    
    .clock-time {
        font-size: 0.75rem;
        color: #858796;
    }
    
    .clock-message {
        font-weight: 600;
        color: #3a3b45;
        margin: 0;
    }

    .asistencia-ingreso {
        background-color: #e6f9ed !important;
        border-left: 5px solid #28a745 !important;
    }
    .asistencia-salida {
        background-color: #e6f0fa !important;
        border-left: 5px solid #007bff !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de notificaciones original
    class NotificationSystem {
        constructor() {
            this.notificationsCache = [];
            this.updateInterval = 60000; // 1 minuto
            this.init();
        }

        init() {
            this.loadNotifications();
            setInterval(() => this.loadNotifications(), this.updateInterval);
        }

        async loadNotifications() {
            try {
                const response = await fetch('api/get_notificaciones.php');
                const data = await response.json();
                
                this.updateCounter(data.total);
                this.updateNotificationMenu(data.notificaciones);
                
                this.notificationsCache = data.notificaciones;
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            }
        }

        updateCounter(total) {
            const counter = document.getElementById('notification-counter');
            if (counter) {
                counter.textContent = total;
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
                const iconConfig = this.getNotificationTypeConfig(notif.Tipo);
                const item = document.createElement('div');
                item.className = `alert alert-${iconConfig.color} d-flex align-items-center mb-2 py-2 px-3`;
                item.style.borderLeft = `5px solid var(--bs-${iconConfig.color})`;
                item.innerHTML = `
                    <i class="fas fa-${iconConfig.icon} me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">${notif.Mensaje}</div>
                        <div class="small text-muted">hace ${notif.TiempoTranscurrido}</div>
                    </div>
                `;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.markAsRead(notif.ID_Notificacion);
                });
                container.appendChild(item);
            });
        }

        getNotificationTypeConfig(tipo) {
            const configs = {
                'venta': { icon: 'shopping-cart', color: 'success' },
                'inventario': { icon: 'box', color: 'warning' },
                'sistema': { icon: 'cog', color: 'info' },
                'error': { icon: 'exclamation-triangle', color: 'danger' },
                'default': { icon: 'bell', color: 'primary' }
            };
            
            return configs[tipo] || configs.default;
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
    }

    // Sistema de reloj para asistencias
    class ClockSystem {
        constructor() {
            this.clockCache = [];
            this.updateInterval = 30000; // 30 segundos
            this.init();
        }

        init() {
            this.loadClockData();
            setInterval(() => this.loadClockData(), this.updateInterval);
        }

        async loadClockData() {
            try {
                const response = await fetch('api/get_clock_data.php');
                const data = await response.json();
                
                this.updateCounter(data.total);
                this.updateClockMenu(data.registros);
                
                this.clockCache = data.registros;
            } catch (error) {
                console.error('Error al cargar datos del reloj:', error);
            }
        }

        updateCounter(total) {
            const counter = document.getElementById('clock-counter');
            if (counter) {
                counter.textContent = total;
            }
        }

        updateClockMenu(registros) {
            const container = document.getElementById('clock-list');
            if (!container) return;

            container.innerHTML = '';

            if (registros.length === 0) {
                container.innerHTML = '<div class="dropdown-item text-center">No hay registros nuevos</div>';
                return;
            }

            registros.forEach(reg => {
                // Extrae el tipo de evento del mensaje (asume formato: NOMBRE - TIPO_EVENTO)
                const tipoEvento = reg.mensaje.split(' - ')[1];
                const iconConfig = this.getIconConfig(tipoEvento);
                const item = document.createElement('div');
                item.className = `alert alert-${iconConfig.color} d-flex align-items-center mb-2 py-2 px-3 ${iconConfig.customClass}`;
                item.style.borderLeft = `5px solid var(--bs-${iconConfig.color})`;
                item.innerHTML = `
                    <i class="fas fa-${iconConfig.icon} me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">${reg.mensaje}</div>
                        <div class="small text-muted">${reg.tiempo_transcurrido} (${reg.hora_registro})</div>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        getIconConfig(tipoEvento) {
            // Normaliza el texto a minúsculas para evitar errores por mayúsculas/minúsculas
            const tipo = (tipoEvento || '').toLowerCase();
            if (tipo === 'ingreso') {
                return { icon: 'sign-in-alt', color: 'success', customClass: 'asistencia-ingreso' };
            }
            if (tipo === 'salida') {
                return { icon: 'sign-out-alt', color: 'primary', customClass: 'asistencia-salida' };
            }
            // Por defecto
            return { icon: 'clock', color: 'secondary', customClass: '' };
        }
    }

    // Inicializar ambos sistemas
    window.notificationSystem = new NotificationSystem();
    window.clockSystem = new ClockSystem();
});
</script>

</body>
</html>