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
                   
                   
                

                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Cajas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="AperturarCaja" class="dropdown-item"><i class="fa-solid fa-coins"></i>Apertura de caja</a>
                            <a href="CortesDeCaja" class="dropdown-item"><i class="fa-solid fa-user-xmark"></i>Cortes de caja</a>
                        </div>
                    </div>
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-users"></i>Clientes</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="DatosDeClientes" class="dropdown-item"><i class="fa-solid fa-people-group"></i>Data de clientes</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa-regular fa-credit-card"></i>Creditos </a>
                           
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><<i class="fa-solid fa-ticket"></i>Tickets</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa-solid fa-ticket-simple"></i>Desglose de tickets</a>
                           
                        </div>
                    </div>

                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Ventas</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa-solid fa-coins"></i>Ventas del dia</a>
                           
                        </div>
                    </div>


                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-boxes-stacked"></i>Almacen</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa-solid fa-dolly"></i>Stock</a>
                            
                           
                        </div>
                    </div>


                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-cash-register"></i></i></i>Traspasos</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="FondosDeCaja" class="dropdown-item"><i class="fa-solid fa-coins"></i>Control de productos</a>
                            <a href="CajasActivas" class="dropdown-item"><i class="fa-solid fa-money-bills"></i>Ventas del dia de Credito </a>
                           
                        </div>
                    </div>
                    
                </div>
            </nav>
        </div>