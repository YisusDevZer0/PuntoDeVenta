<div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hospital me-2"></i><?php echo $row['Licencia']?></h3>
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
                   
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users me-2"></i>Personal</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="TiposUsuarios" class="dropdown-item"><i class="fa fa-user-tag me-2"></i>Tipos de usuarios</a>
                            <a href="PersonalActivo" class="dropdown-item"><i class="fa fa-user-check me-2"></i>Personal activo</a>
                            <a href="Personaldebaja" class="dropdown-item"><i class="fa fa-user-times me-2"></i>Personal inactivo</a>
                        </div>
                    </div>

                    <a href="Sucursales" class="nav-item nav-link"><i class="fa fa-hospital me-2"></i>Sucursales</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cash-register me-2"></i>Cajas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa fa-money-bill me-2"></i>Fondos de caja</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-cash-register me-2"></i>Cajas activas</a>
                            <a href="HistorialDeCajas" class="dropdown-item"><i class="fa fa-history me-2"></i>Historial de cajas</a>
                            <a href="CortesDeCaja" class="dropdown-item"><i class="fa fa-file-invoice-dollar me-2"></i>Cortes de caja</a>
                        </div>
                    </div>
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users me-2"></i>Clientes</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="DatosDeClientes" class="dropdown-item"><i class="fa fa-user-friends me-2"></i>Data de clientes</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-credit-card me-2"></i>Créditos</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-receipt me-2"></i>Tickets</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa fa-file-invoice me-2"></i>Desglose de tickets</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-file-invoice-dollar me-2"></i>Desglose Tickets Créditos</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-chart-line me-2"></i>Ventas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa fa-calendar-day me-2"></i>Ventas del día</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-calendar-check me-2"></i>Ventas del día de Crédito</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-warehouse me-2"></i>Almacén</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa fa-boxes me-2"></i>Control de productos</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-exchange-alt me-2"></i>Traspasos</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-exchange-alt me-2"></i>Traspasos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa fa-boxes me-2"></i>Control de productos</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa fa-exchange-alt me-2"></i>Traspasos entre sucursales</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>