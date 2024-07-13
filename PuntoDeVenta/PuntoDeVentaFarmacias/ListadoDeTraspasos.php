<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Clientes de  <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Traspasos de <?php echo $row['Licencia']?> sucursal <?php echo $row['Nombre_Sucursal']?> </h6>
            
            <div id="DataDeClientes"></div>
            </div></div></div></div>
            
          
<script src="js/ControlDeListaDeTraspasos.js"></script>
<script>
  	
      $(document).ready(function() {
      // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
      $(document).on("click", ".btn-EditarProd", function() {
      
          var id = $(this).data("id");
          $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/AceptaTraspasosPez.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
              $("#TitulosCajas").html("Verificacion del traspaso");
              $("#Di").addClass("modal-dialog modal-xl modal-notify modal-info");
          });
          $('#ModalEdDele').modal('show');
          });
       });
  
  </script>
  
  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
    <div id="Di"class="modal-dialog  modal-notify modal-success" >
      <div class="text-center">
        <div class="modal-content">
        <div class="modal-header" style=" background-color: #ef7980 !important;" >
           <p class="heading lead" id="TitulosCajas"  style="color:white;" ></p>
  
           
         </div>
          
              <div class="modal-body">
            <div class="text-center">
          <div id="FormCajas"></div>
          
          </div>
  
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal --></div></div>
            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>


