/**
 * Módulo Ingresos con Lotes y Caducidad.
 * Valida Lote y Fecha de caducidad en cada fila y envía a RegistraIngresoMedicamentosFarmacia.
 */
$(document).ready(function () {
    function validarLotesYCaducidades() {
        // Buscar filas con data-id o que tengan los inputs del ingreso (DataTables puede cambiar el DOM)
        var filas = $('#tablaAgregarArticulos').find('tbody tr[data-id]');
        if (filas.length === 0) {
            filas = $('#tablaAgregarArticulos').find('tbody tr').has('input[name="Lote[]"]');
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

    function enviarIngresos() {
        if (!validarLotesYCaducidades()) return;
        $.ajax({
                type: 'POST',
                url: "Controladores/RegistraIngresoMedicamentosFarmacia.php",
                data: $('#VentasAlmomento').serialize(),
                cache: false,
                dataType: 'json',
                success: function (response) {
                    if (response && response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Guardado exitoso!',
                            text: response.message || 'Los productos se registraron correctamente. Stock y lotes actualizados.',
                            confirmButtonText: 'Aceptar',
                            timer: 3500,
                            timerProgressBar: true,
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al guardar',
                            text: (response && response.message) ? response.message : 'No se pudieron guardar los datos.',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                },
                error: function (xhr, status, err) {
                    var msg = 'No se pudieron guardar los datos. Inténtalo de nuevo.';
                    if (xhr && xhr.responseText) {
                        try {
                            var r = JSON.parse(xhr.responseText);
                            if (r && r.message) msg = r.message;
                        } catch (e) {}
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        text: msg,
                        confirmButtonText: 'Aceptar',
                    });
                }
            });
    }

    // Click directo en el botón (no depende de jQuery Validate)
    $(document).on('click', '#btnGuardarIngresos', function (e) {
        e.preventDefault();
        enviarIngresos();
    });
});
