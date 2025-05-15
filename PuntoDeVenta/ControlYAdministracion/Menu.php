<?php 
// Obtener el tipo de usuario actual
$tipoUsuario = $row['TipoUsuario'];
?>

<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="index.html" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i style="color: #ef7980!important;" class="fa-solid fa-fish"></i><?php echo $row['Licencia']?></h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0"><?php echo $row['Nombre_Apellidos']?></h6>
                <span><?php echo $row['TipoUsuario']?></span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="index" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-money-bill-transfer me-2"></i>Punto de venta</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AperturarCajaV2" class="dropdown-item"><i class="fa-solid fa-cash-register me-2"></i>Apertura de caja</a>
                    <a href="RealizarVentas" class="dropdown-item"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Realizar Ventas</a>
                    <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa-solid fa-file-invoice me-2"></i>Encargos</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-house-medical me-2"></i>Farmacia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa-solid fa-lightbulb me-2"></i>Registro de energia</a>
                    <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-broom me-2"></i>Bitacora de limpieza</a>
                    <a href="TareasPorHacer" class="dropdown-item"><i class="fa-solid fa-list me-2"></i>Lista de tareas</a>
                    <a href="Mensajes" class="dropdown-item"><i class="fa-solid fa-business-time me-2"></i>Mensajes</a>
                    <a href="Recordatorios" class="dropdown-item"><i class="fa-solid fa-business-time me-2"></i>Crear recordatorios</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-business-time me-2"></i>Agendas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa-solid fa-person-walking-arrow-loop-left me-2"></i>Revaloraciones</a>
                    <a href="AgendaEspecialista" class="dropdown-item"><i class="fa-solid fa-hospital-user me-2"></i>Especialistas</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users-gear me-2"></i>Personal</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="TiposUsuarios" class="dropdown-item"><i class="fa-solid fa-users me-2"></i>Tipos de usuarios</a>
                    <a href="PersonalActivo" class="dropdown-item"><i class="fa-solid fa-user-check me-2"></i>Personal activo</a>
                    <a href="Personaldebaja" class="dropdown-item"><i class="fa-solid fa-user-xmark me-2"></i>Personal inactivo</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-clock me-2"></i>Checador</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ChecadorDiario" class="dropdown-item"><i class="fa-solid fa-calendar-day me-2"></i>Reporte diario</a>
                    <a href="ChecadorGeneral" class="dropdown-item"><i class="fa-solid fa-calendar me-2"></i>Reporte General</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <a href="Sucursales" class="nav-item nav-link"><i class="fa-solid fa-house-medical me-2"></i>Sucursales</a>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register me-2"></i>Cajas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="FondosDeCaja" class="dropdown-item"><i class="fa-solid fa-coins me-2"></i>Fondos de caja</a>
                    <a href="CajasActivas" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Cajas activas</a>
                    <a href="HistorialDeCajas" class="dropdown-item"><i class="fa-solid fa-vault me-2"></i>Historial de cajas</a>
                    <a href="CortesDeCaja" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Cortes de caja</a>
                    <a href="RegistroDeGastos" class="dropdown-item"><i class="fa-solid fa-comment-dollar me-2"></i>Registro de gastos</a>
                    <a href="TiposDeGastos" class="dropdown-item"><i class="fa-solid fa-comments-dollar me-2"></i>Tipos de gastos</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users me-2"></i>Clientes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="DatosDeClientes" class="dropdown-item"><i class="fa-solid fa-people-group me-2"></i>Data de clientes</a>
                    <a href="CajasActivas" class="dropdown-item"><i class="fa-regular fa-credit-card me-2"></i>Creditos</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-receipt me-2"></i>Tickets</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Tickets" class="dropdown-item"><i class="fa-solid fa-coins me-2"></i>Desglose de tickets</a>
                    <a href="AbonosEnCreditos" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Desglose Tickets Creditos</a>
                    <a href="CancelacionDeTickets" class="dropdown-item"><i class="fa-solid fa-ban me-2"></i>Cancelacion de tickets</a>
                    <a href="EdicionDeTickets" class="dropdown-item"><i class="fa-solid fa-file-pen me-2"></i>Edicion de tickets</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Ventas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="VentasDelDia" class="dropdown-item"><i class="fa-solid fa-coins me-2"></i>Ventas del dia</a>
                    <a href="VentasACredito" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Ventas del dia de Credito</a>
                    <a href="TotalesDeVentaPorVendedor" class="dropdown-item"><i class="fa-solid fa-users me-2"></i>Ventas por personal</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked me-2"></i>Almacen</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ProductosGenerales" class="dropdown-item"><i class="fa-solid fa-dolly me-2"></i>Historico general</a>
                    <a href="Stocks" class="dropdown-item"><i class="fa-solid fa-house-medical-flag me-2"></i>Stocks</a>
                    <a href="Proveedores" class="dropdown-item"><i class="fa-solid fa-truck-field me-2"></i>Proveedores</a>
                    <a href="Servicios" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Servicios</a>
                    <a href="Marcas" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Marcas</a>
                    <a href="Presentaciones" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Presentaciones</a>
                    <a href="Componentes" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Componentes</a>
                    <a href="TiposProductos" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Tipos productos</a>
                    <a href="Categorias" class="dropdown-item"><i class="fa-solid fa-staff-snake me-2"></i>Categorias</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Traspasos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RealizarTraspasos" class="dropdown-item"><i class="fa-solid fa-truck-ramp-box me-2"></i>Realizar traspasos</a>
                    <a href="ListaDeTraspasos" class="dropdown-item"><i class="fa-solid fa-coins me-2"></i>Listado de traspasos</a>
                    <a href="TraspasosAceptados" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Traspasos aceptados</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked me-2"></i>Ingresos</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="IngresosDeFarmacias" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Ingresos Farmacias</a>
                    <a href="Ingresos" class="dropdown-item"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Realizar ingresos</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-truck-ramp-box me-2"></i>Inventarios</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="InventarioSucursales" class="dropdown-item"><i class="fa-solid fa-boxes-stacked me-2"></i>inventario de sucursales</a>
                    <a href="AjusteInsumos" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Insumos</a>
                    <a href="ReportesCedisInventario" class="dropdown-item"><i class="fa-solid fa-money-bills me-2"></i>Reportes Cedis</a>
                    <a href="ReportesInventarios" class="dropdown-item"><i class="fa-solid fa-square-poll-vertical me-2"></i>Reportes inventarios</a>
                </div>
            </div>
            <?php } ?>

            <?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-folder-tree me-2"></i>Reportes</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ReporteDeVentasTotales" class="dropdown-item"><i class="fa-solid fa-file-excel me-2"></i>Reporte de ventas</a>
                    <a href="ReporteServicios" class="dropdown-item"><i class="fa-regular fa-file-excel me-2"></i>Reporte por tipo de servicios</a>
                    <a href="ReporteMasVendidos" class="dropdown-item"><i class="fa-regular fa-file-excel me-2"></i>Reporte mas vendidos</a>
                    <a href="ReportePorProductoFormaPago" class="dropdown-item"><i class="fa-regular fa-file-excel me-2"></i>Reporte por productos</a>
                    <a href="ReportePorFormaDePago" class="dropdown-item"><i class="fa-regular fa-file-excel me-2"></i>Reporte por forma de pago</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </nav>
</div>