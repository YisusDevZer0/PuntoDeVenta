
<div id="loading-overlay-cli">
        <div class="loader"></div>
        <div id="loading-text"></div>
    </div>
    <table id="TablaClientesSorteo" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>F. Nacimiento</th>
                <th>Edad</th>
                <th>Correo</th>
                <th>Sucursal</th>
                <th>Registrado</th>
                <th>Sistema</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(document).ready(function() {
        var tablaCli = $('#TablaClientesSorteo').DataTable({
            "bProcessing": true,
            "ordering": true,
            "stateSave": false,
            "bAutoWidth": false,
            "order": [[ 0, "desc" ]],
            "sAjaxSource": "Controladores/ArrayClientesSorteo.php",
            "aoColumns": [
                { mData: 'ID' },
                { mData: 'Nombre' },
                { mData: 'Telefono' },
                { mData: 'FechaNac' },
                { mData: 'Edad' },
                { mData: 'Correo' },
                { mData: 'Sucursal' },
                { mData: 'Registrado' },
                { mData: 'Sistema' }
            ],
            "lengthMenu": [[10,20,50,100, -1], [10,20,50,100, "Todos"]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "sPaginationType": "extStyle",
                "zeroRecords": "No se encontraron clientes",
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
                $('#loading-overlay-cli').hide();
            },
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    title: 'Clientes',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            "dom": '<"d-flex justify-content-between"lBf>rtip',
            "responsive": true
        });
        
        tablaCli.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#loading-overlay-cli').show();
            } else {
                $('#loading-overlay-cli').hide();
            }
        });
    });
</script>
