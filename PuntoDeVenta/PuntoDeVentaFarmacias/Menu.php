<!-- Wrapper principal -->
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="navbar">
            <!-- Logo y título -->
            <div class="navbar-brand">
                <h3 class="text-primary">
                    <i class="fa-solid fa-fish me-2"></i>
                    <?php echo $row['Licencia']?>
                </h3>
                <small class="text-muted"><?php echo $row['Nombre_Sucursal']?></small>
            </div>

            <!-- Perfil de usuario -->
            <div class="profile-section">
                <div class="d-flex align-items-center">
                    <div class="position-relative">
                        <img class="rounded-circle" src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" alt="">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo $row['Nombre_Apellidos']?></h6>
                        <span><?php echo $row['TipoUsuario']?></span>
                    </div>
                </div>
            </div>

            <!-- Menú de navegación -->
            <div class="navbar-nav">
                <a href="index" class="nav-item nav-link active">
                    <i class="fa fa-tachometer-alt me-2"></i>
                    <span>Inicio</span>
                </a>

                <!-- Menú Farmacia -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-house-medical me-2"></i>
                        <span>Farmacia</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="RegistrosdeEnergia" class="dropdown-item">
                            <i class="fa-solid fa-lightbulb me-2"></i>
                            <span>Registro de energía</span>
                        </a>
                        <a href="BitacoraLimpieza" class="dropdown-item">
                            <i class="fa-solid fa-broom me-2"></i>
                            <span>Bitácora de limpieza</span>
                        </a>
                        <a href="TareasPorHacer" class="dropdown-item">
                            <i class="fa-solid fa-list me-2"></i>
                            <span>Lista de tareas</span>
                        </a>
                        <a href="Mensajes" class="dropdown-item">
                            <i class="fa-solid fa-message me-2"></i>
                            <span>Mensajes</span>
                        </a>
                        <a href="Recordatorios" class="dropdown-item">
                            <i class="fa-solid fa-bell me-2"></i>
                            <span>Recordatorios</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Cajas -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-cash-register me-2"></i>
                        <span>Cajas</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="AperturarCajaV2" class="dropdown-item">
                            <i class="fa-solid fa-coins me-2"></i>
                            <span>Apertura de caja</span>
                        </a>
                        <a href="CortesDeCaja" class="dropdown-item">
                            <i class="fa-solid fa-calculator me-2"></i>
                            <span>Cortes de caja</span>
                        </a>
                        <a href="ReimpresionDeCortes" class="dropdown-item">
                            <i class="fa-solid fa-print me-2"></i>
                            <span>Reimpresión de corte</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Agenda -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-regular fa-calendar me-2"></i>
                        <span>Agenda</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="AgendaRevaloraciones" class="dropdown-item">
                            <i class="fa-solid fa-calendar-check me-2"></i>
                            <span>Revaloraciones</span>
                        </a>
                        <a href="AgendaLaboratorios" class="dropdown-item">
                            <i class="fa-solid fa-flask me-2"></i>
                            <span>Laboratorios</span>
                        </a>
                        <a href="AgendaEspecialistas" class="dropdown-item">
                            <i class="fa-solid fa-user-md me-2"></i>
                            <span>Especialistas</span>
                        </a>
                    </div>
                </div>
                
                <a href="RealizarVentas" class="nav-item nav-link">
                    <i class="fa-solid fa-cart-shopping me-2"></i>
                    <span>Realizar Venta</span>
                </a>
                
                <!-- Menú Clientes -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-users me-2"></i>
                        <span>Clientes</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="DatosDeClientes" class="dropdown-item">
                            <i class="fa-solid fa-people-group me-2"></i>
                            <span>Lista de clientes</span>
                        </a>
                        <a href="Creditos" class="dropdown-item">
                            <i class="fa-regular fa-credit-card me-2"></i>
                            <span>Créditos</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Tickets -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-ticket me-2"></i>
                        <span>Tickets</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="Tickets" class="dropdown-item">
                            <i class="fa-solid fa-receipt me-2"></i>
                            <span>Desglose de tickets</span>
                        </a>
                        <a href="AbonosEnCreditos" class="dropdown-item">
                            <i class="fa-solid fa-money-bill me-2"></i>
                            <span>Tickets crédito</span>
                        </a>
                        <a href="EncargosPendientes" class="dropdown-item">
                            <i class="fa-solid fa-file-invoice me-2"></i>
                            <span>Encargos</span>
                        </a>
                        <a href="Tickets" class="dropdown-item">
                            <i class="fa-solid fa-print me-2"></i>
                            <span>Reimpresión</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Ventas -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        <span>Ventas</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="VentasDelDia" class="dropdown-item">
                            <i class="fa-solid fa-coins me-2"></i>
                            <span>Ventas del día</span>
                        </a>
                        <a href="VentasAcredito" class="dropdown-item">
                            <i class="fa-solid fa-credit-card me-2"></i>
                            <span>Ventas a crédito</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Almacen -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-boxes-stacked me-2"></i>
                        <span>Almacén</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="StockSucursal" class="dropdown-item">
                            <i class="fa-solid fa-box me-2"></i>
                            <span>Stock</span>
                        </a>
                        <a href="StockInsumos" class="dropdown-item">
                            <i class="fa-solid fa-box-open me-2"></i>
                            <span>Stock de insumos</span>
                        </a>
                        <a href="Pedidos" class="dropdown-item">
                            <i class="fa-solid fa-truck me-2"></i>
                            <span>Pedidos</span>
                        </a>
                        <a href="ConteoDiario" class="dropdown-item">
                            <i class="fa-solid fa-clipboard-list me-2"></i>
                            <span>Conteo diario</span>
                        </a>
                        <a href="Cotizaciones" class="dropdown-item">
                            <i class="fa-solid fa-rotate-left me-2"></i>
                            <span>Devoluciones</span>
                        </a>
                        <a href="Cotizaciones" class="dropdown-item">
                            <i class="fa-solid fa-calendar-xmark me-2"></i>
                            <span>Caducados</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Pedidos -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-clipboard-check me-2"></i>
                        <span>Pedidos</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="Pedidos" class="dropdown-item">
                            <i class="fa-solid fa-list-check me-2"></i>
                            <span>Pedidos</span>
                        </a>
                        <a href="GeneracionDeEncargos" class="dropdown-item">
                            <i class="fa-solid fa-file-circle-plus me-2"></i>
                            <span>Generar encargo</span>
                        </a>
                        <a href="Cotizaciones" class="dropdown-item">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                            <span>Generar cotización</span>
                        </a>
                    </div>
                </div>
                
                <!-- Menú Traspasos -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-right-left me-2"></i>
                        <span>Traspasos</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="ListadoDeTraspasos" class="dropdown-item">
                            <i class="fa-solid fa-list-ul me-2"></i>
                            <span>Listado de traspasos</span>
                        </a>
                        <a href="Ingresos" class="dropdown-item">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
                            <span>Solicitar ingresos</span>
                        </a>
                        <a href="SolicitudPendientes" class="dropdown-item">
                            <i class="fa-solid fa-clock me-2"></i>
                            <span>Solicitudes pendientes</span>
                        </a>
                    </div>
                </div>
                
                <a href="index" class="nav-item nav-link">
                    <i class="fa-solid fa-circle-play me-2"></i>
                    <span>Video tutoriales</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="content-inner">
            <!-- Aquí va el contenido de la página -->
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div>Doctor Pez &copy; <?php echo date('Y'); ?> - Todos los derechos reservados.</div>
    <div>Diseñado por DevZero</div>
</footer>

<!-- Script para toggle de sidebar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');
        const footer = document.querySelector('.footer');
        
        sidebarToggler.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (window.innerWidth > 992) {
                if (sidebar.classList.contains('active')) {
                    content.style.marginLeft = '0';
                    content.style.width = '100%';
                    footer.style.left = '0';
                } else {
                    content.style.marginLeft = '280px';
                    content.style.width = 'calc(100% - 280px)';
                    footer.style.left = '280px';
                }
            }
        });
    });
</script>