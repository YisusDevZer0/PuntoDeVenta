$(document).ready(function($) {
    $("#GeneraTicketAperturaCaja").hide();

    // Configura las reglas de validación para el formulario
    $("#OpenCaja").validate({
        rules: {
            Turno: {
                required: true
            }
        },
        messages: {
            Turno: {
                required: "Por favor, selecciona un turno"
            }
        },
        // Sobreescribimos showErrors para controlar cómo se muestran los errores
        showErrors: function(errorMap, errorList) {
            // Si hay errores
            if (errorList.length) {
                // Recorremos los errores y verificamos si es el campo "Turno"
                $.each(errorList, function(index, error) {
                    if (error.element.name === "Turno") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Campo obligatorio',
                            text: 'Por favor, selecciona un turno antes de continuar.',
                            toast: true,  // Esto convierte la alerta en un estilo "toast"
                            position: 'top-right',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            }
            // Impedimos que los errores se muestren en el DOM
            this.defaultShowErrors();
        },
        submitHandler: submitForm
    });

    function submitForm() {
        $.ajax({
            type: 'POST',
            url: "Controladores/AperturaCaja.php",
            data: $('#OpenCaja').serialize(),
            cache: false,
            beforeSend: function() {
                $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                $("#submit_registro").prop('disabled', true);
            },
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if (dataResult.statusCode == 250) {
                    $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    }).then(() => {
                        $("#submit_registro").prop('disabled', false);
                    });
                } else if (dataResult.statusCode == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Apertura realizada con éxito',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#editModal").removeClass("in");
                        $(".modal-backdrop").remove();
                        $("#editModal").hide();

                        // Enviar los datos del formulario oculto
                        $.ajax({
                            type: 'POST',
                            url: 'http://localhost:8080/ticket/TicketAperturaCaja.php',
                            data: $('#GeneraTicketAperturaCaja').serialize(),
                            success: function(response) {
                                console.log("Response from ticket generation:", response);
                            },
                            error: function(error) {
                                console.error("Error generating ticket:", error);
                            }
                        });

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    });
                } else if (dataResult.statusCode == 201) {
                    $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    }).then(() => {
                        $("#submit_registro").prop('disabled', false);
                    });
                }
            }
        });
        return false;
    }
});
