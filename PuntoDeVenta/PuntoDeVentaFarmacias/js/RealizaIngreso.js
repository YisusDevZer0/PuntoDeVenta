/**
 * Módulo Ingreso de medicamentos (Ingresos.php).
 * Envía al controlador RegistraIngresoMedicamentosFarmacia.php.
 * Usa dataType: 'json' para recibir el objeto directamente, sin JSON.parse.
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
                dataType: 'json',
                success: function (response) {
                    var ok = response && (response.status === 'success' || response.success === true);
                    var msg = (response && response.message) ? response.message : '';
                    if (ok) {
                        msg = msg || 'Los productos se registraron correctamente.';
                        Swal.fire({
                            icon: 'success',
                            title: 'Ingreso registrado',
                            text: msg,
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        $btn.prop('disabled', false).html('Enviar Información');
                        msg = msg || 'No se pudieron guardar los datos.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Algo salió mal',
                            text: msg,
                        });
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('Enviar Información');
                    var msg = 'No se pudieron guardar los datos. Inténtalo de nuevo.';
                    var sesionExpirada = false;
                    if (xhr && xhr.responseText) {
                        var txt = (xhr.responseText || '').toLowerCase();
                        if (txt.indexOf('sesión') >= 0 || txt.indexOf('session') >= 0 || txt.indexOf('login') >= 0 || txt.indexOf('vencida') >= 0) {
                            sesionExpirada = true;
                            msg = 'La sesión ha expirado. Recarga la página e inicia sesión nuevamente.';
                        } else {
                            try {
                                var r = JSON.parse(xhr.responseText);
                                if (r && r.message) msg = r.message;
                            } catch (e) {}
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: sesionExpirada ? 'Sesión vencida' : 'Error en la petición',
                        text: msg,
                    });
                }
            });
            return false;
        }
    });
});
