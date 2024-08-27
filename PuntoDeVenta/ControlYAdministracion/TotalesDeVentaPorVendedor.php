<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ventas del dia de <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Registro de ventas del dia <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#BusquedaVentasSucursal">
  Filtrar por sucursal <i class="fas fa-clinic-medical"></i>
</button>
<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#FiltroEspecificoMesxd" class="btn btn-default">
  Busqueda por mes <i class="fas fa-calendar-week"></i>
</button>
<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#FiltroEspecificoFechaVentas" class="btn btn-default">
  Filtrar por rango de fechas <i class="fas fa-calendar"></i>
</button> <br><br>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/VentasVendedor.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/FiltroPorSucursales.php";
            include "Modales/FiltroPorMeses.php";
            include "Modales/FiltroPorVendedor.php";
            include "Modales/FiltroRangoFechas.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>