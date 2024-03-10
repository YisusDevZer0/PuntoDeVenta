<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/ConsultaFondoCaja.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Fondos de cajas disponibles para  <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
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
          
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Administracion de cajas de  <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
            <div class="text-center">
            <button type="button" class="btn-editcaja btn btn-primary" data-id="<?php echo $ValorFondoCaja["ID_Fon_Caja"];?>" >
  Aperturar nueva caja 
</button> <br>
<div id="Cajas"></div>
            </div></div></div></div>
            </div>
            <script src="js/Cajas.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/AbreCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
            <script>
  	
      $(".btn-edit").click(function(){
            id = $(this).data("id");
            $.post("hhttps://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/AbreCaja.php","id="+id,function(data){
                $("#form-edit").html(data);
                $("#Titulo").html("Apertura de caja");
                $("#Di").addClass("modal-dialog modal-lg modal-notify modal-success");
            });
            $('#editModal').modal('show');
      });
    </script>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="editModalLabel" aria-hidden="true">
    <div id="Di"class="modal-dialog  modal-notify modal-success">
        <div class="modal-content">
        <div class="modal-header">
           <p class="heading lead" id="Titulo"></p>
  
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
          <div id="form-edit"></div>
          
          </div>
  
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</body>

</html>