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
            { "data": "Cod_Barra", "title": "Código", "width": "120px" },
            { "data": "Nombre_Prod", "title": "Producto" },
            { "data": "Lote", "title": "Lote", "width": "120px" },
            { "data": "Fecha_Caducidad", "title": "Fecha Caducidad", "width": "130px" },
            { "data": "Existencias", "title": "Cantidad", "width": "100px", "className": "text-center" },
            { 
                "data": "Dias_restantes", 
                "title": "Días Restantes", 
                "width": "130px",
                "className": "text-center",
                "render": function(data, type, row) {
                    if (data < 0) {
                        return '<span class="badge bg-danger">Vencido (' + Math.abs(data) + ' días)</span>';
                    } else if (data <= 15) {
                        return '<span class="badge bg-warning text-dark">' + data + ' días</span>';
                    } else {
                        return '<span class="badge bg-success">' + data + ' días</span>';
                    }
                }
            },
            { "data": "Estado", "title": "Estado", "width": "130px", "className": "text-center" },
            { "data": "Sucursal", "title": "Sucursal" },
            { "data": "Usuario_Modifico", "title": "Usuario", "width": "150px" },
            { "data": "Editar", "title": "Acciones", "width": "80px", "orderable": false, "className": "text-center" }
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
