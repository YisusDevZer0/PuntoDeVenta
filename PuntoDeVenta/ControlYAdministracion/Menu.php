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
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-money-bill-transfer"></i>Punto de venta</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="AperturarCajaV2"><i class="fa-solid fa-cash-register"></i> Apertura de caja</a>
        <a href="RealizarVentas"><i class="fa-solid fa-hand-holding-dollar"></i> Realizar Ventas</a>
        <a href="GeneracionDeEncargos"><i class="fa-solid fa-file-invoice"></i>Encargos</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-house-medical"></i>Farmacia</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="RegistrosdeEnergia"><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
        <a href="BitacoraLimpieza"><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
        <a href="TareasPorHacer"><i class="fa-solid fa-list"></i>Lista de tareas</a>
        <a href="Mensajes"><i class="fa-solid fa-business-time"></i>Mensajes</a>
        <a href="Recordatorios"><i class="fa-solid fa-business-time"></i>Crear recordatorios</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-business-time"></i>Agendas</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="AgendaRevaloraciones"><i class="fa-solid fa-person-walking-arrow-loop-left"></i>Revaloraciones</a>
        <a href="AgendaEspecialista"><i class="fa-solid fa-hospital-user"></i>Especialistas </a>
       
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users-gear"></i></i>Personal</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="TiposUsuarios"><i class="fa-solid fa-users"></i> Tipos de usuarios</a>
        <a href="PersonalActivo"><i class="fa-solid fa-user-check"></i>Personal activo </a>
        <a href="Personaldebaja"><i class="fa-solid fa-user-xmark"></i>Personal inactivo</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-clock"></i></i>Checador</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ChecadorDiario"><i class="fa-solid fa-calendar-day"></i>Reporte diario</a>
        <a href="ChecadorGeneral"><i class="fa-solid fa-calendar"></i>Reporte General </a>

    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<a href="Sucursales" class="nav-item nav-link"><i class="fa-solid fa-house-medical"></i>Sucursales</a>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Cajas</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="FondosDeCaja"><i class="fa-solid fa-coins"></i> Fondos de caja</a>
        <a href="CajasActivas"><i class="fa-solid fa-money-bills"></i>Cajas activas </a>
        <a href="HistorialDeCajas"><i class="fa-solid fa-vault"></i>Historial de cajas </a>
        <a href="CortesDeCaja"><i class="fa-solid fa-file-invoice-dollar"></i>Cortes de caja</a>
        <a href="RegistroDeGastos"><i class="fa-solid fa-comment-dollar"></i>Registro de gastos</a>
        <a href="TiposDeGastos"><i class="fa-solid fa-comments-dollar"></i>Tipos de gastos</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users"></i>Clientes</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="DatosDeClientes"><i class="fa-solid fa-people-group"></i>Data de clientes</a>
        <a href="CajasActivas"><i class="fa-regular fa-credit-card"></i>Creditos </a>
       
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-receipt"></i></i></i></i>Tickets</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="Tickets"><i class="fa-solid fa-coins"></i>Desglose de tickets</a>
        <a href="AbonosEnCreditos"><i class="fa-solid fa-money-bills"></i>Desglose Tickets Creditos </a>
        <a href="CancelacionDeTickets"><i class="fa-solid fa-ban"></i>Cancelacion de tickets </a>
        <a href="EdicionDeTickets"><i class="fa-solid fa-file-pen"></i>Edicion de tickets</a>
       
    </div>
</div>

<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-file-invoice-dollar"></i>Ventas</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="VentasDelDia"><i class="fa-solid fa-coins"></i>Ventas del dia</a>
        <a href="VentasACredito"><i class="fa-solid fa-money-bills"></i>Ventas del dia de Credito </a>
        <a href="TotalesDeVentaPorVendedor"><i class="fa-solid fa-users"></i>Ventas por personal </a>
    </div>
</div>

<?php } ?>
<!-- MENUS DE ALMACEN -->
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ProductosGenerales"><i class="fa-solid fa-dolly"></i>Historico general </a>
      
        <a href="Stocks"><i class="fa-solid fa-house-medical-flag"></i>Stocks </a>
        <a href="Proveedores"><i class="fa-solid fa-truck-field"></i>Proveedores </a> 
        <a href="Servicios"><i class="fa-solid fa-staff-snake"></i>Servicios </a>
        <a href="Marcas"><i class="fa-solid fa-staff-snake"></i>Marcas </a>
        <a href="Presentaciones"><i class="fa-solid fa-staff-snake"></i>Presentaciones </a>
        <a href="Componentes"><i class="fa-solid fa-staff-snake"></i>Componentes </a>
        <a href="TiposProductos"><i class="fa-solid fa-staff-snake"></i>Tipos productos </a>
        <a href="Categorias"><i class="fa-solid fa-staff-snake"></i>Categorias </a>
       
    </div>
</div>
<?php } ?>
<!-- MENUS DE ALMACEN -->

<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-arrow-right-arrow-left"></i>Traspasos</a>
    <div class="dropdown-menu bg-transparent border-0">
    <a href="RealizarTraspasos"><i class="fa-solid fa-truck-ramp-box"></i>Realizar traspasos</a>
        <a href="ListaDeTraspasos"><i class="fa-solid fa-coins"></i>Listado de traspasos</a>
      
        <a href="TraspasosAceptados"><i class="fa-solid fa-money-bills"></i>Traspasos aceptados </a>
      
    </div>
</div>



<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Ingresos</a>
    <div class="dropdown-menu bg-transparent border-0">
       
    
        <!-- <a href="IngresosDeCedis" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Ingresos Cedis</a> -->
        <a href="IngresosDeFarmacias"><i class="fa-solid fa-file-invoice-dollar"></i>Ingresos Farmacias </a>
        <a href="Ingresos"><i class="fa-solid fa-file-invoice-dollar"></i>Realizar ingresos </a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-truck-ramp-box"></i>Inventarios</a>
    <div class="dropdown-menu bg-transparent border-0">
        <!-- <a href="InventarioCedis" class="dropdown-item"><i class="fa-solid fa-warehouse"></i>Inventario de cedis</a> -->
        <a href="InventarioSucursales"><i class="fa-solid fa-boxes-stacked"></i>inventario de sucursales </a>
        <!-- <a href="AjusteInventarios" class="dropdown-item"><i class="fa-solid fa-plus-minus"></i>Ajuste de inventarios </a> -->
        <a href="AjusteInsumos"><i class="fa-solid fa-money-bills"></i>Insumos </a>
        <a href="ReportesCedisInventario"><i class="fa-solid fa-money-bills"></i>Reportes Cedis </a>
        <a href="ReportesInventarios"><i class="fa-solid fa-square-poll-vertical"></i>Reportes inventarios </a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-folder-tree"></i>Reportes</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ReporteDeVentasTotales"><i class="fa-solid fa-file-excel"></i>Reporte de ventas</a>
        <a href="ReporteServicios"><i class="fa-regular fa-file-excel"></i>Reporte por tipo de servicios </a>
        <a href="ReporteMasVendidos"><i class="fa-regular fa-file-excel"></i>Reporte mas vendidos </a>
        <a href="ReportePorProductoFormaPago"><i class="fa-regular fa-file-excel"></i>Reporte por productos </a>
        <a href="ReportePorFormaDePago"><i class="fa-regular fa-file-excel"></i>Reporte por forma de pago</a>
       
    </div>
</div>

<?php } ?>

            </nav>
        </div>