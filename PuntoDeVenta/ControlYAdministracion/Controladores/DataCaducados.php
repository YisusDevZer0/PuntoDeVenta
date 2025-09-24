<style>
  /* Personalizar el diseño de la paginación con CSS */
  .dataTables_wrapper .dataTables_paginate {
    text-align: center !important; /* Centrar los botones de paginación */
    margin-top: 10px !important;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px !important;
    border: 1px solid #ef7980 !important;
    margin: 2px !important;
    cursor: pointer !important;
    font-size: 16px !important;
    color: #ef7980 !important;
    background-color: #fff !important;
  }

  /* Cambiar el color del paginado seleccionado */
  .dataTables_paginate .paginate_button.current {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  /* Cambiar el color del hover */
  .dataTables_paginate .paginate_button:hover {
    background-color: #C80096 !important;
    color: #fff !important;
    border-color: #C80096 !important;
  }
</style>

<style>
  /* Estilos personalizados para la tabla */
  #Caducados th {
    font-size: 12px; /* Tamaño de letra para los encabezados */
    padding: 4px; /* Ajustar el espaciado entre los encabezados */
    white-space: nowrap; /* Evitar que los encabezados se dividan en varias líneas */
  }
</style>

<style>
  /* Estilos para la tabla */
  #Caducados {
    font-size: 12px; /* Tamaño de letra para el contenido de la tabla */
    border-collapse: collapse; /* Colapsar los bordes de las celdas */
    width: 100%;
    text-align: center; /* Centrar el contenido de las celdas */
  }

  #Caducados td {
    padding: 4px; /* Ajustar el espaciado entre las celdas */
    border: 1px solid #ddd; /* Agregar bordes a las celdas */
  }

  #Caducados th {
    background-color: #f8f9fa; /* Color de fondo para los encabezados */
    font-weight: bold; /* Hacer los encabezados en negrita */
  }
</style>

<div class="row">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0" style="color:#0172b6;">Control de Caducados</h6>
                <div>
                    <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalRegistrarLote">
                        <i class="fa fa-plus me-1"></i>Registrar Lote
                    </button>
                    <button class="btn btn-secondary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalConfiguracionCaducados">
                        <i class="fa fa-cog me-1"></i>Configuración
                    </button>
                </div>
            </div>
            
            <!-- Cards de Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-1">3 Meses</h6>
                                    <h3 class="mb-0" id="contador-3-meses">0</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-exclamation-triangle text-warning fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-1">6 Meses</h6>
                                    <h3 class="mb-0" id="contador-6-meses">0</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-exclamation-circle text-danger fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-1">9 Meses</h6>
                                    <h3 class="mb-0" id="contador-9-meses">0</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-info-circle text-info fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Total Lotes</h6>
                                    <h3 class="mb-0" id="contador-total">0</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-boxes text-success fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Sucursal</label>
                                    <select class="form-select" id="filtro-sucursal">
                                        <option value="">Todas las sucursales</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="filtro-estado">
                                        <option value="">Todos los estados</option>
                                        <option value="activo">Activo</option>
                                        <option value="agotado">Agotado</option>
                                        <option value="vencido">Vencido</option>
                                        <option value="retirado">Retirado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Alerta</label>
                                    <select class="form-select" id="filtro-alerta">
                                        <option value="">Todas las alertas</option>
                                        <option value="3_meses">3 Meses</option>
                                        <option value="6_meses">6 Meses</option>
                                        <option value="9_meses">9 Meses</option>
                                        <option value="vencido">Vencido</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button class="btn btn-primary" onclick="aplicarFiltros()">
                                            <i class="fa fa-filter me-1"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de Productos -->
            <div class="table-responsive">
                <table class="order-column" id="Caducados">
                    <thead>
                        <tr>
                            <th style="background-color:#ef7980 !important;">Código</th>
                            <th style="background-color:#ef7980 !important;">Producto</th>
                            <th style="background-color:#ef7980 !important;">Lote</th>
                            <th style="background-color:#ef7980 !important;">Fecha Caducidad</th>
                            <th style="background-color:#ef7980 !important;">Cantidad</th>
                            <th style="background-color:#ef7980 !important;">Sucursal</th>
                            <th style="background-color:#ef7980 !important;">Estado</th>
                            <th style="background-color:#ef7980 !important;">Alerta</th>
                            <th style="background-color:#ef7980 !important;">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarCargando() {
    document.getElementById('loading-overlay').style.display = 'flex';
}

