<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Servicios de <?php echo $row['Licencia']?></title>
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
          
            <div class="container-fluid pt-8 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Servicios registrados para <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agregar nuevo fondo 
</button> <br>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/ControlDeServicios.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoServicio.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
           <script>
   $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Movimientos" dentro de .dropdown-menu
    $(document).on("click", ".dropdown-menu .btn-Movimientos", function() {
      console.log("Botón de cancelar clickeado para el ID:", id);
        var id = $(this).data("id");
        $.post("https://saludapos.com/AdminPOS/Modales/HistorialCaja.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Historial de caja");
            $("#CajasDi").removeClass("modal-dialog modal-xl modal-notify modal-info");
            $("#CajasDi").addClass("modal-dialog modal-xl modal-notify modal-primary");
        });
        $('#ModalDetallesCaja').modal('show');
    });

    // Delegación de eventos para el botón "btn-Ventas" dentro de .dropdown-menu
    $(document).on("click", ".dropdown-menu .btn-Ventas", function() {
        var id = $(this).data("id");
        $.post("https://saludapos.com/AdminPOS/Modales/HistorialVentas.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Historial de ventas");
            $("#CajasDi").removeClass("modal-dialog modal-xl modal-notify modal-info");
            $("#CajasDi").removeClass("modal-dialog modal-xl modal-notify modal-primary");
            $("#CajasDi").addClass("modal-dialog modal-xl modal-notify modal-success");
        });
        $('#ModalDetallesCaja').modal('show');
    });

    // Delegación de eventos para el botón "btn-Cortes" dentro de .dropdown-menu
    $(document).on("click", ".dropdown-menu .btn-Cortes", function() {
        var id = $(this).data("id");
        $.post("https://saludapos.com/AdminPOS/Modales/CortesDeCaja.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Corte de caja");
            $("#CajasDi").removeClass("modal-dialog modal-xl modal-notify modal-info");
            $("#CajasDi").removeClass("modal-dialog modal-xl modal-notify modal-primary");
            $("#CajasDi").addClass("modal-dialog modal-xl modal-notify modal-success");
        });
        $('#ModalDetallesCaja').modal('show');
    });
});

</script>

  <div class="modal fade" id="ModalDetallesCaja" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalDetallesCajaLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
      <div class="modal-content">
      <div class="modal-header">
         <p class="heading lead" id="TitulosCajas"></p>

         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
        <div id="Mensaje "class="alert alert-info alert-styled-left text-blue-800 content-group">
						                <span id="Aviso" class="text-semibold"><?php echo $row['Nombre_Apellidos']?>
                            Verifique los campos antes de realizar alguna accion</span>
						                <button type="button" class="close" data-dismiss="alert">×</button>
                            </div>
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCajas"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</body>

</html>