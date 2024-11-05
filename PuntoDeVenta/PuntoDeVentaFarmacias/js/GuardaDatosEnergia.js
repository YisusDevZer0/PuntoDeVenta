$('document').ready(function($) {
    $.validator.addMethod("Sololetras", function(value, element) {
        return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $("#RegistroDiarioEnergiaWats").validate({
        rules: {
            registroenergia: { required: true }
        },
        messages: {
            registroenergia: { required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido" }
        },
        submitHandler: submitForm
    });

    function submitForm() {
        $("#RegistroDiarioEnergiaWats").on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/RegistroDeEnergiaDiario.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);

                    if (dataResult.statusCode == 250) {
                        mostrarMensajeSegundoClick();

                    } else if (dataResult.statusCode == 200) {
                        $("#submit_registro").html("Enviado <i class='fas fa-check'></i>");
                        $("#RegistroDiarioEnergiaWats")[0].reset();
                        $("#RegistroEnergiaVentanaModal").removeClass("in");
                        $(".modal-backdrop").remove();
                        $("#RegistroEnergiaVentanaModal").hide();
                        $('#Exito').modal('toggle');
                        setTimeout(function() {
                            $('#Exito').modal('hide');
                        }, 2000);
                        RegistroEnergias();

                    } else if (dataResult.statusCode == 201) {
                        $("#submit_Age").html("Algo no salió bien.. <i class='fas fa-exclamation-triangle'></i>");
                        $('#ErrorData').modal('toggle');
                        setTimeout(function() {
                            $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                        }, 3000);
                    }
                }
            });
        });
        return false;
    }

    function mostrarMensajeSegundoClick() {
        $("#submit_registro").html("Haz clic nuevamente para confirmar");
        setTimeout(function() {
            $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
        }, 3000); // El botón vuelve a su estado original después de 3 segundos
    }
});
