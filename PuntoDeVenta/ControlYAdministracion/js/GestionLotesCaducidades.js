// Función para cargar los lotes y caducidades
function CargarLotesCaducidades() {
    var codigo = $('#buscar-codigo').val();
    var sucursal = $('#filtro-sucursal').val();
    var estado = $('#filtro-estado').val();
    
    var url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataLotesCaducidades.php';
    var params = [];
    
    if (codigo) params.push('codigo=' + encodeURIComponent(codigo));
    if (sucursal) params.push('sucursal=' + encodeURIComponent(sucursal));
    if (estado) params.push('estado=' + encodeURIComponent(estado));
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    if ($.fn.DataTable.isDataTable('#tablaLotes')) {
        $('#tablaLotes').DataTable().destroy();
    }
    
    $('#tablaLotes').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": url,
            "type": "GET",
            "dataSrc": "aaData"
        },
        "columns": [
            { "data": "Cod_Barra", "title": "Código de Barras" },
            { "data": "Nombre_Prod", "title": "Producto" },
            { "data": "Lote", "title": "Lote" },
            { "data": "Fecha_Caducidad", "title": "Fecha Caducidad" },
            { "data": "Existencias", "title": "Existencias" },
            { "data": "Dias_restantes", "title": "Días Restantes" },
            { "data": "Estado", "title": "Estado" },
            { "data": "Sucursal", "title": "Sucursal" },
            { "data": "Usuario_Modifico", "title": "Último Usuario" },
            { "data": "Editar", "title": "Acciones", "orderable": false }
        ],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },
        "order": [[3, "asc"]], // Ordenar por fecha de caducidad
        "pageLength": 25,
        "responsive": true
    });
}

// Inicializar tabla al cargar la página
$(document).ready(function() {
    // Crear tabla si no existe
    if ($('#tablaLotes').length === 0) {
        $('#DataLotesCaducidades').html(`
            <table id="tablaLotes" class="table table-striped table-hover">
                <thead></thead>
                <tbody></tbody>
            </table>
        `);
    }
    
    // Esperar un momento para asegurar que el DOM esté listo
    setTimeout(function() {
        CargarLotesCaducidades();
    }, 100);
});
