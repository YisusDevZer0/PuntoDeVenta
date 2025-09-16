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
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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
            <!-- Animación de burbujas -->
            <div class="bubbles-container">
                <div class="bubble bubble-1"></div>
                <div class="bubble bubble-2"></div>
                <div class="bubble bubble-3"></div>
                <div class="bubble bubble-4"></div>
                <div class="bubble bubble-5"></div>
                <div class="bubble bubble-6"></div>
                <div class="bubble bubble-7"></div>
                <div class="bubble bubble-8"></div>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4 position-relative">
                        <div class="text-center">
                            <h1 class="mb-4 text-primary">
                                <i class="fa-solid fa-fish me-3" style="color: #ef7980!important;"></i>
                                Bienvenido, <?php echo $row['Nombre_Apellidos']; ?>
                            </h1>
                            <p class="lead mb-4">
                                <i class="fa-solid fa-user-tag me-2 text-info"></i>
                                <?php echo $tipoUsuario; ?> - <?php echo $row['Licencia']; ?>
                            </p>
                            <p class="text-muted mb-4">
                                <i class="fa-solid fa-building me-2"></i>
                                Sucursal: <?php echo isset($row['Nombre_Sucursal']) ? $row['Nombre_Sucursal'] : 'N/A'; ?>
                            </p>
                            
                            <!-- Dashboard Section - Visible para todos -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-lg-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center d-flex flex-column">
                                            <i class="fa fa-chart-line fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Dashboard</h5>
                                            <p class="card-text flex-grow-1">Accede a estadísticas detalladas y reportes en tiempo real</p>
                                            <a href="dashboard" class="btn btn-primary mt-auto">
                                                <i class="fa fa-chart-line me-2"></i>Ver Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Acciones Rápidas según tipo de usuario -->
                                <div class="col-12 col-lg-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center d-flex flex-column">
                                            <i class="fa fa-tachometer-alt fa-3x text-success mb-3"></i>
                                            <h5 class="card-title">Acciones Rápidas</h5>
                                            <p class="card-text flex-grow-1">Accede directamente a las funciones más utilizadas</p>
                                            
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
                                            <div class="row g-3">
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-boxes-stacked fa-2x text-primary mb-2"></i>
                                                        <h6 class="mb-2">Almacén</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="ProductosGenerales" class="btn btn-sm btn-outline-primary">Productos</a>
                                                            <a href="Stocks" class="btn btn-sm btn-outline-primary">Stock</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-chart-line fa-2x text-success mb-2"></i>
                                                        <h6 class="mb-2">Ventas</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="VentasDelDia" class="btn btn-sm btn-outline-success">Ventas del Día</a>
                                                            <a href="ReportePorProducto" class="btn btn-sm btn-outline-success">Reportes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-truck fa-2x text-warning mb-2"></i>
                                                        <h6 class="mb-2">Traspasos</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="RealizarTraspasos" class="btn btn-sm btn-outline-warning">Realizar</a>
                                                            <a href="ListaDeTraspasos" class="btn btn-sm btn-outline-warning">Listado</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-file-invoice fa-2x text-info mb-2"></i>
                                                        <h6 class="mb-2">Reportes</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="ReportesAnuales" class="btn btn-sm btn-outline-info">Anuales</a>
                                                            <a href="ReporteSucursales" class="btn btn-sm btn-outline-info">Sucursales</a>
                                                        </div>
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
                                            <div class="row g-3">
                                                <div class="col-12 col-md-4">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-user-check fa-2x text-success mb-2"></i>
                                                        <h6 class="mb-2">Personal</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="PersonalActivo" class="btn btn-sm btn-outline-success">Activo</a>
                                                            <a href="Personaldebaja" class="btn btn-sm btn-outline-warning">Inactivo</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-clock fa-2x text-primary mb-2"></i>
                                                        <h6 class="mb-2">Asistencia</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="ChecadorDiario" class="btn btn-sm btn-outline-primary">Diario</a>
                                                            <a href="ChecadorGeneral" class="btn btn-sm btn-outline-primary">General</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="text-center p-2 p-md-3">
                                                        <i class="fa fa-user-tag fa-2x text-info mb-2"></i>
                                                        <h6 class="mb-2">Configuración</h6>
                                                        <div class="d-grid gap-1">
                                                            <a href="TiposUsuarios" class="btn btn-sm btn-outline-info">Tipos</a>
                                                            <a href="Sucursales" class="btn btn-sm btn-outline-info">Sucursales</a>
                                                        </div>
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

        <!-- Estilos para la animación de burbujas -->
        <style>
            .bubbles-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
                overflow: hidden;
            }
            
            .bubble {
                position: absolute;
                background: linear-gradient(45deg, #ef7980, #ff6b6b, #4ecdc4, #45b7d1);
                border-radius: 50%;
                opacity: 0.3;
                animation: float 6s ease-in-out infinite;
            }
            
            .bubble-1 {
                width: 40px;
                height: 40px;
                left: 10%;
                animation-delay: 0s;
            }
            
            .bubble-2 {
                width: 60px;
                height: 60px;
                left: 20%;
                animation-delay: 1s;
            }
            
            .bubble-3 {
                width: 30px;
                height: 30px;
                left: 30%;
                animation-delay: 2s;
            }
            
            .bubble-4 {
                width: 50px;
                height: 50px;
                left: 40%;
                animation-delay: 3s;
            }
            
            .bubble-5 {
                width: 35px;
                height: 35px;
                left: 50%;
                animation-delay: 4s;
            }
            
            .bubble-6 {
                width: 45px;
                height: 45px;
                left: 60%;
                animation-delay: 5s;
            }
            
            .bubble-7 {
                width: 25px;
                height: 25px;
                left: 70%;
                animation-delay: 6s;
            }
            
            .bubble-8 {
                width: 55px;
                height: 55px;
                left: 80%;
                animation-delay: 7s;
            }
            
            @keyframes float {
                0%, 100% {
                    transform: translateY(100vh) scale(0);
                    opacity: 0;
                }
                10% {
                    opacity: 0.3;
                }
                90% {
                    opacity: 0.3;
                }
                100% {
                    transform: translateY(-100px) scale(1);
                    opacity: 0;
                }
            }
            
            /* Efecto de ondas en el fondo */
            .bg-light {
                position: relative;
                overflow: hidden;
            }
            
            .bg-light::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(239, 121, 128, 0.05) 0%, transparent 70%);
                animation: wave 8s ease-in-out infinite;
                pointer-events: none;
            }
            
            @keyframes wave {
                0%, 100% {
                    transform: rotate(0deg);
                }
                50% {
                    transform: rotate(180deg);
                }
            }
            
            /* Mejoras en las tarjetas */
            .card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            /* Efecto especial para el título */
            .text-primary {
                background: linear-gradient(45deg, #ef7980, #ff6b6b);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: shimmer 2s ease-in-out infinite;
            }
            
            @keyframes shimmer {
                0%, 100% {
                    filter: brightness(1);
                }
                50% {
                    filter: brightness(1.2);
                }
            }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                .bubbles-container {
                    display: none; /* Ocultar animaciones en móviles para mejor rendimiento */
                }
                
                .bg-light::before {
                    display: none; /* Ocultar efecto de ondas en móviles */
                }
                
                .card:hover {
                    transform: none; /* Desactivar hover en móviles */
                }
                
                .text-primary {
                    animation: none; /* Desactivar animación de shimmer en móviles */
                }
                
                /* Ajustar espaciado en móviles */
                .container-fluid {
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                /* Mejorar botones en móviles */
                .btn {
                    font-size: 0.875rem;
                    padding: 0.5rem 1rem;
                }
                
                .btn-sm {
                    font-size: 0.75rem;
                    padding: 0.375rem 0.75rem;
                }
                
                /* Ajustar iconos en móviles */
                .fa-3x {
                    font-size: 2rem !important;
                }
                
                .fa-2x {
                    font-size: 1.5rem !important;
                }
                
                /* Mejorar títulos en móviles */
                h1 {
                    font-size: 1.75rem;
                }
                
                h5 {
                    font-size: 1.25rem;
                }
                
                h6 {
                    font-size: 1rem;
                }
                
                /* Ajustar padding de tarjetas en móviles */
                .card-body {
                    padding: 1rem;
                }
                
                .p-2 {
                    padding: 0.75rem !important;
                }
            }
            
            @media (max-width: 576px) {
                /* Ajustes adicionales para pantallas muy pequeñas */
                .col-6 {
                    margin-bottom: 1rem;
                }
                
                .d-grid .btn {
                    font-size: 0.75rem;
                    padding: 0.375rem 0.5rem;
                }
                
                .lead {
                    font-size: 1rem;
                }
                
                .text-muted {
                    font-size: 0.875rem;
                }
            }
            
            /* Mejoras para tablets */
            @media (min-width: 768px) and (max-width: 1024px) {
                .col-md-3 {
                    margin-bottom: 1rem;
                }
                
                .col-md-4 {
                    margin-bottom: 1rem;
                }
            }
        </style>
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