function ocultarCargando() {
    document.getElementById('loading-overlay').style.display = 'none';
}

// Cargar estadísticas
function cargarEstadisticas() {
    $.get("api/productos_caducados_simple.php", function(data) {
        if (data.success) {
            $("#contador-3-meses").text(data.estadisticas.alerta_3_meses || 0);
            $("#contador-6-meses").text(data.estadisticas.alerta_6_meses || 0);
            $("#contador-9-meses").text(data.estadisticas.alerta_9_meses || 0);
            $("#contador-total").text(data.estadisticas.total || 0);
        }
    });
}

// Cargar sucursales para filtros
function cargarSucursales() {
    $.get("api/obtener_sucursales.php", function(data) {
        if (data.success) {
            const select = $("#filtro-sucursal");
            select.empty().append('<option value="">Todas las sucursales</option>');
            
            data.sucursales.forEach(sucursal => {
                select.append(`<option value="${sucursal.id}">${sucursal.nombre}</option>`);
            });
        }
    });
}

// Aplicar filtros
function aplicarFiltros() {
    tabla.ajax.reload();
}

// Inicializar DataTable
tabla = $('#Caducados').DataTable({
    "bProcessing": true,
    "ordering": true,
    "stateSave": true,
    "bAutoWidth": false,
    "order": [[ 3, "asc" ]], // Ordenar por fecha de caducidad
    "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayCaducados.php",
    "aoColumns": [
        { mData: 'cod_barra' },
        { mData: 'nombre_producto' },
        { mData: 'lote' },
        { mData: 'fecha_caducidad' },
        { mData: 'cantidad_actual' },
        { mData: 'sucursal' },
        { mData: 'estado' },
        { mData: 'alerta' },
        { mData: 'acciones' }
    ],
    "lengthMenu": [[20, 50, 100, 250, -1], [20, 50, 100, 250, "Todos"]],
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
        },
        "processing": function () {
            mostrarCargando();
        }
    },
    "initComplete": function() {
        ocultarCargando();
    },
    "buttons": [
        {
            extend: 'excelHtml5',
            text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
            titleAttr: 'Exportar a Excel',
            title: 'Control de Caducados',
            className: 'btn btn-success',
            exportOptions: {
                columns: ':visible'
            }
        }
    ],
    "dom": '<"d-flex justify-content-between"lf>rtip',
    "responsive": true
});

// Cargar datos iniciales
$(document).ready(function() {
    cargarEstadisticas();
    cargarSucursales();
});

// Eventos para modales
$('#modalRegistrarLote').on('show.bs.modal', function () {
    cargarSucursalesModal();
});

$('#modalConfiguracionCaducados').on('show.bs.modal', function () {
    cargarSucursalesConfiguracion();
});

// Funciones auxiliares para modales
function cargarSucursalesModal() {
    $.get("api/obtener_sucursales.php", function(data) {
        if (data.success) {
            const select = document.getElementById('sucursal');
            select.innerHTML = '<option value="">Seleccionar sucursal</option>';
            
            data.sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.id;
                option.textContent = sucursal.nombre;
                select.appendChild(option);
            });
        }
    });
}

function cargarSucursalesConfiguracion() {
    $.get("api/obtener_sucursales.php", function(data) {
        if (data.success) {
            const select = document.getElementById('sucursalConfig');
            select.innerHTML = '<option value="">Seleccionar sucursal</option>';
            
            data.sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.id;
                option.textContent = sucursal.nombre;
                select.appendChild(option);
            });
        }
    });
}

// Funciones para abrir modales (se implementarán en los archivos de modales)
function abrirModalDetallesLote(idLote) {
    // Implementar en DetallesLote.php
    console.log('Abrir detalles del lote:', idLote);
}

function abrirModalActualizarCaducidad(idLote, datosLote) {
    // Implementar en ActualizarCaducidad.php
    console.log('Abrir actualizar caducidad:', idLote, datosLote);
}

function abrirModalTransferirLote(idLote, datosLote) {
    // Implementar en TransferirLote.php
    console.log('Abrir transferir lote:', idLote, datosLote);
}
</script>
