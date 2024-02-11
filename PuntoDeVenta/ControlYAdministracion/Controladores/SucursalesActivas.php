
<div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text"></div>
    </div>
    <table id="Productos" class="table table-hover">
        <thead>
            <tr>
                <th>ID Empleado</th>
                <th>Nombre</th>
                <th>Fotografia</th>
                <th>Tipo de usuario</th>
                <th>Sucursal</th>
                <th>Fecha|Hora de creacion</th>
                <th>Estado</th>
                <th>Creado por</th>
                <th>Acciones</th>
                <!-- Agrega más columnas si es necesario -->
            </tr>
        </thead>
    </table>
</div>
<script>
    $(document).ready(function() {
        var tabla = $('#Productos').DataTable({
            "bProcessing": true,
            "ordering": true,
            "stateSave": true,
            "bAutoWidth": false,
            "order": [[ 0, "desc" ]],
            "sAjaxSource": "Controladores/ArraySucursales.php",
            "aoColumns": [
                { mData: 'Idsucursal' },
                { mData: 'NombreSucursal' },
                { mData: 'Direccion' },
                { mData: 'Telefono' },
                { mData: 'Pin' },
                { mData: 'CreadoEl' },
                { mData: 'Estatus' },
                { mData: 'CreadoPor' },
                { mData: 'Acciones' }
            ],
            "lengthMenu": [[10,20,150,250,500, -1], [10,20,50,250,500, "Todos"]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "sPaginationType": "extStyle",
                "zeroRecords": "No se encontraron resultados",
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
                // Al completar la inicialización de la tabla, ocultar el mensaje de carga
                $('#loading-overlay').hide();
            },
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    title: 'Base de productos',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible' // Exportar solo las columnas visibles
                    }
                }
            ],
            "dom": '<"d-flex justify-content-between"lBf>rtip',
            "responsive": true
        });
        
        // Mostrar el mensaje de carga mientras se procesan los datos
        tabla.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#loading-overlay').show();
            } else {
                $('#loading-overlay').hide();
            }
        });
    });
</script>
