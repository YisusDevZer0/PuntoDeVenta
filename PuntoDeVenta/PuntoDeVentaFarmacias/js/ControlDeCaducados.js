$(document).ready(function() {
    console.log('DataCaducados cargado');
    
    // Verificar que las funciones globales estén disponibles
    console.log('Funciones disponibles:', {
        abrirModalDetallesLote: typeof abrirModalDetallesLote,
        abrirModalActualizarCaducidad: typeof abrirModalActualizarCaducidad,
        abrirModalTransferirLote: typeof abrirModalTransferirLote
    });

    // Función para mostrar/ocultar overlay de carga
    function mostrarCargando() {
        $('#loading-overlay').show();
    }

    function ocultarCargando() {
        $('#loading-overlay').hide();
    }

    // Función para cargar estadísticas
    function cargarEstadisticas() {
        $.get('api/productos_proximos_caducar.php', function(data) {
            if (data.success && data.estadisticas) {
                const stats = data.estadisticas;
                
                // Crear HTML de estadísticas
                const statsHtml = `
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>${stats.total || 0}</h4>
                                    <p class="mb-0">Total Lotes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>${stats.alerta_3_meses || 0}</h4>
                                    <p class="mb-0">3 Meses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>${stats.alerta_6_meses || 0}</h4>
                                    <p class="mb-0">6 Meses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h4>${stats.alerta_9_meses || 0}</h4>
                                    <p class="mb-0">9 Meses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>${stats.vencidos || 0}</h4>
                                    <p class="mb-0">Vencidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarLote">
                                        <i class="fa fa-plus"></i> Registrar Lote
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#DataDeCaducados').html(statsHtml);
            }
        });
    }

    // Función para cargar sucursales
    function cargarSucursales() {
        $.get('api/obtener_sucursales.php', function(data) {
            if (data.success) {
                const select = document.getElementById('sucursal');
                if (select) {
                    select.innerHTML = '<option value="">Seleccionar sucursal</option>';
                    data.sucursales.forEach(sucursal => {
                        const option = document.createElement('option');
                        option.value = sucursal.id;
                        option.textContent = sucursal.nombre;
                        select.appendChild(option);
                    });
                }
            }
        });
    }

    // Inicializar DataTable
    var tabla = $('#tablaCaducados').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "Controladores/ArrayCaducados.php",
            "type": "GET",
            "dataSrc": "productos"
        },
        "columns": [
            { "data": "cod_barra" },
            { "data": "nombre_producto" },
            { "data": "lote" },
            { "data": "fecha_caducidad" },
            { "data": "cantidad_actual" },
            { "data": "estado" },
            { "data": "alerta" },
            { "data": "sucursal" },
            { "data": "dias_restantes" },
            { 
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return generarBotonesAccion(row);
                }
            }
        ],
        "order": [[3, "asc"]], // Ordenar por fecha de caducidad
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "initComplete": function() {
            console.log('DataTable inicializada correctamente');
        }
    });

    // Función para generar botones de acción
    function generarBotonesAccion(row) {
        const datosLote = {
            id_lote: row.id_lote,
            cod_barra: row.cod_barra,
            nombre_producto: row.nombre_producto,
            lote: row.lote,
            fecha_caducidad: row.fecha_caducidad,
            cantidad_actual: row.cantidad_actual,
            sucursal: row.sucursal,
            sucursal_id: row.sucursal_id || 1
        };
        
        const productoJson = JSON.stringify(datosLote);
        
        return `
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-info" onclick="abrirModalDetallesLote(${row.id_lote})" title="Ver detalles">
                    <i class="fa fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="abrirModalActualizarCaducidad(${row.id_lote}, '${productoJson.replace(/'/g, "\\'")}')" title="Actualizar fecha">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalTransferirLote(${row.id_lote}, '${productoJson.replace(/'/g, "\\'")}')" title="Transferir">
                    <i class="fa fa-truck"></i>
                </button>
            </div>
        `;
    }

    // Cargar estadísticas y sucursales al inicio
    cargarEstadisticas();
    cargarSucursales();

    // Manejar clics en botones de la tabla
    $('#tablaCaducados tbody').on('click', 'button', function() {
        const action = $(this).attr('title');
        console.log('Botón clickeado:', action);
    });
});
