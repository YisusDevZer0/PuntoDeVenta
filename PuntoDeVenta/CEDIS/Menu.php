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
                   
                   
                    

                    <a href="Sucursales" class="nav-item nav-link"><i class="fa-solid fa-house-medical"></i>Sucursales</a>
                    
                   
                    
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-file-invoice-dollar"></i>Ventas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="VentasDelDia" class="dropdown-item"><i class="fa-solid fa-coins"></i>Ventas del dia</a>
                            <a href="VentasACredito" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Ventas del dia de Credito </a>
                           
                        </div>
                    </div>


                    <!-- MENUS DE ALMACEN -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="ProductosGenerales" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Historial general </a>
                            <a href="ControlCedis" class="dropdown-item"><i class="fa-solid fa-house-medical"></i>CEDIS </a>
                            <a href="Stocks" class="dropdown-item"><i class="fa-solid fa-house-medical-flag"></i>Stocks </a>
                            <a href="Proveedores" class="dropdown-item"><i class="fa-solid fa-truck-field"></i>Proveedores </a> 
                            <a href="Servicios" class="dropdown-item"><i class="fa-solid fa-staff-snake"></i>Servicios </a>
                            <a href="Marcas" class="dropdown-item"><i class="fa-solid fa-staff-snake"></i>Marcas </a>
                            <a href="Presentaciones" class="dropdown-item"><i class="fa-solid fa-staff-snake"></i>Presentaciones </a>
                            <a href="Componentes" class="dropdown-item"><i class="fa-solid fa-staff-snake"></i>Componentes </a>
                            <a href="TiposProductos" class="dropdown-item"><i class="fa-solid fa-staff-snake"></i>Tipos productos </a>
                            
                           
                        </div>
                    </div>
             <!-- MENUS DE ALMACEN -->

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Traspasos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="ListaDeTraspasos" class="dropdown-item"><i class="fa-solid fa-coins"></i>Listado de traspasos</a>
                            <a href="TraspasosAceptados" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Traspasos aceptados </a>
                           
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Inventarios</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="InventarioCedis" class="dropdown-item"><i class="fa-solid fa-coins"></i>Inventario de cedis</a>
                            <a href="InventarioSucursales" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>inventario de sucursales </a>
                            <a href="AjusteInventarios" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Ajuste de inventarios </a>
                           
                        </div>
                    </div>
                    
                   
            </nav>
        </div>