$(document).ready(function($) {
    // Esconde el formulario de generación de tickets (si aplica)
    
    $("#NuevoMensajeSucursales").validate({
        submitHandler: submitForm
    });

    function submitForm() {
        $.ajax({
            type: 'POST',
            url: "Controladores/AperturaCaja.php", // Cambia esta URL si es necesario
            data: $('#NuevoMensajeSucursales').serialize(),
            cache: false,
            beforeSend: function() {
                $(".btn-primary").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                $(".btn-primary").prop('disabled', true);
            },
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if (dataResult.statusCode == 250) {
                    $(".btn-primary").html("Guardar");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    }).then(() => {
                        $(".btn-primary").prop('disabled', false);
                    });
                } else if (dataResult.statusCode == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Mensaje enviado con éxito',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#ModalRecordatoriosMensajes").removeClass("in");
                        $(".modal-backdrop").remove();
                        $("#ModalRecordatoriosMensajes").hide();

                       

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    });
                } else if (dataResult.statusCode == 201) {
                    $(".btn-primary").html("Guardar");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Algo no salió bien',
                    }).then(() => {
                        $(".btn-primary").prop('disabled', false);
                    });
                }
            }
        });
        return false;
    }
});
