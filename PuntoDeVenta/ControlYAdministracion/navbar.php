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
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Mensajes</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">See all message</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown" id="notification-nav-item">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" id="notification-bell">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificaciones</span>
                            <span class="badge badge-danger badge-counter" id="notification-counter">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0" id="notification-dropdown">
                            <h6 class="dropdown-header">Centro de Notificaciones</h6>
                            <div id="notification-list">
                                <a href="#" class="dropdown-item text-center">Cargando...</a>
                            </div>
                            <hr class="dropdown-divider">
                            <a href="GestionNotificaciones.php" class="dropdown-item text-center">Ver todas las notificaciones</a>
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

<!-- Estilos para el contador de notificaciones -->
<style>
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
    
    /* Posicionamiento relativo para el contenedor del icono */
    #notification-bell {
        position: relative;
        display: inline-block;
    }
</style>

<!-- Scripts de notificaciones -->
<script src="js/init-notifications.js"></script>

<!-- Script para cargar el contador de notificaciones -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para cargar el contador de notificaciones
        function cargarContadorNotificaciones() {
            fetch('api/get_notificaciones.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.error) {
                        // Actualizar el contador
                        const contador = document.getElementById('notification-counter');
                        if (contador) {
                            contador.textContent = data.total > 0 ? data.total : '';
                            contador.style.display = data.total > 0 ? 'inline-block' : 'none';
                        }
                        
                        // Actualizar el contenido del menú desplegable
                        const container = document.getElementById('notification-list');
                        if (container) {
                            container.innerHTML = '';
                            
                            if (data.notificaciones && data.notificaciones.length > 0) {
                                // Agregar cada notificación al menú
                                data.notificaciones.forEach(notif => {
                                    const item = document.createElement('a');
                                    item.href = '#';
                                    item.className = 'dropdown-item d-flex align-items-center';
                                    item.dataset.id = notif.ID_Notificacion;
                                    
                                    // Determinar el color y el icono según el tipo
                                    let iconColor, iconClass;
                                    switch(notif.Tipo.toLowerCase()) {
                                        case 'inventario': 
                                            iconColor = 'warning'; 
                                            iconClass = 'box'; 
                                            break;
                                        case 'caducidad': 
                                            iconColor = 'danger'; 
                                            iconClass = 'calendar'; 
                                            break;
                                        case 'caja': 
                                            iconColor = 'info'; 
                                            iconClass = 'cash-register'; 
                                            break;
                                        case 'venta': 
                                            iconColor = 'success'; 
                                            iconClass = 'tags'; 
                                            break;
                                        default: 
                                            iconColor = 'primary'; 
                                            iconClass = 'bell';
                                    }
                                    
                                    item.innerHTML = `
                                        <div class="mr-3">
                                            <div class="icon-circle bg-${iconColor}">
                                                <i class="fas fa-${iconClass} text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">hace ${notif.TiempoTranscurrido}</div>
                                            <span class="font-weight-bold">${notif.Mensaje}</span>
                                        </div>
                                    `;
                                    
                                    // Marcar como leída al hacer clic
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const id = this.dataset.id;
                                        fetch(`api/marcar_notificacion.php?id=${id}`)
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.success) {
                                                    // Eliminar del menú y actualizar contador
                                                    this.remove();
                                                    cargarContadorNotificaciones();
                                                }
                                            });
                                    });
                                    
                                    container.appendChild(item);
                                });
                            } else {
                                // Mensaje cuando no hay notificaciones
                                container.innerHTML = '<div class="dropdown-item text-center">No hay notificaciones</div>';
                            }
                        }
                    }
                })
                .catch(error => console.error('Error al cargar notificaciones:', error));
        }
        
        // Cargar al inicio
        cargarContadorNotificaciones();
        
        // Actualizar cada 60 segundos
        setInterval(cargarContadorNotificaciones, 60000);
        
        // Configurar toggle del menú de notificaciones
        const bell = document.getElementById('notification-bell');
        const menu = document.getElementById('notification-dropdown');
        
        if (bell && menu) {
            bell.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.toggle('show');
            });
            
            // Cerrar al hacer clic fuera del menú
            document.addEventListener('click', function(e) {
                if (!bell.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        }
    });
</script>

</body>
</html>