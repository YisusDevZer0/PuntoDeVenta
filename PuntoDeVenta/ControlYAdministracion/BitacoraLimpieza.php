<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Registro diario de control de energia de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
    <?php
   include "header.php";?>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->
<style>
    .imgpez {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px; /* Esto añade bordes redondeados, opcional */
}

</style>

            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Registros de bitacora de limpieza de  <?php echo $row['Licencia']?></h6>
            <div class="text-center">
            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#RegistroEnergiaVentanaModal">
  Registrar control de energia en watss
</button>  -->
        <br>
<div id="Cajas"></div>
            </div></div></div></div>
            </div>
            <script src="js/RegistrosDeEnergia.js"></script>
            <script src="js/GuardaDatosEnergia.js"></script>
            <!-- Footer Start -->
            <?php 
           include "Modales/RegistroDeEnergia.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
        
</body>

</html>