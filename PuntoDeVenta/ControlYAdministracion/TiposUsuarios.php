<?php
include_once "Controladores/ControladorUsuario.php"
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Tipos de usuarios <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <?php
   include "header.php";?>
<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
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
            <h6 class="mb-4">Tipos de Usuarios</h6>
            <div class="table-responsive">
                <table class="table" id="userTable">
                    <thead>
                        <tr>
                            <th>ID User</th>
                            <th>Tipo de Usuario</th>
                            <th>Licencia</th>
                            <th>Creado el</th>
                            <th>Creado</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    // Inicializa DataTables
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" // Configura el idioma a español
                },
        "ajax": "Controladores/datatable_server.php",
        "columns": [
        { "data": 0 }, // Cambia a 0 en lugar de 'data'
        { "data": 1 }, // Cambia a 1 en lugar de 'data'
        { "data": 2 }, // Cambia a 2 en lugar de 'data'
        { "data": 3 }, // Cambia a 3 en lugar de 'data'
        { "data": 4 }, // Cambia a 4 en lugar de 'data'
        {
                        "data": null,
                        "render": function (data, type, row) {
                            // Usa un atributo data para almacenar el ID
                            return '<button class="btn btn-primary btn-sm edit-btn" data-id="' + row[0] + '">Editar</button>';
                        }
                    }
    ]
    });

   // Agrega un evento clic para el botón "Editar"
   $('#userTable').on('click', '.edit-btn', function () {
                var userId = $(this).data('id');
                
                // Realiza una consulta SQL para obtener los datos del usuario con el ID correspondiente
                // Aquí debes implementar tu lógica para obtener los datos del usuario desde tu base de datos
                // y luego mostrarlos en un modal o SweetAlert.

                // Ejemplo de SweetAlert:
                Swal.fire({
                    title: 'Editar Usuario',
                    html: 'Aquí puedes mostrar los datos del usuario con ID ' + userId,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar cambios',
                    cancelButtonText: 'Cancelar'
                    // Puedes personalizar más opciones de SweetAlert según tus necesidades
                });
            });

  
});
</script>


            </div>   </div>
            <!-- Footer Start -->
            <?php include "Footer.php";?>
</body>

</html>