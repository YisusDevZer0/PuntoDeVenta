<?php
include_once "Controladores/ControladorUsuario.php";

// Función para determinar si los botones deben estar deshabilitados
function isActionButtonDisabled($tipoUsuario) {
    return $tipoUsuario == 'MKT' ? 'disabled style="opacity: 0.5; pointer-events: none;"' : '';
}

// Obtener el atributo de deshabilitado para los botones de acción
$actionDisabledAttr = isActionButtonDisabled($row['TipoUsuario']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Manejo de stocks de sucursales de <?php echo $row['Licencia']?></title>
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
            <h6 class="mb-4" style="color:#0172b6;">listado de productos en stocks de sucursales de <?php echo $row['Licencia']?></h6>
            
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            
          
<script src="js/ControlDeStocks.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>



<script>
  $(document).ready(function() {
    // Función para verificar si el usuario es MKT
    function isUserMKT() {
        return '<?php echo $row['TipoUsuario'] ?>' === 'MKT';
    }

    // Función base para cargar modales
    function cargarModal(url, titulo, id) {
        if (isUserMKT()) {
            return false;
        }
        $('#CajasDi').removeClass('modal-dialog modal-xl modal-notify modal-success')
                    .addClass('modal-dialog modal-xl modal-notify modal-success');
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id },
            success: function(data) {
                $("#FormCajas").html(data);
                $("#TitulosCajas").html(titulo);
                $('#ModalEdDele').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar el modal:', error);
                alert('Error al cargar el contenido. Por favor, intente nuevamente.');
            }
        });
    }

    // Delegación de eventos para el botón "btn-minimomaximo"
    $(document).on("click", ".btn-minimomaximo", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        cargarModal(
            "Modales/EditaMinMaxStocks.php",
            "Editar Mínimo y Máximo de Stock",
            id
        );
    });

    // Delegación de eventos para el botón "btn-editproducto"
    $(document).on("click", ".btn-editproducto", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        cargarModal(
            "Modales/EditaProductoStocks.php",
            "Editar Producto",
            id
        );
    });

    // Delegación de eventos para el botón "btn-AjustInvetario"
    $(document).on("click", ".btn-AjustInvetario", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        cargarModal(
            "Modales/AjusteInventarioManual.php",
            "Ajuste de Inventario",
            id
        );
    });

    // Delegación de eventos para el botón "btn-eliminarprod"
    $(document).on("click", ".btn-eliminarprod", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        cargarModal(
            "Modales/EliminarProducto.php",
            "Eliminar Producto",
            id
        );
    });

    // Delegación de eventos para el botón "btn-GeneraOrdenCompra"
    $(document).on("click", ".btn-GeneraOrdenCompra", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        cargarModal(
            "Modales/GeneraOrdenCompra.php",
            "Generar Orden de Compra",
            id
        );
    });
});

// Delegación de eventos para el botón "btn-Reimpresion"
$(document).on("click", ".btn-Reimpresion", function(e) {
    e.preventDefault();
    if (isUserMKT()) {
        return false;
    }
    var id = $(this).data("id");
    cargarModal(
        "Modales/ReimpresionTicket.php",
        "Reimpresión de Ticket",
        id
    );
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