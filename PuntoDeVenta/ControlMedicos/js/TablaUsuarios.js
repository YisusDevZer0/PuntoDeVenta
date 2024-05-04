
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

    $('#userTable').on('click', '.edit-btn', function () {
        var userId = $(this).data('id');

        // Realiza una solicitud AJAX para obtener los datos del usuario
        $.ajax({
            url: 'Controladores/ObtieneDatosTipoUsuario.php',
            type: 'GET',
            data: { user_id: userId },
            dataType: 'json',
            success: function (data) {
                // Muestra los datos del usuario en un SweetAlert con un formulario
                Swal.fire({
                    title: 'Editar Usuario',
                    html:
                        '<form id="editForm">' +
                        '<div class="mb-3">' +
                        '<label for="tipoUsuario" class="form-label">Tipo de Usuario:</label>' +
                        '<input type="text" class="form-control" id="tipoUsuario" name="tipoUsuario" value="' + data.TipoUsuario + '" required>' +
                        '</div>' +
                        '<div class="mb-3">' +
                        '<label for="licencia" class="form-label">Licencia:</label>' +
                        '<input type="text" class="form-control" id="licencia" name="licencia" value="' + data.Licencia + '" required>' +
                        '</div>' +
                        '</form>',
                    showCancelButton: true,
                    confirmButtonText: 'Guardar cambios',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        // Obtén los valores del formulario al confirmar
                        return {
                            tipoUsuario: document.getElementById('tipoUsuario').value,
                            licencia: document.getElementById('licencia').value
                        };
                    }
                }).then((result) => {
                    // `result.value` contendrá los datos del formulario si se hizo clic en "Guardar cambios"
                    if (result.value) {
                        // Realiza una solicitud AJAX o realiza cualquier otra acción necesaria para guardar los cambios
                        console.log(result.value);
                        // Aquí puedes realizar una solicitud AJAX para guardar los cambios en la base de datos
                        // ...
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al obtener los datos del usuario.'
                });
            }
        });
    });
});
