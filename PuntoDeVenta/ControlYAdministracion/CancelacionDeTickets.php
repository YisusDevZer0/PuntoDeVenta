<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ventas del dia de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
     <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
            <h6 class="mb-4" style="color:#0172b6;">Tickets de venta de  <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#FiltroEspecifico" class="btn btn-default">
 Cambiar de sucursal <i class="fas fa-clinic-medical"></i> 
</button><br>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/DesgloseTicketss.js"></script>
<script src="js/RealizaCambioDeSucursalPorFiltro.js"></script>
<?php 
       include ("Modales/FiltraEspecificamenteInventarios.php");
            include "Footer.php";?>
<script>
  $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Reimpresion" dentro de .dropdown-menu
    $(document).on("click", ".btn-eliminar", function() {
        var id = $(this).data("id");  // Asignar el valor correcto aquí
        $('#CajasDi').removeClass('modal-dialog   modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');  // Asegúrate de que solo tenga el tamaño grande
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/CancelarTickets.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Cancelar ticket");
        });
        
        $('#ModalEdDele').modal('show');
    });


    


   

});



</script>





  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog modal-xl modal-notify modal-success" >
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