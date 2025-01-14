<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Encargos pendientes de <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Tickets de encargos de  <?php echo $row['Licencia']?></h6>
            
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/DesgloseTicketssEncargos.js"></script>
<?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
<script>
  $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Reimpresion" dentro de .dropdown-menu
    $(document).on("click", ".btn-Reimpresion", function() {
        var id = $(this).data("id");  // Asignar el valor correcto aquí
        console.log("Botón de cancelar clickeado para el ID:", id); // Mover console.log después de la asignación de id
        $('#CajasDi').removeClass('modal-dialog  modal-xl modal-notify modal-success').addClass('modal-dialog  modal-notify modal-success');  // Asegúrate de que solo tenga el tamaño grande
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/ReimprimeTicketsVenta.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Generando archivo para reimpresion");
        });
        
        $('#ModalEdDele').modal('show');
    });


    // Delegación de eventos para el botón "btn-Reimpresion" dentro de .dropdown-menu
    $(document).on("click", ".btn-desglose", function() {
        var id = $(this).data("id");  // Asignar el valor correcto aquí
        console.log("Botón de cancelar clickeado para el ID:", id); // Mover console.log después de la asignación de id
        
    $('#CajasDi').removeClass('modal-dialog  modal-notify modal-success').addClass('modal-dialog  modal-xl modal-notify modal-success');  // Asegúrate de que solo tenga el tamaño grande
   
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/DesgloseTicketsVenta.php", { id: id }, function(data) {
          $("#TitulosCajas").html("Desglose de ticket");  
          $("#FormCajas").html(data);
            $("#TitulosCajas").html("Desglose de ticket");
        });
        
        $('#ModalEdDele').modal('show');
    });


   

});



</script>





  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
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
  </div><!-- /.modal --></div>
         
</body>

</html>