<?php 
// Verificar si $row está disponible, si no, obtener los datos del usuario
if (!isset($row) || !isset($row['TipoUsuario'])) {
    // Incluir el controlador de usuario si no está ya incluido
    if (!function_exists('getUserData')) {
        include_once "Controladores/ControladorUsuario.php";
    }
}

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Asegurar que las variables necesarias estén definidas
if (!isset($currentPage)) {
    $currentPage = 'index';
}

if (!isset($showDashboard)) {
    $showDashboard = false;
}
?>

<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="index.html" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i style="color: #ef7980!important;" class="fa-solid fa-fish"></i><?php echo isset($row['Licencia']) ? $row['Licencia'] : 'Doctor Pez'; ?></h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo isset($row['file_name']) ? $row['file_name'] : 'user.jpg'; ?>" alt="" style="width: 40px; height: 40px;" onerror="this.src='img/user.jpg'; this.onerror=null;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0"><?php echo isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Usuario'; ?></h6>
                <span><?php echo $tipoUsuario; ?></span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="index" class="nav-item nav-link <?php echo (!isset($currentPage) || $currentPage == 'index') ? 'active' : ''; ?>"><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>
            
            <?php if ($showDashboard): ?>
            
            <?php endif; ?>
            
            <a href="dashboard" class="nav-item nav-link <?php echo (isset($currentPage) && $currentPage == 'dashboard') ? 'active' : ''; ?>"><i class="fa fa-chart-line me-2"></i>Dashboard</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-money-bill-transfer"></i>Punto de venta</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AperturarCajaV2" class="dropdown-item"><i class="fa-solid fa-cash-register"></i> Apertura de caja</a>
                    <a href="RealizarVentas" class="dropdown-item"><i class="fa-solid fa-hand-holding-dollar"></i> Realizar Ventas</a>
                    <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Encargos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-house-medical"></i>Farmacia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
                    <a href="TareasPorHacer" class="dropdown-item"><i class="fa-solid fa-list"></i>Lista de tareas</a>
                    <a href="Mensajes" class="dropdown-item"><i class="fa-solid fa-business-time"></i>Mensajes</a>
                    <a href="Recordatorios" class="dropdown-item"><i class="fa-solid fa-business-time"></i>Crear recordatorios</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-business-time"></i>Agendas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa-solid fa-person-walking-arrow-loop-left"></i>Revaloraciones</a>
                    <a href="AgendaEspecialista" class="dropdown-item"><i class="fa-solid fa-hospital-user"></i>Especialistas </a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users-gear"></i></i>Personal</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="TiposUsuarios" class="dropdown-item"><i class="fa-solid fa-users"></i> Tipos de usuarios</a>
                    <a href="PersonalActivo" class="dropdown-item"><i class="fa-solid fa-user-check"></i>Personal activo </a>
                    <a href="Personaldebaja" class="dropdown-item"><i class="fa-solid fa-user-xmark"></i>Personal inactivo</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-clock"></i></i>Checador</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ChecadorDiario" class="dropdown-item"><i class="fa-solid fa-calendar-day"></i>Reporte diario</a>
                    <a href="ChecadorGeneral" class="dropdown-item"><i class="fa-solid fa-calendar"></i>Reporte General </a>
                </div>
            </div>
            
            <a href="Sucursales" class="nav-item nav-link"><i class="fa-solid fa-house-medical"></i>Sucursales</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Inventarios</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ProductosGenerales" class="dropdown-item"><i class="fa-solid fa-box"></i>Productos</a>
                    <a href="Categorias" class="dropdown-item"><i class="fa-solid fa-tags"></i>Categorias</a>
                    <a href="Presentaciones" class="dropdown-item"><i class="fa-solid fa-box-open"></i>Presentaciones</a>
                    <a href="Proveedores" class="dropdown-item"><i class="fa-solid fa-truck"></i>Proveedores</a>
                    <a href="Servicios" class="dropdown-item"><i class="fa-solid fa-hand-holding-heart"></i>Servicios</a>
                    <a href="InventarioSucursales" class="dropdown-item"><i class="fa-solid fa-warehouse"></i>Inventario por sucursal</a>
                    <a href="Stocks" class="dropdown-item"><i class="fa-solid fa-chart-line"></i>Stock</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-chart-line"></i>Reportes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ReporteDeVentas" class="dropdown-item"><i class="fa-solid fa-chart-bar"></i>Reporte de ventas</a>
                    <a href="ReporteDeVentasTotales" class="dropdown-item"><i class="fa-solid fa-chart-pie"></i>Reporte de ventas totales</a>
                    <a href="ReporteMasVendidos" class="dropdown-item"><i class="fa-solid fa-star"></i>Productos mas vendidos</a>
                    <a href="ReportePorFormaDePago" class="dropdown-item"><i class="fa-solid fa-credit-card"></i>Reporte por forma de pago</a>
                    <a href="ReportePorProductoFormaPago" class="dropdown-item"><i class="fa-solid fa-chart-line"></i>Reporte por producto y forma de pago</a>
                    <a href="ReportesInventarios" class="dropdown-item"><i class="fa-solid fa-boxes-stacked"></i>Reportes de inventarios</a>
                    <a href="ReportesCedisInventario" class="dropdown-item"><i class="fa-solid fa-warehouse"></i>Reportes CEDIS inventario</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cogs"></i>Configuracion</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="TiposDeGastos" class="dropdown-item"><i class="fa-solid fa-money-bill"></i>Tipos de gastos</a>
                    <a href="SolicitudPendientes" class="dropdown-item"><i class="fa-solid fa-clock"></i>Solicitudes pendientes</a>
                    <a href="RegistroSolicitudesCompletas" class="dropdown-item"><i class="fa-solid fa-check-circle"></i>Registro de solicitudes completas</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-exchange-alt"></i>Traspasos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RealizarTraspasos" class="dropdown-item"><i class="fa-solid fa-truck"></i>Realizar traspasos</a>
                    <a href="ListaDeTraspasos" class="dropdown-item"><i class="fa-solid fa-list"></i>Lista de traspasos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-plus"></i>Ingresos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Ingresos" class="dropdown-item"><i class="fa-solid fa-plus-circle"></i>Ingresos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-receipt"></i>Tickets</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Tickets" class="dropdown-item"><i class="fa-solid fa-ticket"></i>Tickets</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users"></i>Clientes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="DatosDeClientes" class="dropdown-item"><i class="fa-solid fa-user"></i>Datos de clientes</a>
                    <a href="Creditos" class="dropdown-item"><i class="fa-solid fa-credit-card"></i>Creditos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i>Cajas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="CortesDeCaja" class="dropdown-item"><i class="fa-solid fa-cash-register"></i>Cortes de caja</a>
                    <a href="RegistroDeGastos" class="dropdown-item"><i class="fa-solid fa-money-bill"></i>Registro de gastos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-file-invoice"></i>Pedidos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Pedidos" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Pedidos</a>
                    <a href="PedidosAdministrativos" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar"></i>Pedidos administrativos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-chart-line"></i>Reportes Anuales</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ReportesAnuales" class="dropdown-item"><i class="fa-solid fa-chart-line"></i>Reportes anuales</a>
                    <a href="ReporteVentasTotalesPorFechas" class="dropdown-item"><i class="fa-solid fa-chart-bar"></i>Reporte ventas totales por fechas</a>
                    <a href="ReporteSucursales" class="dropdown-item"><i class="fa-solid fa-building"></i>Reporte sucursales</a>
                    <a href="ReporteProductosMasVendidos" class="dropdown-item"><i class="fa-solid fa-star"></i>Reporte productos mas vendidos</a>
                    <a href="ReporteFormaDePago" class="dropdown-item"><i class="fa-solid fa-credit-card"></i>Reporte forma de pago</a>
                    <a href="ReporteServicios" class="dropdown-item"><i class="fa-solid fa-hand-holding-heart"></i>Reporte servicios</a>
                    <a href="ReportePorProducto" class="dropdown-item"><i class="fa-solid fa-box"></i>Reporte por producto</a>
                    <a href="TotalesDeVentaPorVendedor" class="dropdown-item"><i class="fa-solid fa-user-tie"></i>Totales de venta por vendedor</a>
                </div>
            </div>
        </div>
    </nav>
</div>