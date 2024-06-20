$('document').ready(function($) {
    $("#GuardaMedicamentoAutorizados").validate({
        rules: {
            NombreTipoProd: {
                required: true,
                minlength: 2,
                maxlength: 40,
            },
            VigenciaProdT: {
                required: true,
            },
        },
        messages: {
            NombreTipoProd: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa el nombre ",
                maxlength: "No puede tener más de 40 caracteres",
                minlength: "Un nombre no puede tener solo 1 caracter"
            },
            VigenciaProdT: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
        },
        submitHandler: function(form) {
            $.ajax({
                type: 'POST',
                url: "ruta/al/archivo/php.php", // Reemplaza con la ruta correcta
                data: $(form).serialize(),
                cache: false,
                beforeSend: function() {
                    $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function(response) {
                    var dataResult = JSON.parse(response);
                    if (dataResult.statusCode == 250) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'El registro ya existe'
                        });
                    } else if (dataResult.statusCode == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registro exitoso!',
                            text: 'El medicamento ha sido registrado exitosamente'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else if (dataResult.statusCode == 201) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error en la inserción: ' + dataResult.error
                        });
                    } else if (dataResult.statusCode == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error: ' + dataResult.error
                        });
                    }
                    $("#submit_registro").html("Algo no salió bien.. <i class='fas fa-exclamation-triangle'></i>");
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error en la solicitud AJAX: ' + error
                    });
                    $("#submit_registro").html("Algo no salió bien.. <i class='fas fa-exclamation-triangle'></i>");
                }
            });
            return false;
        }
    });
});
