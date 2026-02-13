/**
 * Módulo Ingresos con Lotes y Caducidad.
 * Valida Lote y Fecha de caducidad en cada fila y envía a RegistraIngresoMedicamentosFarmacia.
 */
$(document).ready(function () {
    function validarLotesYCaducidades() {
        var filas = $('#tablaAgregarArticulos tbody tr[data-id]');
        if (filas.length === 0) {
            filas = $('#tablaAgregarArticulos tbody tr').has('input[name="Lote[]"]');
        }
        if (filas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Lista vacía',
                text: 'Agrega al menos un producto (escanear o buscar) antes de enviar.',
            });
            return false;
        }
        var incompletos = [];
        filas.each(function (i) {
            var $fila = $(this);
            var lote = ($fila.find('input[name="Lote[]"]').val() || '').trim();
            var caducidad = ($fila.find('input[name="FechaCaducidad[]"]').val() || '').trim();
            var producto = $fila.find('.descripcion-producto-input').val() || $fila.find('textarea[name="NombreDelProducto[]"]').val() || ('Producto ' + (i + 1));
            if (!lote || !caducidad) {
                incompletos.push(producto.substring(0, 40) + (producto.length > 40 ? '...' : ''));
            }
        });
        if (incompletos.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Lote y fecha de caducidad obligatorios',
                html: 'Completa <b>Lote</b> y <b>Fecha de caducidad</b> en cada fila.<br><br>' +
                    (incompletos.length <= 3 ? incompletos.join('<br>') : incompletos.slice(0, 3).join('<br>') + '<br>... y ' + (incompletos.length - 3) + ' más.'),
            });
            return false;
        }
        return true;
    }

    $("#VentasAlmomento").validate({
        submitHandler: function (form) {
            if (!validarLotesYCaducidades()) return false;
            var $btn = $(form).find('button[type="submit"]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
            $.ajax({
                type: 'POST',
                url: "Controladores/RegistraIngresoMedicamentosFarmacia.php",
                data: $(form).serialize(),
                cache: false,
                dataType: 'json',
                success: function (response) {
                    $('#loading-overlay').hide();
                    var ok = response && (response.status === 'success' || response.success === true);
                    var msg = (response && response.message) ? response.message : '';
                    if (ok) {
                        msg = msg || 'Los productos se registraron correctamente.';
                        setTimeout(function () {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('¡Guardado exitoso!', msg, 'success').then(function () {
                                    location.reload();
                                });
                            } else {
                                alert('¡Guardado exitoso!\n\n' + msg);
                                location.reload();
                            }
                        }, 50);
                    } else {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Guardar ingresos');
                        msg = msg || 'No se pudieron guardar los datos.';
                        setTimeout(function () {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error al guardar', msg, 'error');
                            } else {
                                alert('Error al guardar\n\n' + msg);
                            }
                        }, 50);
                    }
                },
                error: function (xhr) {
                    $('#loading-overlay').hide();
                    $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Guardar ingresos');
                    var msg = 'No se pudieron guardar los datos. Inténtalo de nuevo.';
                    if (xhr && xhr.responseText) {
                        try {
                            var r = JSON.parse(xhr.responseText);
                            if (r && r.message) msg = r.message;
                        } catch (e) {}
                    }
                    setTimeout(function () {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error al guardar', msg, 'error');
                        } else {
                            alert('Error al guardar\n\n' + msg);
                        }
                    }, 50);
                }
            });
            return false; // Evitar submit normal del formulario
        }
    });
});
