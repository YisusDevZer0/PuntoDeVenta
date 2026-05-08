
<div id="loading-overlay-part">
        <div class="loader"></div>
        <div id="loading-text"></div>
    </div>
    <table id="TablaParticipaciones" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sorteo</th>
                <th>Ticket</th>
                <th>Folio Rifa</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>F. Nacimiento</th>
                <th>Sucursal</th>
                <th>Participa</th>
                <th>Registrado</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(document).ready(function() {
        var sorteoFiltro = $('#filtroSorteoParticipaciones').val() || '0';
        var tablaP = $('#TablaParticipaciones').DataTable({
            "bProcessing": true,
            "ordering": true,
            "stateSave": false,
            "bAutoWidth": false,
            "order": [[ 0, "desc" ]],
            "sAjaxSource": "Controladores/ArrayParticipaciones.php?sorteo_id=" + sorteoFiltro,
            "aoColumns": [
                { mData: 'ID' },
                { mData: 'Sorteo' },
                { mData: 'Ticket' },
                { mData: 'FolioRifa' },
                { mData: 'Cliente' },
                { mData: 'Telefono' },
                { mData: 'FechaNac' },
                { mData: 'Sucursal' },
                { mData: 'Participa' },
                { mData: 'Registrado' }
            ],
            "lengthMenu": [[10,20,50,100, -1], [10,20,50,100, "Todos"]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "sPaginationType": "extStyle",
                "zeroRecords": "No se encontraron participaciones",
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
                $('#loading-overlay-part').hide();
            },
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    title: 'Participaciones de Sorteo',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            "dom": '<"d-flex justify-content-between"lBf>rtip',
            "responsive": true
        });
        
        tablaP.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#loading-overlay-part').show();
            } else {
                $('#loading-overlay-part').hide();
            }
        });
    });
</script>
