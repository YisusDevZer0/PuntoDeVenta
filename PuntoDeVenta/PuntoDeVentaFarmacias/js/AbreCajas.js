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
                        text: 'Operación realizada con éxito',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                        $("#OpenCaja")[0].reset();

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
