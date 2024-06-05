<?php
include_once "Controladores/ControladorUsuario.php"
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Pantalla de inicio punto de venta <?php echo $row['Licencia']?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
   <?php
   include "header.php";?>

<body>
  


        <!-- Sidebar Start -->
       <?php include_once "Menu.php" ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa-solid fa-capsules fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Productos</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ConsultaProductos">Consultar</button>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                           
                            <i class="fa-solid fa-right-left fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Traspasos</p>
                                <button type="button" class="btn btn-primary" id="openModalBtn">Consultar</button>
                            </div>
                        </div>
                    </div>
                   
                    <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Mensajes o recordatorios de  <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
            <div class="text-center">
            
<div id="Cajas"></div> <?php 
        include "Modales/ConsultaProductosRapidos.php";
        ?>
            </div></div></div></div>
            <!-- Sale & Revenue End -->
           
            <script src="js/Recordatorios_mensajes.js"></script>
       
            

        <?php 
       
        include "Footer.php";?>
</body>

</html>