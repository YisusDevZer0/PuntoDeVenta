<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos en cedis <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">lista de productos de cedis de <?php echo $row['Licencia']?></h6>
           
    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#FiltroLabs" class="btn btn-default">
      Generar nuevo traspaso <i class="fas fa-exchange-alt"></i>
    </button>
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ModificarTraspasoModal">
      Modificar traspaso <i class="fas fa-edit"></i>
    </button>
    <button type="button" class="btn btn-danger btn-sm " data-toggle="modal" data-target="#CancelarTraspasoModal">
      Cancelar Traspaso <i class="fas fa-ban"></i>
    </button>
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#FiltroTraspasos"
      class="btn btn-default">
      Filtrar por fechas <i class="fas fa-search"></i>
    </button>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/ListadoDeTraspasos.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoTraspasoGenerador";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>