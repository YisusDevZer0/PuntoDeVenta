<?php
/**
 * Sistema de Recordatorios - Doctor Pez
 * Interfaz administrativa para gestionar recordatorios
 */

include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    include_once "Controladores/ControladorUsuario.php";
}

// Obtener ID del usuario actual
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sucursal_id = $row['Fk_Sucursal'] ?? 1;

// Definir variables necesarias para el menú
$disabledAttr = '';
$currentPage = 'recordatorios';
$showDashboard = false;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar si las tablas existen
$tablas_existen = false;
try {
    $resultado = $con->query("SHOW TABLES LIKE 'recordatorios_sistema'");
    $tablas_existen = ($resultado && $resultado->num_rows > 0);
} catch (Exception $e) {
    $tablas_existen = false;
}

// Obtener sucursales
$sucursales = [];
try {
    $sucursales_query = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Activo = 1 ORDER BY Nombre_Sucursal";
    $sucursales_result = $con->query($sucursales_query);
    if ($sucursales_result) {
        while ($row_sucursal = $sucursales_result->fetch_assoc()) {
            $sucursales[] = $row_sucursal;
        }
    }
} catch (Exception $e) {
    // Error al obtener sucursales
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios - <?php echo $row['Licencia']?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
    
    <!-- Estilos específicos de recordatorios -->
    <style>
        .recordatorios-container {
            min-height: 400px;
        }
        .install-message {
            text-align: center;
            padding: 3rem 1rem;
        }
        .install-message i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include "navbar.php";?>
    <!-- Navbar End -->

    <!-- Sidebar Start -->
    <?php include "Menu.php";?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-bell me-2"></i>
                                Sistema de Recordatorios
                            </h6>
                            <?php if ($tablas_existen): ?>
                                <button class="btn btn-primary" id="btn-nuevo-recordatorio">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    Nuevo Recordatorio
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="recordatorios-container">
                            <?php if (!$tablas_existen): ?>
                                <div class="install-message">
                                    <i class="fa-solid fa-exclamation-triangle text-warning"></i>
                                    <h4 class="text-warning">Instalación Requerida</h4>
                                    <p class="text-muted">Las tablas de la base de datos para el sistema de recordatorios no están instaladas.</p>
                                    <p class="text-muted">Para usar esta funcionalidad, necesitas ejecutar el script SQL de instalación.</p>
                                    <div class="mt-4">
                                        <a href="instalar_recordatorios.php" class="btn btn-primary btn-lg">
                                            <i class="fa-solid fa-download me-2"></i>
                                            Instalar Sistema de Recordatorios
                                        </a>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            O ejecuta manualmente el archivo: <code>database/recordatorios_tables_mejorado.sql</code>
                                        </small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="fa-solid fa-check-circle me-2"></i>
                                    <strong>¡Sistema instalado correctamente!</strong> El sistema de recordatorios está listo para usar.
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa-solid fa-bell me-2"></i>
                                            Recordatorios del Sistema
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center py-5">
                                            <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay recordatorios creados</h5>
                                            <p class="text-muted">Crea tu primer recordatorio para comenzar</p>
                                            <button class="btn btn-primary" id="btn-crear-primer-recordatorio">
                                                <i class="fa-solid fa-plus me-2"></i>
                                                Crear Primer Recordatorio
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Footer Start -->
    <?php include "footer.php";?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <?php include "scripts.php";?>
    
    <!-- Scripts específicos de recordatorios -->
    <script>
        $(document).ready(function() {
            // Ocultar spinner
            $('#spinner').hide();
            
            // Botón crear recordatorio
            $('#btn-crear-primer-recordatorio, #btn-nuevo-recordatorio').click(function() {
                alert('Funcionalidad de crear recordatorio en desarrollo. Primero instala las tablas de la base de datos.');
            });
        });
    </script>
</body>
</html>