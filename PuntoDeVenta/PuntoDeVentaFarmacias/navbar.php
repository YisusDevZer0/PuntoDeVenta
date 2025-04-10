<?php
// Asegúrate de que $Licencia y $Fk_Sucursal están definidos y tienen valores
$Licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';?>

<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-0 border-bottom" style="border-color: rgba(0,188,212,0.1) !important; box-shadow: none !important;">
    <div class="d-flex align-items-center h-100 ps-4 border-end" style="width: 250px; border-color: rgba(0,188,212,0.1) !important;">
        <a href="#" class="sidebar-toggler d-inline-block d-lg-none p-2 me-3">
            <i class="fa fa-bars"></i>
        </a>
        <a href="index.html" class="navbar-brand d-flex d-lg-none me-0">
            <h2 class="text-primary mb-0"><i class="fa-solid fa-fish" style="color: #00BCD4;"></i></h2>
        </a>
    </div>
    <div class="navbar-nav align-items-center w-100 px-4">
        <div class="d-flex justify-content-between w-100">
            <div class="d-flex">
                <div class="nav-item dropdown border-end pe-3" style="border-color: rgba(0,188,212,0.1) !important;">
                    <a href="#" class="nav-link dropdown-toggle py-3" data-bs-toggle="dropdown">
                        <i class="fa fa-envelope me-2 text-primary"></i>
                        <span class="d-none d-lg-inline-flex">Mensajes</span>
                    </a>
                    <div class="dropdown-menu border" style="margin-top: 0; border-radius: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2">
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
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
                <div class="nav-item dropdown border-end px-3" style="border-color: rgba(0,188,212,0.1) !important;">
                    <a href="#" class="nav-link dropdown-toggle py-3" data-bs-toggle="dropdown">
                        <i class="fa fa-bell me-2 text-primary"></i>
                        <span class="d-none d-lg-inline-flex">Notificaciones</span>
                    </a>
                    <div class="dropdown-menu border" style="margin-top: 0; border-radius: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2">
                            <h6 class="fw-normal mb-0">Inventario actualizado</h6>
                            <small>Hace 15 minutos</small>
                        </a>
                        <hr class="dropdown-divider my-0" style="border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2">
                            <h6 class="fw-normal mb-0">Nuevos productos agregados</h6>
                            <small>Hace 30 minutos</small>
                        </a>
                        <hr class="dropdown-divider my-0" style="border-color: rgba(0,188,212,0.1) !important;">
                        <a href="#" class="dropdown-item py-2 text-center">Ver todas las notificaciones</a>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle py-3 d-flex align-items-center" data-bs-toggle="dropdown">
                    <img class="rounded-circle" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 36px; height: 36px; object-fit: cover;">
                    <div class="d-none d-lg-flex flex-column ms-3 text-end">
                        <span class="fw-bold"><?php echo $row['Nombre_Apellidos']?></span>
                        <small class="text-muted"><?php echo $row['TipoUsuario']?></small>
                    </div>
                </a>
                <div class="dropdown-menu border" style="margin-top: 0; border-radius: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-color: rgba(0,188,212,0.1) !important;">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona el enlace de cierre de sesión
        const logoutLink = document.querySelector('#logout-link');
        
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
                confirmButtonColor: '#00BCD4',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // Si el usuario confirma, redirige a la página de cierre de sesión
                if (result.isConfirmed) {
                    window.location.href = "cerrar_sesion.php";
                }
            });
        });

        // Efecto para el toggler de la barra lateral
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        sidebarToggler.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.content').classList.toggle('active');
        });
    });
</script>