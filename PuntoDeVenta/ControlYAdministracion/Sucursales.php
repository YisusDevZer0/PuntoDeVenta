<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Personal activo <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
            <h6 class="mb-4">Sucursales de <?php echo $row['Nombre_Licencia']?></h6>
            <div id="SucursalesDisponibles"></div>
            </div></div></div></div>
<script src="js/SucursalesActivas.js"></script>
<script src="js/AgregaNuevaSucursal.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/Altadenuevopersonal.php";
            include "Footer.php";?>
</body>

</html>