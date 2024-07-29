$(document).ready(function () {
    $.validator.addMethod("Sololetras", function (value, element) {
        return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras");

    $.validator.addMethod("Telefonico", function (value, element) {
        return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números!");

    $.validator.addMethod("Correos", function (value, element) {
        return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo válido!");

    $.validator.addMethod("Problema", function (value, element) {
        return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $.validator.addMethod("Curps", function (value, element) {
        return this.optional(element) || /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Verifique el CURP");

    $.validator.addMethod("RFCC", function (value, element) {
        return this.optional(element) || /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Verifique el RFC");

    $.validator.addMethod("NSSS", function (value, element) {
        return this.optional(element) || /^(\d{2})(\d{2})(\d{2})\d{5}$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Verifique el NSS");

    $("#ActualizaDatosDeProductos").validate({
        rules: {
            ActNomServ: {
                required: true,
            },
            ActVigenciaServ: {
                required: true,
            }
        },
        messages: {
            ActNomServ: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Límite de caracteres sobrepasado",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Un nombre no puede tener solo un carácter"
            },
            ActVigenciaServ: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
        },
        submitHandler: submitForm
    });

    function submitForm() {
        $.ajax({
            type: 'POST',
            url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/actualiza_producto",
            data: $('#ActualizaDatosDeProductos').serialize(),
            cache: false,
            dataType: 'json', // Especifica que esperamos una respuesta JSON
            beforeSend: function () {
                $("#success").fadeOut();
                $("#submit").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
            },
            success: function (dataResult) {
                console.log('Parsed response:', dataResult); // Verifica el contenido parseado

                if (dataResult.statusCode == 200) {
                    $("#submit").html("Enviado <i class='fas fa-check'></i>");
                    $("#ModalEdDele").removeClass("in");
                    $(".modal-backdrop").remove();
                    $("#ModalEdDele").hide();
                    $('#updateSuccessModal').modal('toggle');
                    setTimeout(function () {
                        $('#updateSuccessModal').modal('hide');
                        location.reload(); // Recargar la página completa después de 5 segundos
                    }, 5000); // Mostrar el modal durante 5 segundos antes de recargar la página
                } else if (dataResult.statusCode == 201) {
                    alert("Error occurred!");
                    $("#CS").html("Enviar <i class='fas fa-paper-plane'></i>");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error); // Verifica errores AJAX
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la solicitud',
                    text: 'Hubo un problema con la solicitud AJAX.',
                });
            }
        });
        return false;
    }
});
