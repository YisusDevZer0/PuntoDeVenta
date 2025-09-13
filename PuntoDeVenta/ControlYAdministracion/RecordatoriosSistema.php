<?php
// Verificación básica de sesión
session_start();

// Verificar sesión de manera simple
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

// Variables básicas
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sucursal_id = $row['Fk_Sucursal'] ?? 1;
$disabledAttr = '';
$currentPage = 'recordatorios';
$showDashboard = false;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar tablas de manera segura
$tablas_existen = false;
try {
    if (isset($con) && $con) {
        $resultado = $con->query("SHOW TABLES LIKE 'recordatorios_sistema'");
        $tablas_existen = ($resultado && $resultado->num_rows > 0);
    }
} catch (Exception $e) {
    $tablas_existen = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios - <?php echo isset($row['Licencia']) ? $row['Licencia'] : 'Doctor Pez'; ?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
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
                        </div>
                        
                        <div class="text-center py-5">
                            <?php if (!$tablas_existen): ?>
                                <i class="fa-solid fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Instalación Requerida</h4>
                                <p class="text-muted">Las tablas de la base de datos para el sistema de recordatorios no están instaladas.</p>
                                <div class="mt-4">
                                    <a href="instalar_recordatorios.php" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-download me-2"></i>
                                        Instalar Sistema
                                    </a>
                                </div>
                            <?php else: ?>
                                <i class="fa-solid fa-check-circle fa-3x text-success mb-3"></i>
                                <h4 class="text-success">¡Sistema Instalado!</h4>
                                <p class="text-muted">El sistema de recordatorios está listo para usar.</p>
                                <div class="mt-4">
                                    <button class="btn btn-primary btn-lg" onclick="alert('Funcionalidad en desarrollo')">
                                        <i class="fa-solid fa-plus me-2"></i>
                                        Crear Recordatorio
                                    </button>
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
    
    <script>
        $(document).ready(function() {
            $('#spinner').hide();
        });
    </script>
</body>
</html>