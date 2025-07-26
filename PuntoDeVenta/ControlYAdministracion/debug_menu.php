<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    // Si $row no está disponible, incluir nuevamente el controlador
    include_once "Controladores/ControladorUsuario.php";
}

// Definir variable para atributos disabled (por ahora vacía para habilitar todo)
$disabledAttr = '';

// Variable para identificar la página actual
$currentPage = 'dashboard';

// Variable específica para el dashboard (no depende de permisos)
$showDashboard = true;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Debug Menu - <?php echo $row['Licencia']?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
</head>
<body>
    <!-- Debug Info Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded p-4">
            <h4>Debug del Menú</h4>
            <div class="row">
                <div class="col-md-6">
                    <h5>Variables del Usuario:</h5>
                    <ul>
                        <li><strong>ID Usuario:</strong> <?php echo isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 'No definido'; ?></li>
                        <li><strong>Nombre:</strong> <?php echo isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'No definido'; ?></li>
                        <li><strong>Tipo Usuario:</strong> <?php echo isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'No definido'; ?></li>
                        <li><strong>Licencia:</strong> <?php echo isset($row['Licencia']) ? $row['Licencia'] : 'No definido'; ?></li>
                        <li><strong>Sucursal:</strong> <?php echo isset($row['Nombre_Sucursal']) ? $row['Nombre_Sucursal'] : 'No definido'; ?></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Variables del Menú:</h5>
                    <ul>
                        <li><strong>currentPage:</strong> <?php echo isset($currentPage) ? $currentPage : 'No definido'; ?></li>
                        <li><strong>showDashboard:</strong> <?php echo isset($showDashboard) ? ($showDashboard ? 'true' : 'false') : 'No definido'; ?></li>
                        <li><strong>disabledAttr:</strong> <?php echo isset($disabledAttr) ? $disabledAttr : 'No definido'; ?></li>
                        <li><strong>TipoUsuario es Administrador:</strong> <?php echo (isset($row['TipoUsuario']) && $row['TipoUsuario'] == 'Administrador') ? 'Sí' : 'No'; ?></li>
                        <li><strong>TipoUsuario es MKT:</strong> <?php echo (isset($row['TipoUsuario']) && $row['TipoUsuario'] == 'MKT') ? 'Sí' : 'No'; ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Debug Info End -->

    <!-- Sidebar Start -->
    <?php include_once "Menu.php" ?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <div class="container-fluid pt-4 px-4">
            <div class="bg-light rounded p-4">
                <h4>Contenido del Dashboard</h4>
                <p>Si puedes ver este contenido, el menú debería estar funcionando correctamente.</p>
            </div>
        </div>

        <!-- Footer Start -->
        <?php include "Footer.php"; ?>
    </div>
    <!-- Content End -->
</body>
</html> 