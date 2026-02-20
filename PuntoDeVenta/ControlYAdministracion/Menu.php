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

if (!isset($disabledAttr)) {
    $disabledAttr = '';
}

// Verificar si el usuario tiene permisos para ver el menú completo
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');
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
            <?php if ($isAdmin): ?>
                <a href="dashboard" class="nav-item nav-link <?php echo (isset($currentPage) && $currentPage == 'dashboard') ? 'active' : ''; ?>"><i class="fa fa-chart-line me-2"></i>Dashboard</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-money-bill-transfer"></i>Punto de venta</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AperturarCajaV2" class="dropdown-item"><i class="fa-solid fa-cash-register"></i> Apertura de caja</a>
                    <a href="RealizarVentas" class="dropdown-item"><i class="fa-solid fa-hand-holding-dollar"></i> Realizar Ventas</a>
                    <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Encargos</a>
                    <a href="GeneracionDePagosDeServicios" class="dropdown-item"><i class="fa-solid fa-money-check-dollar"></i>Pago de servicios</a>
                    <a href="ConsultarProductoOtraSucursal" class="dropdown-item"><i class="fa-solid fa-warehouse"></i> Producto en otra sucursal</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-house-medical"></i>Farmacia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
                    <a href="TareasPorHacer" class="dropdown-item"><i class="fa-solid fa-list"></i>Lista de tareas</a>
                    <a href="Mensajes" class="dropdown-item"><i class="fa-solid fa-comments"></i>Mensajes</a>
                    <a href="Devoluciones" class="dropdown-item"><i class="fa-solid fa-undo"></i>Devoluciones</a>
                </div>
            </div>
            
            <a href="RecordatoriosSistema" class="nav-item nav-link"><i class="fa-solid fa-bell"></i>Recordatorios</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-broom"></i>Limpieza</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-clipboard-list"></i>Control de Bitácoras</a>
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-chart-line"></i>Reportes de Limpieza</a>
                </div>
            </div>
            
            <!-- <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-business-time"></i>Agendas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa-solid fa-person-walking-arrow-loop-left"></i>Revaloraciones</a>
                    <a href="AgendaEspecialista" class="dropdown-item"><i class="fa-solid fa-hospital-user"></i>Especialistas </a>
                </div>
            </div> -->
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users-gear"></i></i>Personal</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="TiposUsuarios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i> Tipos de usuarios</a>
                    <a href="PersonalActivo" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-user-check"></i>Personal activo </a>
                    <a href="Personaldebaja" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-user-xmark"></i>Personal inactivo</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-clock"></i>Checador</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ChecadorIndex" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-home"></i>Inicio Checador</a>
                    <a href="Checador" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-sign-in-alt"></i>Registrar Asistencia</a>
                    <a href="ChecadorDiario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar-day"></i>Reporte Diario</a>
                    <a href="ChecadorGeneral" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar"></i>Reporte General</a>
                    <a href="ConfiguracionUbicaciones" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-map-marker-alt"></i>Configurar Ubicaciones</a>
                    <?php if ($isAdmin): ?>
                    <div class="dropdown-divider"></div>
                    <a href="ChecadorAdmin" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users-cog"></i>Administración</a>
                    <a href="ChecadorCentrosTrabajo" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-building"></i>Centros de Trabajo</a>
                    <a href="ChecadorReportes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-chart-bar"></i>Reportes Avanzados</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <a href="Sucursales" class="nav-item nav-link" <?php echo $disabledAttr; ?>><i class="fa-solid fa-house-medical"></i>Sucursales</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-cash-register"></i></i></i>Cajas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="FondosDeCaja" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i> Fondos de caja</a>
                    <a href="CajasActivas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Cajas activas </a>
                    <a href="HistorialDeCajas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-vault"></i>Historial de cajas </a>
                    <a href="CortesDeCaja" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Cortes de caja</a>
                    <a href="RegistroDeGastos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-comment-dollar"></i>Registro de gastos</a>
                    <a href="TiposDeGastos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-comments-dollar"></i>Tipos de gastos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i>Clientes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="DatosDeClientes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-people-group"></i>Data de clientes</a>
                    <a href="CajasActivas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-credit-card"></i>Creditos </a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-receipt"></i></i></i></i>Tickets</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Tickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Desglose de tickets</a>
                    <a href="AbonosEnCreditos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Desglose Tickets Creditos </a>
                    <a href="CancelacionDeTickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-ban"></i>Cancelacion de tickets </a>
                    <a href="EdicionDeTickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-pen"></i>Edicion de tickets</a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Ventas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="VentasDelDia" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Ventas del dia</a>
                    <a href="VentasACredito" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Ventas del dia de Credito </a>
                    <a href="TotalesDeVentaPorVendedor" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i>Ventas por personal </a>
                    <a href="ComparacionInventarioVentas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-excel"></i>Comparación Inventario/Ventas</a>
                </div>
            </div>

            <!-- MENUS DE ALMACEN -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ProductosGenerales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-dolly"></i>Historico general </a>
                    <a href="Stocks" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-house-medical-flag"></i>Stocks </a>
                    <a href="GestionLotesCaducidades" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar-check"></i>Gestión de Lotes y Caducidades</a>
                    <a href="Caducados" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar-times"></i>Control de Caducados</a>
                    <a href="InventarioTurnos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-clipboard-list"></i>Inventario por Turnos</a>
                    <a href="GestionConteosInventario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-clipboard-check"></i>Gestión de Conteos</a>
                    <a href="Proveedores" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-field"></i>Proveedores </a> 
                    <a href="Servicios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Servicios </a>
                    <a href="Marcas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Marcas </a>
                    <a href="Presentaciones" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Presentaciones </a>
                    <a href="Componentes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Componentes </a>
                    <a href="TiposProductos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Tipos productos </a>
                    <a href="Categorias" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Categorias </a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck"></i>Órdenes de pedidos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Pedidos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck"></i>Gestionar pedidos</a>
                </div>
            </div>
            <!-- MENUS DE ALMACEN -->

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-arrow-right-arrow-left"></i>Traspasos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RealizarTraspasos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-ramp-box"></i>Realizar traspasos</a>
                    <a href="SolicitudesTraspasoSucursales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-inbox"></i>Solicitudes entre sucursales</a>
                    <a href="ListaDeTraspasos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Listado de traspasos</a>
                    <a href="RecepcionTraspasos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-ramp-box"></i>Recepción de traspasos (lote y caducidad)</a>
                    <a href="TraspasosAceptados" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Traspasos aceptados </a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>Ingresos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="IngresosDeFarmacias" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Ingresos Farmacias </a>
                    <a href="Ingresos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Realizar ingresos </a>
                    <a href="HistorialIngresosFarmacias" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-history"></i>Consultar ingresos (lotes y caducidades)</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-ramp-box"></i>Inventarios</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="MovimientosInventario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-arrows-rotate"></i>Movimientos de inventario</a>
                    <a href="InventarioSucursales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>inventario de sucursales </a>
                    <a href="AjusteInsumos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Insumos </a>
                    <a href="ReportesCedisInventario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Reportes Cedis </a>
                    <a href="ReportesInventarios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-square-poll-vertical"></i>Reportes inventarios </a>
                    <a href="ConteosPausados" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-pause-circle"></i>Conteos Pausados </a>
                    <a href="ConteosDiarios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar-day"></i>Conteos Diarios </a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-folder-tree"></i>Reportes</a>
                <div class="dropdown-menu bg-transparent border-0">
                <a href="ReportePagosDeServicios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-excel"></i>Reportes por pago de servicio</a>
                    <a href="ReportePorProducto" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-excel"></i>Reportes por producto</a>
                    <a href="ReporteServicios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte por tipo de servicios </a>
                    <a href="ReportesAnuales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reportes anuales </a>
                    <a href="TotalesDeVentaPorVendedor" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reportes por vendedor</a>
                    <a href="ReporteFormaDePago" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte por forma de pago</a>
                    <a href="ReporteProductosMasVendidos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Productos más vendidos</a>
                    <a href="ReporteSucursales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Rendimiento por sucursal</a>
                </div>
            </div>

            <a href="Mensajes" class="nav-item nav-link"><i class="fa fa-envelope me-2"></i>Mensajes</a>
            <?php endif; ?>

        </div>
    </nav>
</div>