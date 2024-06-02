$(document).ready(function($) {
    $("#GeneraTicketAperturaCaja").hide();

    $("#OpenCaja").validate({
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
                    $("#submit_registro").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    });
                    setTimeout(function() {
                        $("#submit_registro").prop('disabled', false);
                        $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000);
                } else if (dataResult.statusCode == 200) {
                    $("#submit_registro").html("Enviado <i class='fas fa-check'></i>");
                    $("#OpenCaja")[0].reset();
                    $("#AltaFondo").removeClass("in");
                    $(".modal-backdrop").remove();
                    $("#AltaFondo").hide();

                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Operación realizada con éxito',
                        showConfirmButton: false,
                        timer: 2000
                    });

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
                } else if (dataResult.statusCode == 201) {
                    $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    });
                    setTimeout(function() {
                        $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000);
                }
            }
        });
        return false;
    }
});
