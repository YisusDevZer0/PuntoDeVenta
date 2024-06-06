$(document).ready(function() {
    // Hacer una solicitud AJAX para obtener los proveedores
    $.ajax({
        url: 'Controladores/get_proveedores.php', // Archivo PHP que creamos
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Rellenar el select con los proveedores
            var proveedoresSelect = $('#proveedoresSelect');
            $.each(response, function(index, proveedor) {
                proveedoresSelect.append('<option value="' + proveedor + '">' + proveedor + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los proveedores:', error);
        }
    });
    
    // Función para habilitar/deshabilitar el input de producto
    function toggleCodigoEscaneado() {
        const proveedorValue = $('#proveedoresSelect').val();
        const facturaValue = $('#numerofactura').val().trim();
        const codigoEscaneadoInput = $('#codigoEscaneado');
        
        if (proveedorValue === "" || facturaValue === "") {
            codigoEscaneadoInput.prop('disabled', true);
        } else {
            codigoEscaneadoInput.prop('disabled', false);
        }
    }

    // Eventos de cambio para el select y el input de factura
    $('#proveedoresSelect, #numerofactura').on('change keyup', function() {
        toggleCodigoEscaneado();
    });

    // Inicializar el estado del input de producto al cargar la página
    toggleCodigoEscaneado();

});

