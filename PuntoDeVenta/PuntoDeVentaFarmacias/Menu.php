<div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i style="color: #ef7980!important;" class="fa-solid fa-fish"></i><?php echo $row['Licencia']?><br> <?php echo $row['Nombre_Sucursal']?></h3> 
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
                    <a href="index" class="nav-item nav-link "><i class="fa fa-tachometer-alt me-2"></i>Inicio</a>
                   
                   
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-house-medical"></i>Farmacia</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="RegistrosdeEnergia" class="dropdown-item"><i class="fa-solid fa-lightbulb"></i>Registro de energia</a>
                            <a href="BitacoraLimpieza" class="dropdown-item"><i class="fa-solid fa-broom"></i>Bitacora de limpieza</a>
                            <a href="TareasPorHacer" class="dropdown-item"><i class="fa-solid fa-list"></i>Lista de tareas</a>
                            <a href="Mensajes" class="dropdown-item"><i class="fa-solid fa-business-time"></i>Mensajes</a>
                            <a href="Recordatorios" class="dropdown-item"><i class="fa-solid fa-business-time"></i>Crear recordatorios</a>
                        </div>
                    </div>

                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Cajas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="AperturarCajaV2" class="dropdown-item"><i class="fa-solid fa-coins"></i>Apertura de caja</a>
                            <a href="CortesDeCaja" class="dropdown-item"><i class="fa-solid fa-user-xmark"></i>Cortes de caja</a>
                            <a href="ReimpresionDeCortes" class="dropdown-item"><i class="fa-solid fa-print"></i>Reimpresion de corte</a>
                        </div>
                    </div>
                        
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-regular fa-calendar"></i>Agenda</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="AgendaRevaloraciones" class="dropdown-item"><i class="fa-solid fa-calendar-days"></i>Revaloraciones</a>
                            <a href="AgendaLaboratorios" class="dropdown-item"><i class="fa-solid fa-user-xmark"></i>Laboratorios</a>
                            <a href="AgendaEspecialistas" class="dropdown-item"><i class="fa-solid fa-user-xmark"></i>Especialistas</a>
                        </div>
                    </div>
                    <a href="RealizarVentas" class="nav-item nav-link "><i class="fa-solid fa-cart-shopping"></i>Realizar Venta</a>
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users"></i>Clientes</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="DatosDeClientes" class="dropdown-item"><i class="fa-solid fa-people-group"></i>Lista de clientes</a>
                            <a href="Creditos" class="dropdown-item"><i class="fa-regular fa-credit-card"></i>Creditos </a>
                           
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-ticket"></i>Tickets</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="Tickets" class="dropdown-item"><i class="fa-solid fa-ticket-simple"></i>Desglose de tickets</a>
                            <a href="AbonosEnCreditos" class="dropdown-item"><i class="fa-solid fa-ticket-simple"></i>Desglose de tickets credito</a>
                           
                        </div>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="Tickets" class="dropdown-item"><i class="fa-solid fa-ticket-simple"></i>Reimpresion de tickets</a>
                           
                        </div>
                    </div>

                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Ventas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="VentasDelDia" class="dropdown-item"><i class="fa-solid fa-coins"></i>Ventas del dia</a>
                            <a href="VentasAcredito" class="dropdown-item"><i class="fa-solid fa-coins"></i>Ventas a credito</a>
                           
                        </div>
                    </div>


                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="StockSucursal" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Stock</a>
                            <a href="StockInsumos" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Stock de insumos</a>
                            <a href="Pedidos" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Pedidos</a>
                            <a href="ConteoDiario" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Conteo diario</a>                           
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Devoluciones</a>    
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Caducados</a>    
                           
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Pedidos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                 
                            <a href="Pedidos" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Pedidos</a>
                            <a href="GeneracionDeEncargos" class="dropdown-item"><i class="fa-solid fa-coins"></i>Generar encargo</a>
                            <a href="Cotizaciones" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Generar cotizacion</a>
                                                       
                           
                           
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-right-left"></i>Traspasos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="ListadoDeTraspasos" class="dropdown-item"><i class="fa-solid fa-arrows-turn-to-dots"></i>Listado de traspasos</a>
                            <a href="Ingresos" class="dropdown-item"><i class="fa-solid fa-arrows-turn-to-dots"></i>Solicitar ingresos</a>
                            <a href="SolicitudPendientes" class="dropdown-item"><i class="fa-solid fa-arrows-turn-to-dots"></i>Solicitudes pendientes</a>
                        </div>
                    </div>

                    <a href="index" class="nav-item nav-link "><i class="fa-solid fa-file-video"></i>Video tutoriales</a>
                </div>
            </nav>
        </div>