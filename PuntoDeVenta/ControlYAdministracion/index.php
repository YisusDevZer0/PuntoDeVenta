<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Definir variable para atributos disabled (por ahora vacía para habilitar todo)
$disabledAttr = '';

// Variable para identificar la página actual
$currentPage = 'index';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pantalla de inicio administrativa - <?php echo $row['Licencia']?></title>
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

    <!-- Sidebar Start -->
    <?php include_once "Menu.php" ?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Welcome Section Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="text-center">
                            <h1 class="mb-4 text-primary">
                                <i class="fa-solid fa-fish me-3" style="color: #ef7980!important;"></i>
                                Bienvenido a <?php echo $row['Licencia']; ?>
                            </h1>
                            <p class="lead mb-4">Sistema de Control y Administración</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-body text-center">
                                            <i class="fa fa-chart-line fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Dashboard</h5>
                                            <p class="card-text">Accede a estadísticas detalladas y reportes en tiempo real</p>
                                            <a href="dashboard" class="btn btn-primary">
                                                <i class="fa fa-chart-line me-2"></i>Ver Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-body text-center">
                                            <i class="fa fa-tachometer-alt fa-3x text-success mb-3"></i>
                                            <h5 class="card-title">Acciones Rápidas</h5>
                                            <p class="card-text">Accede directamente a las funciones más utilizadas</p>
                                            <div class="d-grid gap-2">
                                                <a href="RealizarVentas" class="btn btn-success">
                                                    <i class="fa fa-hand-holding-dollar me-2"></i>Realizar Ventas
                                                </a>
                                                <a href="CortesDeCaja" class="btn btn-warning">
                                                    <i class="fa fa-file-invoice-dollar me-2"></i>Cortes de Caja
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Welcome Section End -->

        <!-- Footer Start -->
        <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";
        ?>
    </div>
    <!-- Content End -->
</body>
</html>