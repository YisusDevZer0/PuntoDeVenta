<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos de <?php echo $row['Licencia']?></title>
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
          <div class="text-center">
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
     
            <h6 class="mb-4" style="color:#0172b6;">Base de productos de <?php echo $row['Licencia']?></h6> <br>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agregar nuevo producto
</button> 
<button type="button" class="btn btn-success" id="openAlert">
  Actualizacion masiva
</button> 
<button type="button" class="btn btn-success" id="openAlertmaxmin">
  Actualizacion masiva de maximos y minimos
</button>
<br>
<script>
document.getElementById('openAlert').addEventListener('click', function() {
  Swal.fire({
    title: '¿Has descargado la plantilla?',
    text: "Primero debes descargar la plantilla antes de proceder.",
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Sí, ya tengo la plantilla',
    cancelButtonText: 'No, descargar ahora'
  }).then((result) => {
    if (result.isConfirmed) {
      // Redirige a la URL para proceder si el usuario ya tiene la plantilla
      window.location.href = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ActualizacionMasiva';
    } else if (result.isDismissed) {
      // Redirige a la URL para descargar la plantilla si el usuario no la tiene
      window.location.href = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Plantillaexcel';
    }
  });
});

document.getElementById('openAlertmaxmin').addEventListener('click', function() {
  Swal.fire({
    title: '¿Has descargado la plantilla de máximos y mínimos?',
    text: "Primero debes descargar la plantilla antes de proceder.",
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Sí, ya tengo la plantilla',
    cancelButtonText: 'No, descargar ahora'
  }).then((result) => {
    if (result.isConfirmed) {
      // Redirige a la URL para proceder si el usuario ya tiene la plantilla
      window.location.href = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ActualizacionMasivaMaxMin';
    } else if (result.isDismissed) {
      // Descarga la plantilla y redirige automáticamente después
      const downloadUrl = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/GenerarPlantillaMaxMin';
      const redirectUrl = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ActualizacionMasivaMaxMin';

      // Crea un formulario invisible para descargar la plantilla
      const form = document.createElement('form');
      form.style.display = 'none';
      form.action = downloadUrl;
      form.method = 'GET';
      document.body.appendChild(form);
      form.submit();

      // Redirige después de un tiempo (ajusta el tiempo según sea necesario)
      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 3000); // 3 segundos de espera
    }
  });
});
</script>


            <div id="DataDeProductos"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeProductos.js"></script>

<script src="js/AltaProductosNuevos.js"></script>
            <!-- Footer Start -->
            <script>
  	
    $(document).ready(function() {
    // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
    $(document).on("click", ".btn-EditarProd", function() {
    
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EditaProductosGenerales.php", { id: id }, function(data) {
          $("#FormCajas").html(data);
            $("#TitulosCajas").html("Editar datos de productos");
            $("#Di").addClass("modal-dialog modal-xl modal-notify modal-warning");
        });
        $('#ModalEdDele').modal('show');
        });

  // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
  $(document).on("click", ".btn-EliminarData", function() {
    
    var id = $(this).data("id");
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminaProductosGeneral.php", { id: id }, function(data) {
      $("#FormCajas").html(data);
        $("#TitulosCajas").html("Eliminar datos");
        $("#Di").addClass("modal-dialog  modal-notify modal-warning");
    });
    $('#ModalEdDele').modal('show');
    });
        $(document).on("click", ".btn-CrearCodBar", function() {
    
    var id = $(this).data("id");
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/GeneraCodigoDeBarrasProductos.php", { id: id }, function(data) {
      $("#FormCajas").html(data);
        $("#TitulosCajas").html("Generar codigos de barras");
        $("#Di").addClass("modal-dialog modal-lg modal-notify modal-warning");
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
            <?php 
            include "Modales/NuevoProductos.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>