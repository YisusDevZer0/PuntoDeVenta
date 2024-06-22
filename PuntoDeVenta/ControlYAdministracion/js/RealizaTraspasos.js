$('document').ready(function($) {
    // Métodos de validación personalizados
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

    // Validación del formulario
    $("#Generamelostraspasos").validate({
        rules: {
            codbarras: {
                required: true,
            },
        },
        messages: {
            codbarras: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
            },
        },
        submitHandler: submitForm
    });

    // Función para enviar el formulario
    function submitForm() {
        $.ajax({
            type: 'POST',
            url: "Controladores/TraspasoAlMomento.php",
            data: $('#Generamelostraspasos').serialize(),
            cache: false,
            beforeSend: function() {
                $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
            },
            success: function(response) {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ListaDeTraspasos";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al realizar el traspaso',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
        return false;
    }
});
