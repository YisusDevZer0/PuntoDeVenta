$('document').ready(function($){

    // === FORMULARIO NUEVO SORTEO ===
    $("#formNuevoSorteo").on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        // Agregar aplica_todas basado en el checkbox
        var aplicaTodas = $('#sorteo_aplica_todas').is(':checked') ? '1' : '0';
        formData += '&aplica_todas=' + aplicaTodas;
        
        // Agregar sucursales seleccionadas
        var sucursales = [];
        $('.nuevo-sucursal-cb:checked').each(function() {
            sucursales.push($(this).val());
        });
        formData += '&sucursales=' + encodeURIComponent(JSON.stringify(sucursales));

        $.ajax({
            type: 'POST',
            url: 'Controladores/SorteosController.php',
            data: formData,
            cache: false,
            success: function(dataResult) {
                var resp = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                if (resp.status === 'success') {
                    $("#formNuevoSorteo")[0].reset();
                    $('#modalNuevoSorteo').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: resp.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    CargaSorteos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        });
        return false;
    });

    // === FORMULARIO EDITAR SORTEO ===
    $("#formEditarSorteo").on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        // Agregar aplica_todas basado en el checkbox
        var aplicaTodas = $('#edit_sorteo_aplica_todas').is(':checked') ? '1' : '0';
        formData += '&aplica_todas=' + aplicaTodas;
        
        // Agregar sucursales seleccionadas
        var sucursales = [];
        $('.edit-sucursal-cb:checked').each(function() {
            sucursales.push($(this).val());
        });
        formData += '&sucursales=' + encodeURIComponent(JSON.stringify(sucursales));

        $.ajax({
            type: 'POST',
            url: 'Controladores/SorteosController.php',
            data: formData,
            cache: false,
            success: function(dataResult) {
                var resp = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                if (resp.status === 'success') {
                    $('#modalEditarSorteo').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: resp.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    CargaSorteos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        });
        return false;
    });

});
