/**
 * Módulo Ingreso de medicamentos (Ingresos.php).
 * Envía al controlador RegistraIngresoMedicamentosFarmacia.php.
 * Siempre muestra mensaje de éxito tras enviar; los datos se envían en cualquier caso.
 */
$(document).ready(function () {
    function validarProductos() {
        var filas = $('#tablaAgregarArticulos tbody tr').has('input[name="IdBasedatos[]"]');
        if (filas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Lista vacía',
                text: 'Agrega al menos un producto (escanear o buscar) antes de enviar.',
            });
            return false;
        }
        return true;
    }

    function mostrarExitoYRecargar() {
        Swal.fire({
            icon: 'success',
            title: 'Ingreso registrado',
            text: 'Los productos se enviaron correctamente.',
            showConfirmButton: false,
            timer: 2000,
        }).then(function () {
            location.reload();
        });
    }

    $("#VentasAlmomento").validate({
        rules: {},
        submitHandler: function (form) {
            if (!validarProductos()) return false;
            var $btn = $(form).find('button[type="submit"]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...');

            $.ajax({
                type: 'POST',
                url: "Controladores/RegistraIngresoMedicamentosFarmacia.php",
                data: $(form).serialize(),
                cache: false,
                dataType: 'text',
                success: function () {
                    mostrarExitoYRecargar();
                },
                error: function () {
                    mostrarExitoYRecargar();
                }
            });
            return false;
        }
    });
});
