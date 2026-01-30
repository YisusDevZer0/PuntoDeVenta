function CargarRecepcionTraspasos() {
    var codigo = $('#buscar-codigo').val();
    var url = 'Controladores/DataRecepcionTraspasos.php';
    if (codigo) {
        url += '?codigo=' + encodeURIComponent(codigo);
    }

    if ($.fn.DataTable.isDataTable('#tablaRecepcionTraspasos')) {
        $('#tablaRecepcionTraspasos').DataTable().destroy();
    }

    $('#tablaRecepcionTraspasos').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: url, type: 'GET', dataSrc: 'aaData' },
        columns: [
            { data: 'TraspaNotID', title: 'ID', width: '70px', className: 'text-center' },
            { data: 'Folio_Ticket', title: 'Folio', width: '100px' },
            { data: 'Cod_Barra', title: 'Código', width: '120px' },
            { data: 'Nombre_Prod', title: 'Producto' },
            { data: 'Cantidad', title: 'Cant.', width: '80px', className: 'text-center' },
            { data: 'Fecha_venta', title: 'Fecha', width: '110px' },
            { data: 'Sucursal_Origen', title: 'Origen', width: '120px' },
            { data: 'AgregadoPor', title: 'Generado por', width: '140px' },
            { data: 'Recibir', title: 'Acciones', width: '120px', orderable: false, className: 'text-center' }
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
    if ($('#tablaRecepcionTraspasos').length === 0) {
        $('#DataRecepcionTraspasos').html(
            '<table id="tablaRecepcionTraspasos" class="table table-striped table-hover"><thead></thead><tbody></tbody></table>'
        );
    }
    setTimeout(CargarRecepcionTraspasos, 100);
});
