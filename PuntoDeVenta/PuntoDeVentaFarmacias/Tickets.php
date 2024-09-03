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
            
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/DesgloseTicketss.js"></script>
<?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
<script>
    $(document).ready(function() {
    $(".btn-desglose").click(function(){
        var id = $(this).data("id");
        $.post("https://saludapos.com/POS2/Modales/DesgloseTicket.php", "id=" + id, function(data){
            $("#FormCancelacion").html(data);
            $("#TituloCancelacion").html("Desglose del ticket");
            $("#Di3").removeClass("modal-dialog modal-lg modal-notify modal-info");
            $("#Di3").removeClass("modal-dialog modal-xl modal-notify modal-success");
            $("#Di3").addClass("modal-dialog modal-xl modal-notify modal-primary");

            var modal_lv = 0;
            $('.modal').on('shown.bs.modal', function (e) {
                $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
                $(e.currentTarget).css('zIndex', 1052 + modal_lv);
                modal_lv++;
            });

            $('.modal').on('hidden.bs.modal', function (e) {
                modal_lv--;
            });
        });
        $('#Cancelacionmodal').modal('show');
    });

    $(".btn-Reimpresion").click(function(){
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/ReimprimeTicketsVenta.php", "id=" + id, function(data){
            $("#FormCancelacion").html(data);
            $("#TituloCancelacion").html("Editar datos de categoría");
            $("#Di3").removeClass("modal-dialog modal-lg modal-notify modal-info");
            $("#Di3").removeClass("modal-dialog modal-xl modal-notify modal-primary");
            $("#Di3").addClass("modal-dialog modal-xl modal-notify modal-success");

            var modal_lv = 0;
            $('.modal').on('shown.bs.modal', function (e) {
                $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
                $(e.currentTarget).css('zIndex', 1052 + modal_lv);
                modal_lv++;
            });

            $('.modal').on('hidden.bs.modal', function (e) {
                modal_lv--;
            });
        });
        $('#Cancelacionmodal').modal('show');
    });
});

</script>

 <div class="modal fade" id="Cancelacionmodal" tabindex="-2" role="dialog" style="overflow-y: scroll;" aria-labelledby="CancelacionmodalLabel" aria-hidden="true">
  <div id="Di3" class="modal-dialog modal-lg modal-notify modal-info">
      <div class="modal-content">
      <div class="modal-header">
         <p class="heading lead" id="TituloCancelacion">Confirmacion de ticket</p>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
       
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCancelacion"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
            <!-- Footer Start -->
         
</body>

</html>