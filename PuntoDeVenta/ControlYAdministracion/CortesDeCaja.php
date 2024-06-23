<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Cajas activas de  <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4">Fondos de caja de <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agregar nuevo fondo 
</button> <br>
            <div id="FCajas"></div>
            </div></div></div></div>
         
<script src="js/CajasCerradas.js"></script>
<script>
    $(document).ready(function() {
    $.getJSON('Controladores/SelectSucursales.php', function(data) {
        console.log(data); // Verifica que los datos se estén recibiendo correctamente en la consola del navegador
        $.each(data, function(key, value) {
            $('#opciones').append('<option value="' + value.ID_Sucursal + '">' + value.Nombre_Sucursal + '</option>');
        });
    })
    .fail(function(jqxhr, textStatus, error) {
        console.error("Error al obtener los datos de la base de datos:", textStatus, error);
    });
});

</script>
            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>