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
        "ajax": "datatable_server.php",
        "columns": [
            { "data": "ID_User" },
            { "data": "TipoUsuario" },
            { "data": "Licencia" },
            { "data": "Creadoel" },
            { "data": "Creado" },
            { 
                "data": null,
                "render": function (data, type, row) {
                    return '<button class="btn btn-primary btn-sm edit-btn">Editar</button>';
                }
            }
        ]
    });

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
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved. 
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>