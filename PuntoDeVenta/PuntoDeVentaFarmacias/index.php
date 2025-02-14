<?php
include_once "Controladores/ControladorUsuario.php"
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Pantalla de inicio punto de venta <?php echo $row['Licencia']?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
   <?php
   include "header.php";?>

<body data-theme="light">
  


        <!-- Sidebar Start -->
       <?php include_once "Menu.php" ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa-solid fa-capsules fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Productos</p><button onclick="toggleTheme()" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">Alternar Modo</button>
                            
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ConsultaProductosModal">
  Consultar Productos
</button>

                                
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                           
                            <i class="fa-solid fa-right-left fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Traspasos</p>
                                <button type="button" class="btn btn-primary" id="openModalBtn">Consultar</button>
                            </div>
                        </div>
                    </div>
                   
                    <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Mensajes o recordatorios de  <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
            <div class="text-center">
            
<div id="Cajas"></div>
            </div></div></div></div>
            <!-- Sale & Revenue End -->
            <script>
        function toggleTheme() {
            const currentTheme = document.body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', savedTheme);
        });
        </script>

        <script>
   $(document).ready(function() {
    $(document).on("click", ".btn-cambiaestadomensaje", function() {
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/MarcaMensajeComoLeido.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Desactivar caja actual");
        });
        $('#ModalEdDele').modal('show');
    });
});
    </script>
            <script>
   $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Movimientos" dentro de .dropdown-menu
    $(document).on("click", ".btn-cambiaestadomensaje", function() {
      console.log("Botón de cancelar clickeado para el ID:", id);
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/MarcaMensajeComoLeido.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Desactivar caja actual");
            
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
  </div><!-- /.modal --></div></div></div></div></div></div>
            <?php 
        include "Modales/ConsultaProductosRapidos.php";
        ?>
            <script src="js/Recordatorios_mensajes.js"></script>
       
            

        <?php 
       
        include "Footer.php";?>
</body>

</html>