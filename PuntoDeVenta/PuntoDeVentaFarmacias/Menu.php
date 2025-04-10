<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="index" class="navbar-brand mx-4 mb-3">
            <h3><i class="fa-solid fa-fish"></i><?php echo $row['Licencia']?></h3>
            <small class="d-block text-secondary"><?php echo $row['Nombre_Sucursal']?></small>
        </a>
        
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0"><?php echo $row['Nombre_Apellidos']?></h6>
                <span class="text-secondary"><?php echo $row['TipoUsuario']?></span>
            </div>
        </div>

        <div class="navbar-nav w-100">
            <a href="index" class="nav-item nav-link active">
                <i class="fa fa-tachometer-alt me-2"></i>Inicio
            </a>
            
            <!-- Menú Farmacia -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-house-medical"></i>Farmacia
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="RegistrosdeEnergia" class="dropdown-item">
                        <i class="fa-solid fa-lightbulb"></i>Registro de energía
                    </a>
                    <a href="BitacoraLimpieza" class="dropdown-item">
                        <i class="fa-solid fa-broom"></i>Bitácora de limpieza
                    </a>
                    <a href="TareasPorHacer" class="dropdown-item">
                        <i class="fa-solid fa-list"></i>Lista de tareas
                    </a>
                    <a href="Mensajes" class="dropdown-item">
                        <i class="fa-solid fa-message"></i>Mensajes
                    </a>
                    <a href="Recordatorios" class="dropdown-item">
                        <i class="fa-solid fa-bell"></i>Recordatorios
                    </a>
                </div>
            </div>
            
            <!-- Menú Cajas -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-cash-register"></i>Cajas
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AperturarCajaV2" class="dropdown-item">
                        <i class="fa-solid fa-coins"></i>Apertura de caja
                    </a>
                    <a href="CortesDeCaja" class="dropdown-item">
                        <i class="fa-solid fa-calculator"></i>Cortes de caja
                    </a>
                    <a href="ReimpresionDeCortes" class="dropdown-item">
                        <i class="fa-solid fa-print"></i>Reimpresión de corte
                    </a>
                </div>
            </div>
            
            <!-- Menú Agenda -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-calendar"></i>Agenda
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="AgendaRevaloraciones" class="dropdown-item">
                        <i class="fa-solid fa-calendar-check"></i>Revaloraciones
                    </a>
                    <a href="AgendaLaboratorios" class="dropdown-item">
                        <i class="fa-solid fa-flask"></i>Laboratorios
                    </a>
                    <a href="AgendaEspecialistas" class="dropdown-item">
                        <i class="fa-solid fa-user-md"></i>Especialistas
                    </a>
                </div>
            </div>
            
            <a href="RealizarVentas" class="nav-item nav-link">
                <i class="fa-solid fa-cart-shopping"></i>Realizar Venta
            </a>
            
            <!-- Menú Clientes -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-users"></i>Clientes
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="DatosDeClientes" class="dropdown-item">
                        <i class="fa-solid fa-people-group"></i>Lista de clientes
                    </a>
                    <a href="Creditos" class="dropdown-item">
                        <i class="fa-regular fa-credit-card"></i>Créditos
                    </a>
                </div>
            </div>
            
            <!-- Menú Tickets -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-ticket"></i>Tickets
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Tickets" class="dropdown-item">
                        <i class="fa-solid fa-receipt"></i>Desglose de tickets
                    </a>
                    <a href="AbonosEnCreditos" class="dropdown-item">
                        <i class="fa-solid fa-money-bill"></i>Tickets crédito
                    </a>
                    <a href="EncargosPendientes" class="dropdown-item">
                        <i class="fa-solid fa-file-invoice"></i>Encargos
                    </a>
                    <a href="Tickets" class="dropdown-item">
                        <i class="fa-solid fa-print"></i>Reimpresión
                    </a>
                </div>
            </div>
            
            <!-- Menú Ventas -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-chart-line"></i>Ventas
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="VentasDelDia" class="dropdown-item">
                        <i class="fa-solid fa-coins"></i>Ventas del día
                    </a>
                    <a href="VentasAcredito" class="dropdown-item">
                        <i class="fa-solid fa-credit-card"></i>Ventas a crédito
                    </a>
                </div>
            </div>
            
            <!-- Menú Almacen -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-boxes-stacked"></i>Almacén
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="StockSucursal" class="dropdown-item">
                        <i class="fa-solid fa-box"></i>Stock
                    </a>
                    <a href="StockInsumos" class="dropdown-item">
                        <i class="fa-solid fa-box-open"></i>Stock de insumos
                    </a>
                    <a href="Pedidos" class="dropdown-item">
                        <i class="fa-solid fa-truck"></i>Pedidos
                    </a>
                    <a href="ConteoDiario" class="dropdown-item">
                        <i class="fa-solid fa-clipboard-list"></i>Conteo diario
                    </a>
                    <a href="Cotizaciones" class="dropdown-item">
                        <i class="fa-solid fa-rotate-left"></i>Devoluciones
                    </a>
                    <a href="Cotizaciones" class="dropdown-item">
                        <i class="fa-solid fa-calendar-xmark"></i>Caducados
                    </a>
                </div>
            </div>
            
            <!-- Menú Pedidos -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-clipboard-check"></i>Pedidos
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="Pedidos" class="dropdown-item">
                        <i class="fa-solid fa-list-check"></i>Pedidos
                    </a>
                    <a href="GeneracionDeEncargos" class="dropdown-item">
                        <i class="fa-solid fa-file-circle-plus"></i>Generar encargo
                    </a>
                    <a href="Cotizaciones" class="dropdown-item">
                        <i class="fa-solid fa-file-invoice-dollar"></i>Generar cotización
                    </a>
                </div>
            </div>
            
            <!-- Menú Traspasos -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-right-left"></i>Traspasos
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="ListadoDeTraspasos" class="dropdown-item">
                        <i class="fa-solid fa-list-ul"></i>Listado de traspasos
                    </a>
                    <a href="Ingresos" class="dropdown-item">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>Solicitar ingresos
                    </a>
                    <a href="SolicitudPendientes" class="dropdown-item">
                        <i class="fa-solid fa-clock"></i>Solicitudes pendientes
                    </a>
                </div>
            </div>
            
            <a href="index" class="nav-item nav-link">
                <i class="fa-solid fa-circle-play"></i>Video tutoriales
            </a>
        </div>
    </nav>
</div>