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
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Base de productos de <?php echo $row['Licencia']?></h6> <br>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agregar nuevo producto
</button> <br>
            <div id="DataDeProductos"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeProductos.js"></script>

<script src="js/AltaProductosNuevos.js"></script>
<script>
  // Función para cargar el ID del producto en el modal de Edición
function cargarModalEditar(id) {
    $('#modalEditarId').text(id);
    // Aquí puedes añadir lógica adicional para cargar otros datos del producto en el modal de edición si es necesario
}

// Función para cargar el ID del producto en el modal de Historial
function cargarModalHistorial(id) {
    $('#modalHistorialId').text(id);
    // Aquí puedes añadir lógica adicional para cargar el historial del producto si es necesario
}

// Document ready function para inicializar eventos cuando la página se carga completamente
$(document).ready(function () {
    // Evento click para los botones de edición
    $('.btn-warning').click(function () {
        // Obtener el ID del producto desde el elemento más cercano que tenga la clase 'data-id'
        var idProd = $(this).closest('tr').find('.data-id').text();
        // Llamar a la función para cargar el modal de edición con el ID del producto
        cargarModalEditar(idProd);
    });

    // Evento click para los botones de historial
    $('.btn-secondary').click(function () {
        // Obtener el ID del producto desde el elemento más cercano que tenga la clase 'data-id'
        var idProd = $(this).closest('tr').find('.data-id').text();
        // Llamar a la función para cargar el modal de historial con el ID del producto
        cargarModalHistorial(idProd);
    });
});

</script>
            <!-- Footer Start -->
             <!-- Modal de Edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenido del formulario de edición -->
                <p>Aquí va el contenido del formulario de edición para el producto seleccionado.</p>
                <p>ID del producto: <span id="modalEditarId"></span></p>
                <!-- Otros campos de formulario aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Historial -->
<div class="modal fade" id="modalHistorial" tabindex="-1" role="dialog" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialLabel">Historial del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenido del historial del producto -->
                <p>Aquí va el contenido del historial para el producto seleccionado.</p>
                <p>ID del producto: <span id="modalHistorialId"></span></p>
                <!-- Otros detalles del historial aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

            <?php 
            include "Modales/NuevoProductos.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>