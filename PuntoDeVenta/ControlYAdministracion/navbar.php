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
                        <a href="#" class="nav-link dropdown-toggle" id="notification-bell" role="button" aria-expanded="false">
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

<!-- Estilos para el sistema de notificaciones -->
<style>
    #notification-bell {
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
</style>

</body>
</html>