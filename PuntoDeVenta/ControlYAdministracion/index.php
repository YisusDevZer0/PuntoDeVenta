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
$currentPage = 'index';

// Variable específica para el dashboard (no depende de permisos)
$showDashboard = true;

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar si el usuario tiene permisos de administrador
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar si es desarrollo humano (RH)
$isRH = ($tipoUsuario == 'Desarrollo Humano' || $tipoUsuario == 'RH');

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
                            
                            <!-- Dashboard Section - Visible para todos -->
                            <div class="row mb-4">
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
                                
                                <!-- Acciones Rápidas según tipo de usuario -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-body text-center">
                                            <i class="fa fa-tachometer-alt fa-3x text-success mb-3"></i>
                                            <h5 class="card-title">Acciones Rápidas</h5>
                                            <p class="card-text">Accede directamente a las funciones más utilizadas</p>
                                            
                                            <?php if ($isAdmin): ?>
                                            <!-- Opciones para Administradores -->
                                            <div class="d-grid gap-2">
                                                <a href="RealizarVentas" class="btn btn-success">
                                                    <i class="fa fa-hand-holding-dollar me-2"></i>Realizar Ventas
                                                </a>
                                                <a href="CortesDeCaja" class="btn btn-warning">
                                                    <i class="fa fa-file-invoice-dollar me-2"></i>Cortes de Caja
                                                </a>
                                                <a href="PersonalActivo" class="btn btn-info">
                                                    <i class="fa fa-users me-2"></i>Gestionar Personal
                                                </a>
                                                <a href="Sucursales" class="btn btn-secondary">
                                                    <i class="fa fa-building me-2"></i>Sucursales
                                                </a>
                                            </div>
                                            
                                            <?php elseif ($isRH): ?>
                                            <!-- Opciones para Desarrollo Humano -->
                                            <div class="d-grid gap-2">
                                                <a href="PersonalActivo" class="btn btn-info">
                                                    <i class="fa fa-users me-2"></i>Personal Activo
                                                </a>
                                                <a href="Personaldebaja" class="btn btn-warning">
                                                    <i class="fa fa-user-xmark me-2"></i>Personal Inactivo
                                                </a>
                                                <a href="TiposUsuarios" class="btn btn-secondary">
                                                    <i class="fa fa-user-tag me-2"></i>Tipos de Usuarios
                                                </a>
                                                <a href="ChecadorDiario" class="btn btn-success">
                                                    <i class="fa fa-calendar-day me-2"></i>Checador Diario
                                                </a>
                                            </div>
                                            
                                            <?php else: ?>
                                            <!-- Opciones para otros tipos de usuario -->
                                            <div class="d-grid gap-2">
                                                <a href="RealizarVentas" class="btn btn-success">
                                                    <i class="fa fa-hand-holding-dollar me-2"></i>Realizar Ventas
                                                </a>
                                                <a href="CortesDeCaja" class="btn btn-warning">
                                                    <i class="fa fa-file-invoice-dollar me-2"></i>Cortes de Caja
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección adicional para administradores -->
                            <?php if ($isAdmin): ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fa fa-cogs me-2"></i>Gestión Administrativa</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-boxes-stacked fa-2x text-primary mb-2"></i>
                                                        <h6>Almacén</h6>
                                                        <a href="ProductosGenerales" class="btn btn-sm btn-outline-primary">Productos</a>
                                                        <a href="Stocks" class="btn btn-sm btn-outline-primary">Stock</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-chart-line fa-2x text-success mb-2"></i>
                                                        <h6>Ventas</h6>
                                                        <a href="VentasDelDia" class="btn btn-sm btn-outline-success">Ventas del Día</a>
                                                        <a href="ReportePorProducto" class="btn btn-sm btn-outline-success">Reportes</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-truck fa-2x text-warning mb-2"></i>
                                                        <h6>Traspasos</h6>
                                                        <a href="RealizarTraspasos" class="btn btn-sm btn-outline-warning">Realizar</a>
                                                        <a href="ListaDeTraspasos" class="btn btn-sm btn-outline-warning">Listado</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-file-invoice fa-2x text-info mb-2"></i>
                                                        <h6>Reportes</h6>
                                                        <a href="ReportesAnuales" class="btn btn-sm btn-outline-info">Anuales</a>
                                                        <a href="ReporteSucursales" class="btn btn-sm btn-outline-info">Sucursales</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Sección específica para RH -->
                            <?php if ($isRH): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0"><i class="fa fa-users me-2"></i>Gestión de Recursos Humanos</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-user-check fa-2x text-success mb-2"></i>
                                                        <h6>Personal</h6>
                                                        <a href="PersonalActivo" class="btn btn-sm btn-outline-success">Activo</a>
                                                        <a href="Personaldebaja" class="btn btn-sm btn-outline-warning">Inactivo</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-clock fa-2x text-primary mb-2"></i>
                                                        <h6>Asistencia</h6>
                                                        <a href="ChecadorDiario" class="btn btn-sm btn-outline-primary">Diario</a>
                                                        <a href="ChecadorGeneral" class="btn btn-sm btn-outline-primary">General</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center p-3">
                                                        <i class="fa fa-user-tag fa-2x text-info mb-2"></i>
                                                        <h6>Configuración</h6>
                                                        <a href="TiposUsuarios" class="btn btn-sm btn-outline-info">Tipos</a>
                                                        <a href="Sucursales" class="btn btn-sm btn-outline-info">Sucursales</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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