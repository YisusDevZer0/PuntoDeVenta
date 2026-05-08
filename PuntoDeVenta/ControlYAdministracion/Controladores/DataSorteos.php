
<div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text"></div>
    </div>
    <table id="TablaSorteos" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Participaciones</th>
                <th>Sucursales</th>
                <th>Creado por</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(document).ready(function() {
        var tabla = $('#TablaSorteos').DataTable({
            "bProcessing": true,
            "ordering": true,
            "stateSave": true,
            "bAutoWidth": false,
            "order": [[ 0, "desc" ]],
            "sAjaxSource": "Controladores/ArraySorteos.php",
            "aoColumns": [
                { mData: 'ID' },
                { mData: 'Nombre' },
                { mData: 'Descripcion' },
                { mData: 'FechaInicio' },
                { mData: 'FechaFin' },
                { mData: 'Estado' },
                { mData: 'Participaciones' },
                { mData: 'Sucursales' },
                { mData: 'CreadoPor' },
                { mData: 'Acciones' }
            ],
            "lengthMenu": [[10,20,50, -1], [10,20,50, "Todos"]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "sPaginationType": "extStyle",
                "zeroRecords": "No se encontraron sorteos",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "paginate": {
                    "first": '<i class="fas fa-angle-double-left"></i>',
                    "last": '<i class="fas fa-angle-double-right"></i>',
                    "next": '<i class="fas fa-angle-right"></i>',
                    "previous": '<i class="fas fa-angle-left"></i>'
                }
            },
            "initComplete": function() {
                $('#loading-overlay').hide();
            },
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    title: 'Sorteos',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            "dom": '<"d-flex justify-content-between"lBf>rtip',
            "responsive": true
        });
        
        tabla.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#loading-overlay').show();
            } else {
                $('#loading-overlay').hide();
            }
        });
    });
</script>
