$('document').ready(function($) {
    $("#GeneraTicketAperturaCaja").hide();
    $.validator.addMethod("Sololetras", function(value, element) {
        return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
    $.validator.addMethod("Telefonico", function(value, element) {
        return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar numeros!");
    $.validator.addMethod("Correos", function(value, element) {
        return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo valido!");
    $.validator.addMethod("NEmpresa", function(value, element) {
        return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
    $.validator.addMethod("Problema", function(value, element) {
        return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $("#OpenCaja").validate({
        rules: {
            FkFondo: {
                required: true,
            },
            Cantidad: {
                required: true,
            },
            Empleado: {
                required: true,
            },
            Sucursal: {
                required: true,
            },
            Fecha: {
                required: true,
            },
            TotalCaja: {
                required: true,
            },
            Turno: {
                required: true,
            },
            vigencia: {
                required: true,
            },
        },
        messages: {
            FkFondo: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            Cantidad: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            Empleado: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            Sucursal: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            Fecha: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            Turno: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
            },
            TotalCaja: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Se necesita el valor de la caja para poder realizar la apertura ",
            },
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
                $("#submit_registro").prop('disabled', true)
            },
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if (dataResult.statusCode == 250) {
                    $("#submit_registro").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    $('#ErrorCaja').modal('toggle');
                    setTimeout(function() {
                        $("#submit_registro").prop('disabled', false)
                        $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000);
                } else if (dataResult.statusCode == 200) {
                    $("#submit_registro").html("Enviado <i class='fas fa-check'></i>");
                    $("#OpenCaja")[0].reset();
                    $("#AltaFondo").removeClass("in");
                    $(".modal-backdrop").remove();
                    $("#AltaFondo").hide();
                    $('#Exito').modal('toggle');
                    setTimeout(function() {
                        $('#Exito').modal('hide')
                    }, 2000);

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

                    CargaCajas();
                } else if (dataResult.statusCode == 201) {
                    $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    $('#ErrorData').modal('toggle');
                    setTimeout(function() {
                        $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000);
                }
            }
        });
        return false;
    }
});
