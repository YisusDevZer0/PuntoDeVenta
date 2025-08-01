<?php
// Asegúrate de que $Licencia y $Fk_Sucursal están definidos y tienen valores
$Licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';?>

<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-0 border-bottom shadow-sm" style="border-color: rgba(0,188,212,0.1) !important; min-height: 56px; box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;">
    <div class="d-flex align-items-center h-100 ps-4 border-end" style="width: 220px; border-color: rgba(0,188,212,0.1) !important;">
        <a href="#" class="sidebar-toggler d-inline-block d-lg-none p-2 me-3">
            <i class="fa fa-bars"></i>
        </a>
        <a href="index.html" class="navbar-brand d-flex d-lg-none me-0">
            <h2 class="text-primary mb-0"><i class="fa-solid fa-fish" style="color: #00BCD4;"></i></h2>
        </a>
    </div>
    <div class="navbar-nav align-items-center w-100 px-4">
        <div class="d-flex justify-content-between w-100 align-items-center">
            <div class="d-flex">
                <div class="nav-item dropdown border-end pe-3" style="border-color: rgba(0,188,212,0.1) !important;">
                    <a href="#" class="nav-link dropdown-toggle py-2" data-bs-toggle="dropdown">
                        <i class="fa fa-envelope me-2 text-primary"></i>
                        <span class="d-none d-lg-inline-flex">Mensajes</span>
                    </a>
                    <div class="dropdown-menu border rounded-3 shadow-sm" style="margin-top: 0; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2">
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 32px; height: 32px;">
                                <div class="ms-2">
                                    <h6 class="fw-normal mb-0">Tienes un nuevo mensaje</h6>
                                    <small>Hace 15 minutos</small>
                                </div>
                            </div>
                        </a>
                        <hr class="dropdown-divider my-0" style="border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2 text-center">Ver todos los mensajes</a>
                    </div>
                </div>
                <div class="nav-item dropdown" id="notification-nav-item">
                    <a href="#" class="nav-link dropdown-toggle" id="notification-bell" role="button" aria-expanded="false">
                        <i class="fa fa-bell me-lg-2"></i>
                        <span class="d-none d-lg-inline-flex">Notificaciones</span>
                        <span class="badge badge-danger badge-counter" id="notification-counter" style="font-size: 0.85rem; padding: 2px 7px;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow rounded-3 animated--grow-in" id="notification-dropdown" aria-labelledby="notification-bell">
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
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle py-2 d-flex align-items-center" data-bs-toggle="dropdown">
                    <img class="rounded-circle border border-2 border-white shadow-sm" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 36px; height: 36px; object-fit: cover;">
                    <div class="d-none d-lg-flex flex-column ms-3 text-end">
                        <span class="fw-bold" style="font-size: 1rem; color: #222;"><?php echo $row['Nombre_Apellidos']?></span>
                        <small class="text-muted" style="font-size: 0.95rem;"><?php echo $row['TipoUsuario']?></small>
                    </div>
                </a>
                <div class="dropdown-menu border rounded-3 shadow-sm" style="margin-top: 0; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-color: rgba(0,188,212,0.1) !important;">
                    <a href="MiPerfilDeUsuarioYMas" class="dropdown-item py-2">
                        <i class="fa fa-user me-2 text-primary"></i> Mi perfil
                    </a>
                    <hr class="dropdown-divider my-0" style="border-color: rgba(0,188,212,0.1) !important;">
                    <a href="cerrar_sesion.php" class="dropdown-item py-2" id="logout-link">
                        <i class="fa fa-sign-out-alt me-2 text-primary"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar {
    font-family: 'Inter', Arial, sans-serif;
    background: #f8fafc !important;
}
.navbar .nav-link {
    color: #222;
    font-weight: 500;
    transition: color 0.2s, background 0.2s;
}
.navbar .nav-link:hover, .navbar .nav-link:focus {
    color: #0172b6;
    background: #e3f2fd;
    border-radius: 12px;
}
.navbar .dropdown-menu {
    min-width: 220px;
    font-size: 0.97rem;
}
.navbar .dropdown-header {
    font-size: 1rem;
    font-weight: 600;
    background: #0172b6 !important;
}
.badge-counter {
    background: #ef7980;
    color: #fff;
    border-radius: 12px;
    font-weight: bold;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona el enlace de cierre de sesión
        const logoutLink = document.querySelector('#logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: '¿Estás seguro de que deseas cerrar sesión?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#00BCD4',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "cerrar_sesion.php";
                    }
                });
            });
        }
        // Efecto para el toggler de la barra lateral
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
                document.querySelector('.content').classList.toggle('active');
            });
        }
    });
</script>