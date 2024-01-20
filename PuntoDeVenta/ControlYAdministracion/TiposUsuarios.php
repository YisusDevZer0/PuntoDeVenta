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
    console.log("Inicializando DataTables...");
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "Controladores/datatable_server.php",
        "columns": [
            { "data": 0 },  // Cambiado de "ID_User" a 0
            { "data": 1 },  // Cambiado de "TipoUsuario" a 1
            { "data": 2 },  // Cambiado de "Licencia" a 2
            { "data": 3 },  // Cambiado de "Creadoel" a 3
            { "data": 4 },  // Cambiado de "Creado" a 4
            // { 
            //     "data": null,
            //     "render": function (data, type, row) {
            //         return '<button class="btn btn-primary btn-sm edit-btn">Editar</button>';
            //     }
            // }
        ]
    });
    console.log("DataTables inicializado con éxito!");
    // Maneja clics en los botones "Editar"
    $('#userTable tbody').on('click', '.edit-btn', function () {
        var data = table.row($(this).parents('tr')).data();
        abrirVentanaEdicion(data);
    });

    function abrirVentanaEdicion(data) {
        // Aquí deberías usar SweetAlert2 para mostrar la ventana de edición
        Swal.fire({
            title: 'Editar Usuario',
            html: '<input id="tipoUsuario" class="swal2-input" value="' + data.TipoUsuario + '">' +
                  '<input id="licencia" class="swal2-input" value="' + data.Licencia + '">',
            showCancelButton: true,
            confirmButtonText: 'Guardar Cambios',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                var tipoUsuario = document.getElementById('tipoUsuario').value;
                var licencia = document.getElementById('licencia').value;

                // Lógica para enviar una solicitud AJAX y actualizar los datos en el servidor
                $.ajax({
                    type: "POST",
                    url: "actualizar_usuario.php", // Reemplaza con la URL correcta
                    data: {
                        id: data.ID_User,
                        tipoUsuario: tipoUsuario,
                        licencia: licencia
                    },
                    success: function (response) {
                        // Maneja la respuesta del servidor (puede ser un mensaje de éxito, error, etc.)
                        Swal.fire('Éxito', 'Usuario actualizado correctamente', 'success');
                        // Actualiza la tabla para reflejar los cambios
                        table.ajax.reload();
                    },
                    error: function () {
                        // Maneja los errores de la solicitud AJAX
                        Swal.fire('Error', 'Hubo un error al actualizar el usuario', 'error');
                    }
                });

                // Retorna una promesa para que SweetAlert2 espere a la resolución de la solicitud AJAX
                return new Promise(function (resolve) {
                    resolve();
                });
            }
        });
    }
});
</script>


            </div>   </div>
            <!-- Footer Start -->
            <?php include "Footer.php";?>
</body>

</html>