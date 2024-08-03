<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Resultado actualizacion masiva de productos de <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Resultados de actualizacion <?php echo $row['Licencia']?></h6>
           
   
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#FiltroPorFechas">
  Agregar nuevo producto
</button> 
   <br><br>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/ResultadosDeLaActualizacionMasiva.js"></script>
<script>
  $(document).ready(function() {
    $.ajax({
      url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CargaFechaDeCargaMasiva.php',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        var $select = $('#nombreservicio');
        $select.empty(); // Limpia las opciones actuales

        $.each(data, function(index, value) {
          $select.append($('<option></option>').attr('value', value).text(value));
        });
      },
      error: function() {
        alert('Error al cargar las fechas.');
      }
    });
  });
</script>


            <!-- Footer Start -->
            <?php 
            include "Modales/BusquedaDeSubidaMasiva.php";
            include "Footer.php";?>
</body>

</html>