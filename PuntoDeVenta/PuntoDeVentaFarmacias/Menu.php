<div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hospital me-2"></i><?php echo $row['Licencia']?><br> <?php echo $row['Nombre_Sucursal']?></h3>
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
                    <a href="index" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-hospital me-2"></i>Farmacia</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa fa-bolt me-2"></i>Registro de energía</a>
                            <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa fa-broom me-2"></i>Bitácora de limpieza</a>
                            <a href="TareasPorHacer" class="dropdown-item"><i class="fa fa-tasks me-2"></i>Lista de tareas</a>
                            <a href="Mensajes" class="dropdown-item"><i class="fa fa-envelope me-2"></i>Mensajes</a>
                            <a href="Recordatorios" class="dropdown-item"><i class="fa fa-bell me-2"></i>Crear recordatorios</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cash-register me-2"></i>Cajas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="AperturarCajaV2" class="dropdown-item"><i class="fa fa-door-open me-2"></i>Apertura de caja</a>
                            <a href="CortesDeCaja" class="dropdown-item"><i class="fa fa-file-invoice-dollar me-2"></i>Cortes de caja</a>
                            <a href="ReimpresionDeCortes" class="dropdown-item"><i class="fa fa-print me-2"></i>Reimpresión de corte</a>
                        </div>
                    </div>
                        
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-calendar me-2"></i>Agenda</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa fa-calendar-check me-2"></i>Revaloraciones</a>
                            <a href="AgendaLaboratorios" class="dropdown-item"><i class="fa fa-flask me-2"></i>Laboratorios</a>
                            <a href="AgendaEspecialistas" class="dropdown-item"><i class="fa fa-user-md me-2"></i>Especialistas</a>
                        </div>
                    </div>
                    <a href="RealizarVentas" class="nav-item nav-link"><i class="fa fa-shopping-cart me-2"></i>Realizar Venta</a>
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users me-2"></i>Clientes</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="DatosDeClientes" class="dropdown-item"><i class="fa fa-user-friends me-2"></i>Lista de clientes</a>
                            <a href="Creditos" class="dropdown-item"><i class="fa fa-credit-card me-2"></i>Créditos</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-receipt me-2"></i>Tickets</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="Tickets" class="dropdown-item"><i class="fa fa-file-invoice me-2"></i>Desglose de tickets</a>
                            <a href="AbonosEnCreditos" class="dropdown-item"><i class="fa fa-file-invoice-dollar me-2"></i>Desglose de tickets crédito</a>
                            <a href="EncargosPendientes" class="dropdown-item"><i class="fa fa-clipboard-list me-2"></i>Encargos</a>
                            <a href="Tickets" class="dropdown-item"><i class="fa fa-print me-2"></i>Reimpresión de tickets</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-chart-line me-2"></i>Ventas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="VentasDelDia" class="dropdown-item"><i class="fa fa-calendar-day me-2"></i>Ventas del día</a>
                            <a href="VentasAcredito" class="dropdown-item"><i class="fa fa-credit-card me-2"></i>Ventas a crédito</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-warehouse me-2"></i>Almacén</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="StockSucursal" class="dropdown-item"><i class="fa fa-boxes me-2"></i>Stock</a>
                            <a href="StockInsumos" class="dropdown-item"><i class="fa fa-box me-2"></i>Stock de insumos</a>
                            <a href="Pedidos" class="dropdown-item"><i class="fa fa-shopping-basket me-2"></i>Pedidos</a>
                            <a href="ConteoDiario" class="dropdown-item"><i class="fa fa-clipboard-check me-2"></i>Conteo diario</a>
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa fa-undo me-2"></i>Devoluciones</a>
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa fa-exclamation-triangle me-2"></i>Caducados</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-shopping-basket me-2"></i>Pedidos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="Pedidos" class="dropdown-item"><i class="fa fa-clipboard-list me-2"></i>Pedidos</a>
                            <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa fa-file-invoice me-2"></i>Generar encargo</a>
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa fa-file-invoice-dollar me-2"></i>Generar cotización</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-exchange-alt me-2"></i>Traspasos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="ListadoDeTraspasos" class="dropdown-item"><i class="fa fa-list me-2"></i>Listado de traspasos</a>
                            <a href="Ingresos" class="dropdown-item"><i class="fa fa-sign-in-alt me-2"></i>Solicitar ingresos</a>
                            <a href="SolicitudPendientes" class="dropdown-item"><i class="fa fa-clock me-2"></i>Solicitudes pendientes</a>
                        </div>
                    </div>

                    <a href="index" class="nav-item nav-link"><i class="fa fa-video me-2"></i>Video tutoriales</a>
                </div>
            </nav>
        </div>