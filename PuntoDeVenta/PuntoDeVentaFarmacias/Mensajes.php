<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Registro diario de control de energia de <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">Mensajes o recordatorios de  <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
            <div class="text-center">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalRecordatoriosMensajes">
  Crear nuevo mensaje o recordatorio
</button> <br>
<div id="Cajas"></div>
            </div></div></div></div>
            </div>
            <script src="js/Recordatorios_mensajes.js"></script>
         
            <!-- Footer Start -->
            <?php 
          
            include "Modales/NuevoRecordatorio.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
           <script>
    $(".btn-cambiaestadomensaje").click(function(){
        id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/ActualizaEstadoMensaje.php", "id=" + id, function(data){
            $("#form-edit").html(data);
            $("#Titulo").html("¿Marcar como leido?");
            $("#Di").addClass("modal-dialog  modal-warning modal-success");
        });
        $('#editModal').modal('show');
    });
</script>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="editModalLabel" aria-hidden="true">
    <div id="Di" class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="Titulo" style="color:white;">Apertura de caja</h5>
                
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong><?php echo $row['Nombre_Apellidos']; ?></strong> Verifique los campos antes de realizar alguna acción.
                  
                </div>
                <div id="form-edit">
                    <!-- Contenido del formulario -->
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>