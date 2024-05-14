<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Fondos de cajas disponibles para  <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

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


            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Consultas con especialistas de <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agendar nueva cita
</button> <br>
            <div id="RevaloracionesMedicas"></div>
            </div></div></div></div>
            
            <script src="js/GuardaRevaloracion.js"></script>
<script src="js/ControlRevaloraciones.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/AgendaRevaloracion.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>