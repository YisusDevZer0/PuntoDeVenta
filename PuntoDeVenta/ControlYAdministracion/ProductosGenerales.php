<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos de <?php echo $row['Licencia']?></title>
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
          <div class="text-center">
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Base de productos de <?php echo $row['Licencia']?></h6> <br>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AltaProductos">Abrir Modal</button> <br>
            <div id="DataDeProductos"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeProductos.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoProductos.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>