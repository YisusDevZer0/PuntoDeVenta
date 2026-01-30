function CargarRecepcionTraspasos() {
    var codigo = $('#buscar-codigo').val();
    var url = 'Controladores/DataRecepcionTraspasos.php';
    if (codigo) {
        url += '?codigo=' + encodeURIComponent(codigo);
    }

    if ($.fn.DataTable.isDataTable('#tablaRecepcionTraspasos')) {
        $('#tablaRecepcionTraspasos').DataTable().destroy();
        $('#tablaRecepcionTraspasos').empty();
    }

    $('#tablaRecepcionTraspasos').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: url,
            type: 'GET',
            dataSrc: function(json) {
                if (json.error) {
                    console.error('Error del servidor:', json.error);
                    Swal.fire('Error', json.error, 'error');
                    return [];
                }
                return json.aaData || [];
            },
            error: function(xhr, error, thrown) {
                console.error('Error AJAX:', error, thrown);
                var errorMsg = 'Error al cargar los datos';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        },
        columns: [
            { data: 'TraspaNotID', title: 'ID', width: '70px', className: 'text-center', defaultContent: '-' },
            { data: 'Folio_Ticket', title: 'Folio', width: '100px', defaultContent: '-' },
            { data: 'Cod_Barra', title: 'Código', width: '120px', defaultContent: '-' },
            { data: 'Nombre_Prod', title: 'Producto', defaultContent: '-' },
            { data: 'Cantidad', title: 'Cant.', width: '80px', className: 'text-center', defaultContent: '-' },
            { data: 'Fecha_venta', title: 'Fecha', width: '110px', defaultContent: '-' },
            { data: 'Sucursal_Origen', title: 'Origen', width: '120px', defaultContent: '-' },
            { data: 'AgregadoPor', title: 'Generado por', width: '140px', defaultContent: '-' },
            { data: 'Recibir', title: 'Acciones', width: '120px', orderable: false, className: 'text-center', defaultContent: '-' }
        ],
        language: {
            lengthMenu: 'Mostrar _MENU_ registros',
            zeroRecords: 'No hay traspasos pendientes de recibir',
            info: 'Mostrando _START_ a _END_ de _TOTAL_',
            infoEmpty: 'Sin registros',
            infoFiltered: '(filtrado de _MAX_)',
            search: 'Buscar:',
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
}

$(document).ready(function() {
    // Asegurar que la tabla existe antes de inicializar
    if ($('#tablaRecepcionTraspasos').length === 0) {
        $('#DataRecepcionTraspasos').html(
            '<table id="tablaRecepcionTraspasos" class="table table-striped table-hover table-bordered"><thead></thead><tbody></tbody></table>'
        );
    }
    
    // Limpiar cualquier instancia previa de DataTables
    if ($.fn.DataTable.isDataTable('#tablaRecepcionTraspasos')) {
        $('#tablaRecepcionTraspasos').DataTable().destroy();
        $('#tablaRecepcionTraspasos').empty();
    }
    
    // Inicializar después de un pequeño delay para asegurar que el DOM está listo
    setTimeout(function() {
        try {
            CargarRecepcionTraspasos();
        } catch (e) {
            console.error('Error al cargar tabla:', e);
            Swal.fire('Error', 'Error al inicializar la tabla: ' + e.message, 'error');
        }
    }, 200);
});
