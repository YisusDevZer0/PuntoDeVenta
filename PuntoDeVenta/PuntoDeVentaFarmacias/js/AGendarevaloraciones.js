$('document').ready(function($) {

    $("#AgendaExternoRevaloraciones").validate({
        rules: {
            Folio: {
                requiered: true,
            },
            Sucursal: {
                required: true,

            },

            CitaExt: {
                required: true,
            },
            Medico: {
                required: true,
            },
            Fecha: {
                required: true,
            },
            Hora: {
                required: true,
            },
            Costo: {
                required: true,
            },
            TipoConsulta: {
                required: true
            },

        },
        messages: {
            Folio: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },

            CitaExt: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            Sucursal: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            Medico: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            Fecha: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            Hora: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            Costo: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
            TipoConsulta: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido ",
            },
        },
        submitHandler: submitForm
    });
    // hide messages 


    function submitForm() {


        $.ajax({
            type: 'POST',
            url: "https://saludapos.com/POS2/Consultas/GuardaCitasDeRevaloracion.php",
            data: $('#AgendaExternoRevaloraciones').serialize(),
            cache: false,
            beforeSend: function() {


                $("#submit_Age").html("Un momento... <i class='fas fa-check'></i>");


            },
            success: function(dataResult) {

                var dataResult = JSON.parse(dataResult);

                if (dataResult.statusCode == 250) {
                    var modal_lv = 0;
                    $('.modal').on('shown.bs.modal', function(e) {
                        $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
                        $(e.currentTarget).css('zIndex', 1052 + modal_lv);
                        modal_lv++
                    });

                    $('.modal').on('hidden.bs.modal', function(e) {
                        modal_lv--
                    });
                    $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    $('#ErrorDupli').modal('toggle');
                    setTimeout(function() {}, 2000); // abrir
                    setTimeout(function() {
                        $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000); // abrir


                } else if (dataResult.statusCode == 200) {


                    $("#submit_Age").html("Completo <i class='fas fa-check'></i>");
                    $("#CitaExt").removeClass("in");
                    $(".modal-backdrop").remove();
                    $("#CitaExt").hide();
                    $('#Exito').modal('toggle');
                    $("#AgendaExternoRevaloraciones")[0].reset();

                    setTimeout(function() {
                        $('#Exito').modal('hide')
                    }, 2000); // abrir

                    $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    CargaRevaloraciones();
                } else if (dataResult.statusCode == 201) {
                    var modal_lv = 0;
                    $('.modal').on('shown.bs.modal', function(e) {
                        $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
                        $(e.currentTarget).css('zIndex', 1052 + modal_lv);
                        modal_lv++
                    });

                    $('.modal').on('hidden.bs.modal', function(e) {
                        modal_lv--
                    });
                    $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                    $('#ErrorData').modal('toggle');
                    setTimeout(function() {}, 2000); // abrir
                    setTimeout(function() {
                        $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                    }, 3000); // abrir

                }







            }
        });
        return false;
    }
});