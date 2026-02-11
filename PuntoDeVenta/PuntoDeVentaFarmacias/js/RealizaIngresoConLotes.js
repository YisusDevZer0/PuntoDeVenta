/**
 * Módulo Ingresos con Lotes y Caducidad.
 * Valida Lote y Fecha de caducidad en cada fila y envía a RegistraIngresoMedicamentosFarmacia.
 */
$(document).ready(function () {
    function validarLotesYCaducidades() {
        var filas = $('#tablaAgregarArticulos tbody tr[data-id]');
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
        submitHandler: function () {
            if (!validarLotesYCaducidades()) return;
            $.ajax({
                type: 'POST',
                url: "Controladores/RegistraIngresoMedicamentosFarmacia.php",
                data: $('#VentasAlmomento').serialize(),
                cache: false,
                success: function (data) {
                    var response = JSON.parse(data);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ingresos guardados',
                            text: 'Los productos se registraron correctamente. Stock y Historial de lotes actualizados.',
                            showConfirmButton: false,
                            timer: 2200,
                            didOpen: function () {
                                setTimeout(function () { location.reload(); }, 1800);
                            },
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al guardar',
                            text: response.message || 'No se pudieron guardar los datos.',
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la petición',
                        text: 'No se pudieron guardar los datos. Inténtalo de nuevo.',
                    });
                }
            });
        },
    });
});
