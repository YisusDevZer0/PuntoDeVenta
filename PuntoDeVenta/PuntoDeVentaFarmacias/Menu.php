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
                <span><?php echo isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario'; ?></span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="index" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-money-bill-transfer"></i>Punto de venta</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AperturarCajaV2" class="dropdown-item"><i class="fa-solid fa-cash-register"></i> Apertura de caja</a>
                    <a href="RealizarVentas" class="dropdown-item"><i class="fa-solid fa-hand-holding-dollar"></i> Realizar Ventas</a>
                    <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Encargos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-house-medical"></i>Farmacia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
                    <a href="TareasPorHacer" class="dropdown-item"><i class="fa-solid fa-list"></i>Lista de tareas</a>
                    <a href="Mensajes" class="dropdown-item"><i class="fa-solid fa-message"></i>Mensajes</a>
                    <a href="Recordatorios" class="dropdown-item"><i class="fa-solid fa-bell"></i>Crear recordatorios</a>
                    <a href="CheckListFarmacia" class="dropdown-item"><i class="fa-solid fa-clipboard-check"></i>Checklist farmacia</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-calendar"></i>Agendas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa-solid fa-person-walking-arrow-loop-left"></i>Revaloraciones</a>
                    <a href="AgendaEspecialistas" class="dropdown-item"><i class="fa-solid fa-hospital-user"></i>Especialistas</a>
                    <a href="AgendaLaboratorios" class="dropdown-item"><i class="fa-solid fa-flask"></i>Laboratorios</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-cash-register"></i>Cajas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="CortesDeCaja" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar"></i>Cortes de caja</a>
                    <a href="RegistroDeGastos" class="dropdown-item"><i class="fa-solid fa-comment-dollar"></i>Registro de gastos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-users"></i>Clientes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="DatosDeClientes" class="dropdown-item"><i class="fa-solid fa-people-group"></i>Lista de clientes</a>
                    <a href="Creditos" class="dropdown-item"><i class="fa-regular fa-credit-card"></i>Créditos</a>
                    <a href="AbonosEnCreditos" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Abonos en créditos</a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-receipt"></i>Tickets</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Tickets" class="dropdown-item"><i class="fa-solid fa-ticket-simple"></i>Desglose de tickets</a>
                    <a href="AbonosEnCreditos" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Tickets de crédito</a>
                    <a href="EncargosPendientes" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar"></i>Encargos</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-chart-line"></i>Ventas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="VentasDelDia" class="dropdown-item"><i class="fa-solid fa-coins"></i>Ventas del día</a>
                    <a href="VentasAcredito" class="dropdown-item"><i class="fa-solid fa-credit-card"></i>Ventas a crédito</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-boxes-stacked"></i>Almacén</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="StockSucursal" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Stock</a>
                    <a href="IngresosDeMedicamentos" class="dropdown-item"><i class="fa-solid fa-box"></i>Stock de insumos</a>
                    <a href="Pedidos" class="dropdown-item"><i class="fa-solid fa-truck"></i>Órdenes de pedidos</a>
                    <a href="ConteoDiario" class="dropdown-item"><i class="fa-solid fa-clipboard-list"></i>Conteo diario</a>
                    <a href="Cotizaciones" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Devoluciones</a>
                    <a href="Cotizaciones" class="dropdown-item"><i class="fa-solid fa-calendar-xmark"></i>Caducados</a>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-arrow-right-arrow-left"></i>Traspasos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RealizarTraspasos" class="dropdown-item"><i class="fa-solid fa-exchange-alt"></i>Realizar Traspasos</a>
                    <a href="ListadoDeTraspasos" class="dropdown-item"><i class="fa-solid fa-list-check"></i>Listado de traspasos</a>
                    <a href="Ingresos" class="dropdown-item"><i class="fa-solid fa-inbox"></i>Solicitar ingresos</a>
                    <a href="SolicitudPendientes" class="dropdown-item"><i class="fa-solid fa-clock-rotate-left"></i>Solicitudes pendientes</a>
                </div>
            </div>
            
            <a href="videos" class="nav-item nav-link"><i class="fa-solid fa-circle-play"></i>Video tutoriales</a>
        </div>
    </nav>
</div>