<div class="sidebar pe-4 pb-3" style="background: #f8fafc; min-height: 100vh; box-shadow: 2px 0 8px rgba(0,0,0,0.04); border-radius: 0 18px 18px 0;">
    <nav class="navbar bg-light navbar-light" style="border-radius: 0 18px 18px 0;">
        <a href="index.html" class="navbar-brand mx-4 mb-3 d-flex align-items-center">
            <h3 class="text-primary mb-0" style="font-weight: bold;"><i style="color: #ef7980!important; margin-right: 8px;" class="fa-solid fa-fish"></i><?php echo $row['Licencia']?></h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative shadow" style="box-shadow: 0 2px 8px rgba(0,0,0,0.10);">
                <img class="rounded-circle border border-3 border-white" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 60px; height: 60px; object-fit: cover;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0" style="font-weight: 600; color: #222; font-size: 1.1rem;"><?php echo $row['Nombre_Apellidos']?></h6>
                <span style="color: #888; font-size: 0.95rem;"> <?php echo $row['TipoUsuario']?></span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="index" class="nav-item nav-link py-2 px-4 border-start border-4 border-transparent rounded-pill mb-1" style="transition: all 0.2s; font-weight: 500;">
                <i class="fa fa-tachometer-alt me-2 text-primary"></i>Inicio
            </a>

            <div class="nav-item">
                <a href="#PuntoVentaMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-money-bill-transfer me-2 text-primary"></i>Punto de venta<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="PuntoVentaMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="AperturarCajaV2" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-cash-register me-2 text-primary"></i>Apertura de caja</a>
                        <a href="RealizarVentas" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i>Realizar Ventas</a>
                        <a href="GeneracionDeEncargos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Encargos</a>
                    </div>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="#FarmaciaMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-house-medical me-2 text-primary"></i>Farmacia<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="FarmaciaMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="RegistrosdeEnergia" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-lightbulb me-2 text-primary"></i>Registro de energía</a>
                        <a href="BitacoraLimpieza" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-broom me-2 text-primary"></i>Bitácora de limpieza</a>
                        <a href="TareasPorHacer" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-list me-2 text-primary"></i>Lista de tareas</a>
                        <a href="Mensajes" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-message me-2 text-primary"></i>Mensajes</a>
                        <a href="Recordatorios" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-bell me-2 text-primary"></i>Crear recordatorios</a>
                    </div>
                </div>
            </div>
           
            <div class="nav-item">
                <a href="#AgendasMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-calendar me-2 text-primary"></i>Agendas<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="AgendasMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="AgendaRevaloraciones" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-person-walking-arrow-loop-left me-2 text-primary"></i>Revaloraciones</a>
                        <a href="AgendaEspecialista" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-hospital-user me-2 text-primary"></i>Especialistas</a>
                    </div>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="#CajasMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-cash-register me-2 text-primary"></i>Cajas<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="CajasMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="CortesDeCaja" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Cortes de caja</a>
                        <a href="RegistroDeGastos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-comment-dollar me-2 text-primary"></i>Registro de gastos</a>
                    </div>
                </div>
            </div>
           
            <div class="nav-item">
                <a href="#ClientesMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-users me-2 text-primary"></i>Clientes<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="ClientesMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="DatosDeClientes" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-people-group me-2 text-primary"></i>Lista de clientes</a>
                        <a href="Creditos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-regular fa-credit-card me-2 text-primary"></i>Créditos</a>
                    </div>
                </div>
            </div>

            <div class="nav-item">
                <a href="#TicketsMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-receipt me-2 text-primary"></i>Tickets<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="TicketsMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="Tickets" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-ticket-simple me-2 text-primary"></i>Desglose de tickets</a>
                        <a href="AbonosEnCreditos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-money-bills me-2 text-primary"></i>Tickets de crédito</a>
                        <a href="EncargosPendientes" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Encargos</a>
                    </div>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="#VentasMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-chart-line me-2 text-primary"></i>Ventas<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="VentasMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="VentasDelDia" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-coins me-2 text-primary"></i>Ventas del día</a>
                        <a href="VentasAcredito" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-credit-card me-2 text-primary"></i>Ventas a crédito</a>
                    </div>
                </div>
            </div>

            <div class="nav-item">
                <a href="#AlmacenMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Almacén<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="AlmacenMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="StockSucursal" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-dolly me-2 text-primary"></i>Stock</a>
                        <a href="StockInsumos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-box me-2 text-primary"></i>Stock de insumos</a>
                        <a href="Pedidos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-truck me-2 text-primary"></i>Órdenes de pedidos</a>
                        <a href="ConteoDiario" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Conteo diario</a>
                        <a href="Cotizaciones" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Devoluciones</a>
                        <a href="Cotizaciones" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-calendar-xmark me-2 text-primary"></i>Caducados</a>
                    </div>
                </div>
            </div>

            <div class="nav-item">
                <a href="#TraspasosMenu" data-bs-toggle="collapse" class="nav-link py-2 px-4 border-start border-4 border-transparent collapsed">
                    <i class="fa-solid fa-arrow-right-arrow-left me-2 text-primary"></i>Traspasos<i class="fa fa-angle-down ms-auto"></i>
                </a>
                <div class="collapse" id="TraspasosMenu">
                    <div style="background-color: rgba(0,188,212,0.03); margin-left: 30px;">
                        <a href="ListadoDeTraspasos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-list-check me-2 text-primary"></i>Listado de traspasos</a>
                        <a href="Ingresos" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-inbox me-2 text-primary"></i>Solicitar ingresos</a>
                        <a href="SolicitudPendientes" class="nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Solicitudes pendientes</a>
                    </div>
                </div>
            </div>

            <a href="videos" class="nav-item nav-link py-2 px-4 border-start border-4 border-transparent"><i class="fa-solid fa-circle-play me-2 text-primary"></i>Video tutoriales</a>
        </div>

    </nav>
</div>

<style>
    .sidebar .nav-link {
        color: #222;
        border-radius: 18px;
    }
    .sidebar .nav-link.active, .sidebar .nav-link:hover {
        background: #e3f2fd;
        color: #0172b6;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(0,123,255,0.07);
    }
    .sidebar .nav-link i {
        transition: color 0.2s;
    }
    .sidebar .nav-link.active i, .sidebar .nav-link:hover i {
        color: #0172b6 !important;
    }
</style>