<?php 
// Función para determinar si el botón debe estar deshabilitado
function isButtonDisabled($tipoUsuario) {
    return $tipoUsuario == 'MKT' ? 'disabled style="opacity: 0.5; pointer-events: none;"' : '';
}

// Obtener el tipo de usuario actual
$tipoUsuario = $row['TipoUsuario'];
$disabledAttr = isButtonDisabled($tipoUsuario);
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
                <a href="index" class="nav-item nav-link active" <?php echo $disabledAttr; ?>><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>

<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bill-transfer"></i>Punto de venta</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="AperturarCajaV2" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-cash-register"></i> Apertura de caja</a>
        <a href="RealizarVentas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-hand-holding-dollar"></i> Realizar Ventas</a>
        <a href="GeneracionDeEncargos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice"></i>Encargos</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-house-medical"></i>Farmacia</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="RegistrosdeEnergia" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
        <a href="BitacoraLimpieza" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
        <a href="TareasPorHacer" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-list"></i>Lista de tareas</a>
        <a href="Mensajes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-business-time"></i>Mensajes</a>
        <a href="Recordatorios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-business-time"></i>Crear recordatorios</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-business-time"></i>Agendas</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="AgendaRevaloraciones" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-person-walking-arrow-loop-left"></i>Revaloraciones</a>
        <a href="AgendaEspecialista" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-hospital-user"></i>Especialistas </a>
       
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users-gear"></i></i>Personal</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="TiposUsuarios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i> Tipos de usuarios</a>
        <a href="PersonalActivo" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-user-check"></i>Personal activo </a>
        <a href="Personaldebaja" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-user-xmark"></i>Personal inactivo</a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'Desarrollo Humano' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-clock"></i></i>Checador</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ChecadorDiario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar-day"></i>Reporte diario</a>
        <a href="ChecadorGeneral" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-calendar"></i>Reporte General </a>

    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<a href="Sucursales" class="nav-item nav-link" <?php echo $disabledAttr; ?>><i class="fa-solid fa-house-medical"></i>Sucursales</a>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
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
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i>Clientes</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="DatosDeClientes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-people-group"></i>Data de clientes</a>
        <a href="CajasActivas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-credit-card"></i>Creditos </a>
       
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-receipt"></i></i></i></i>Tickets</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="Tickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Desglose de tickets</a>
        <a href="AbonosEnCreditos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Desglose Tickets Creditos </a>
        <a href="CancelacionDeTickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-ban"></i>Cancelacion de tickets </a>
        <a href="EdicionDeTickets" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-pen"></i>Edicion de tickets</a>
       
    </div>
</div>

<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Ventas</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="VentasDelDia" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Ventas del dia</a>
        <a href="VentasACredito" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Ventas del dia de Credito </a>
        <a href="TotalesDeVentaPorVendedor" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-users"></i>Ventas por personal </a>
    </div>
</div>

<?php } ?>
<!-- MENUS DE ALMACEN -->
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ProductosGenerales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-dolly"></i>Historico general </a>
      
        <a href="Stocks" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-house-medical-flag"></i>Stocks </a>
        <a href="Proveedores" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-field"></i>Proveedores </a> 
        <a href="Servicios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Servicios </a>
        <a href="Marcas" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Marcas </a>
        <a href="Presentaciones" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Presentaciones </a>
        <a href="Componentes" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Componentes </a>
        <a href="TiposProductos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Tipos productos </a>
        <a href="Categorias" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-staff-snake"></i>Categorias </a>
       
    </div>
</div>
<?php } ?>
<!-- MENUS DE ALMACEN -->

<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-arrow-right-arrow-left"></i>Traspasos</a>
    <div class="dropdown-menu bg-transparent border-0">
    <a href="RealizarTraspasos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-ramp-box"></i>Realizar traspasos</a>
        <a href="ListaDeTraspasos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-coins"></i>Listado de traspasos</a>
      
        <a href="TraspasosAceptados" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Traspasos aceptados </a>
      
    </div>
</div>



<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>Ingresos</a>
    <div class="dropdown-menu bg-transparent border-0">
       
    
        <!-- <a href="IngresosDeCedis" class="dropdown-item"><i class="fa-solid fa-file-invoice"></i>Ingresos Cedis</a> -->
        <a href="IngresosDeFarmacias" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Ingresos Farmacias </a>
        <a href="Ingresos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-invoice-dollar"></i>Realizar ingresos </a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-truck-ramp-box"></i>Inventarios</a>
    <div class="dropdown-menu bg-transparent border-0">
        <!-- <a href="InventarioCedis" class="dropdown-item"><i class="fa-solid fa-warehouse"></i>Inventario de cedis</a> -->
        <a href="InventarioSucursales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-boxes-stacked"></i>inventario de sucursales </a>
        <!-- <a href="AjusteInventarios" class="dropdown-item"><i class="fa-solid fa-plus-minus"></i>Ajuste de inventarios </a> -->
        <a href="AjusteInsumos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Insumos </a>
        <a href="ReportesCedisInventario" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-money-bills"></i>Reportes Cedis </a>
        <a href="ReportesInventarios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-square-poll-vertical"></i>Reportes inventarios </a>
    </div>
</div>
<?php } ?>
<?php if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') { ?>
<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" <?php echo $disabledAttr; ?>><i class="fa-solid fa-folder-tree"></i>Reportes</a>
    <div class="dropdown-menu bg-transparent border-0">
        <a href="ReporteDeVentasTotales" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-solid fa-file-excel"></i>Reporte de ventas</a>
        <a href="ReporteServicios" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte por tipo de servicios </a>
        <a href="ReporteMasVendidos" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte mas vendidos </a>
        <a href="ReportePorProductoFormaPago" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte por productos </a>
        <a href="ReportePorFormaDePago" class="dropdown-item" <?php echo $disabledAttr; ?>><i class="fa-regular fa-file-excel"></i>Reporte por forma de pago</a>
       
    </div>
</div>

<?php } ?>

            </nav>
        </div>