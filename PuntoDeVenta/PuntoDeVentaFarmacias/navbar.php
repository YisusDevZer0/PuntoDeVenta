<?php
// Asegúrate de que $Licencia y $Fk_Sucursal están definidos y tienen valores
$Licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';?>

<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="javascript:void(0);" class="sidebar-toggler flex-shrink-0">
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
                                        <h6 class="fw-normal mb-0">Tienes un nuevo mensaje</h6>
                                        <small>Hace 15 minutos</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">Ver todos los mensajes</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown" id="notification-nav-item">
                        <a href="#" class="nav-link dropdown-toggle" id="notification-bell" role="button" aria-expanded="false">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificaciones</span>
                            <span class="badge badge-danger badge-counter" id="notification-counter" style="font-size: 0.85rem; padding: 2px 7px;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0" id="notification-dropdown" aria-labelledby="notification-bell">
                            <h6 class="dropdown-header bg-primary text-white rounded-top">
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
                            <a href="cerrar_sesion.php" class="dropdown-item" id="logout-link">Cerrar sesion</a>
                        </div>
                    </div>
                </div>
            </nav>
            <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona el enlace de cierre de sesión
        const logoutLink = document.querySelector('#logout-link');
        
        // Agrega un evento de clic al enlace
        if (logoutLink) {
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
        }
    });
</script>