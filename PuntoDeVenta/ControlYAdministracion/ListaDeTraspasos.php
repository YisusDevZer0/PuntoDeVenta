<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Lista de traspasos - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0" style="color:#0172b6;">
                    <i class="fa-solid fa-truck-fast me-2"></i>
                    Lista de traspasos - <?php echo $row['Licencia']?>
                </h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" onclick="CargaServicios()">
                        <i class="fa-solid fa-refresh me-1"></i>Actualizar
                    </button>
                </div>
            </div>

            <!-- Filtros (mismo estilo que otros módulos) -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <label class="form-label">Sucursal origen:</label>
                    <select class="form-select form-select-sm" id="filtroSucursalOrigen" onchange="CargaServicios()">
                        <option value="">Todas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sucursal destino:</label>
                    <select class="form-select form-select-sm" id="filtroSucursalDestino" onchange="CargaServicios()">
                        <option value="">Todas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha desde:</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFechaDesde" onchange="CargaServicios()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha hasta:</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFechaHasta" onchange="CargaServicios()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estatus:</label>
                    <select class="form-select form-select-sm" id="filtroEstatus" onchange="CargaServicios()">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Enviado">Enviado</option>
                        <option value="Recibido">Recibido</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimpiarFiltrosTraspasos">
                        <i class="fa-solid fa-eraser me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>

            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/ListadoDeTraspasos.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/GenerarNuevoTraspaso.php";
            include "Modales/GenerarNuevoTraspasoSucursales.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>