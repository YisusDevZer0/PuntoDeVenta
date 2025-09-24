<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Control de Caducados - <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Control de Caducados - <?php echo $row['Licencia']?></h6>
            
            <div id="DataDeCaducados"></div>
            </div></div></div></div>
            
          
<script src="js/ControlDeCaducados.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/RegistrarLote.php";
            include "Modales/ActualizarCaducidad.php";
            include "Modales/TransferirLote.php";
            include "Modales/DetallesLote.php";
            include "Modales/ConfiguracionCaducados.php";
            include "Modales/Modales_Errores.php";
            include "footer.php";?>
