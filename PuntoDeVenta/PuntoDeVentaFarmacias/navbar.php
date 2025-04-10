<?php
// Asegúrate de que $Licencia y $Fk_Sucursal están definidos y tienen valores
$Licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';?>

<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
    <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa-solid fa-fish" style="color: #00BCD4;"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>
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
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-bell me-lg-2"></i>
                <span class="d-none d-lg-inline-flex">Notificaciones</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Inventario actualizado</h6>
                    <small>Hace 15 minutos</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Nuevos productos agregados</h6>
                    <small>Hace 30 minutos</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item text-center">Ver todas las notificaciones</a>
            </div>
        </div>
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img class="rounded-circle me-lg-2" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 40px; height: 40px;">
                <span class="d-none d-lg-inline-flex"><?php echo $row['Nombre_Apellidos']?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="MiPerfilDeUsuarioYMas" class="dropdown-item">Mi perfil</a>
                <a href="cerrar_sesion.php" class="dropdown-item" id="logout-link">Cerrar sesión</a>
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