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
});