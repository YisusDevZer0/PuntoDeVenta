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
                    <button class="btn btn-primary btn-sm me-2" onclick="abrirModalRegistrarLote()">
                        <i class="fa fa-plus me-1"></i>Registrar Lote
                    </button>
                    <button class="btn btn-secondary btn-sm me-2" onclick="abrirModalConfiguracion()">
                        <i class="fa fa-cog me-1"></i>Configuración
                    </button>
                    <a href="instalar_caducados.php" class="btn btn-warning btn-sm">
                        <i class="fa fa-database me-1"></i>Instalar Módulo
                    </a>
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
                <table class="table table-striped" id="Caducados">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Fecha Caducidad</th>
                            <th>Cantidad</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Alerta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-caducados">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#Caducados').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pageLength": 25,
        "order": [[ 3, "asc" ]], // Ordenar por fecha de caducidad
        "columnDefs": [
            { "orderable": false, "targets": 8 } // Deshabilitar ordenamiento en columna de acciones
        ]
    });
    
    // Cargar datos iniciales
    cargarEstadisticas();
    cargarSucursales();
    cargarProductosCaducados();
});

function cargarEstadisticas() {
    $.get("api/productos_caducados_simple.php", function(data) {
        if (data.success) {
            $("#contador-3-meses").text(data.estadisticas.alerta_3_meses || 0);
            $("#contador-6-meses").text(data.estadisticas.alerta_6_meses || 0);
            $("#contador-9-meses").text(data.estadisticas.alerta_9_meses || 0);
            $("#contador-total").text(data.estadisticas.total || 0);
            
            // Mostrar mensaje si las tablas no existen
            if (data.estadisticas.mensaje) {
                Swal.fire({
                    icon: 'info',
                    title: 'Configuración requerida',
                    text: data.estadisticas.mensaje,
                    confirmButtonText: 'Entendido'
                });
            }
        }
    });
}

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

function cargarProductosCaducados() {
    $.get("api/productos_caducados_simple.php", function(data) {
        if (data.success) {
            mostrarProductosEnTabla(data.productos);
        }
    });
}

function obtenerFiltros() {
    return {
        sucursal: $("#filtro-sucursal").val(),
        estado: $("#filtro-estado").val(),
        tipo_alerta: $("#filtro-alerta").val()
    };
}

function aplicarFiltros() {
    cargarProductosCaducados();
}

function mostrarProductosEnTabla(productos) {
    const tbody = $("#tbody-caducados");
    tbody.empty();
    
    if (productos.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-2x mb-2"></i><br>
                    No hay productos que coincidan con los filtros
                </td>
            </tr>
        `);
        return;
    }
    
    productos.forEach(producto => {
        const row = generarFilaProducto(producto);
        tbody.append(row);
    });
    
    // Refrescar DataTable
    $('#Caducados').DataTable().destroy();
    $('#Caducados').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pageLength": 25,
        "order": [[ 3, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": 8 }
        ]
    });
}

function generarFilaProducto(producto) {
    const estadoBadge = generarBadgeEstado(producto.estado);
    const alertaBadge = generarBadgeAlerta(producto.tipo_alerta, producto.dias_restantes);
    const acciones = generarBotonesAccion(producto);
    
    return `
        <tr>
            <td><code>${producto.cod_barra}</code></td>
            <td>
                <div class="fw-bold">${producto.nombre_producto}</div>
                <small class="text-muted">${producto.proveedor || 'Sin proveedor'}</small>
            </td>
            <td><span class="badge bg-secondary">${producto.lote}</span></td>
            <td>
                <div class="fw-bold ${producto.dias_restantes < 0 ? 'text-danger' : ''}">${producto.fecha_caducidad}</div>
                <small class="text-muted">${producto.dias_restantes} días</small>
            </td>
            <td>
                <span class="badge bg-primary">${producto.cantidad_actual}</span>
            </td>
            <td>${producto.sucursal}</td>
            <td>${estadoBadge}</td>
            <td>${alertaBadge}</td>
            <td>${acciones}</td>
        </tr>
    `;
}

function generarBadgeEstado(estado) {
    const estados = {
        'activo': { class: 'bg-success', text: 'Activo' },
        'agotado': { class: 'bg-warning', text: 'Agotado' },
        'vencido': { class: 'bg-danger', text: 'Vencido' },
        'retirado': { class: 'bg-secondary', text: 'Retirado' }
    };
    
    const estadoInfo = estados[estado] || { class: 'bg-secondary', text: estado };
    return `<span class="badge ${estadoInfo.class}">${estadoInfo.text}</span>`;
}

function generarBadgeAlerta(tipoAlerta, diasRestantes) {
    if (diasRestantes < 0) {
        return '<span class="badge bg-danger">VENCIDO</span>';
    }
    
    const alertas = {
        '3_meses': { class: 'bg-warning', text: '3 MESES' },
        '6_meses': { class: 'bg-danger', text: '6 MESES' },
        '9_meses': { class: 'bg-info', text: '9 MESES' },
        'normal': { class: 'bg-success', text: 'NORMAL' }
    };
    
    const alertaInfo = alertas[tipoAlerta] || { class: 'bg-secondary', text: tipoAlerta };
    return `<span class="badge ${alertaInfo.class}">${alertaInfo.text}</span>`;
}

function generarBotonesAccion(producto) {
    return `
        <div class="btn-group" role="group">
            <button class="btn btn-sm btn-outline-info" onclick="abrirModalDetallesLote(${producto.id_lote})" title="Ver detalles">
                <i class="fa fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning" onclick="abrirModalActualizarCaducidad(${producto.id_lote}, '${JSON.stringify(producto).replace(/"/g, '&quot;')}')" title="Actualizar fecha">
                <i class="fa fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModalTransferirLote(${producto.id_lote}, '${JSON.stringify(producto).replace(/"/g, '&quot;')}')" title="Transferir">
                <i class="fa fa-truck"></i>
            </button>
        </div>
    `;
}

// Funciones para abrir modales (se implementarán en los archivos de modales)
function abrirModalRegistrarLote() {
    // Implementar en RegistrarLote.php
}

function abrirModalActualizarCaducidad(idLote, datosLote) {
    // Implementar en ActualizarCaducidad.php
}

function abrirModalTransferirLote(idLote, datosLote) {
    // Implementar en TransferirLote.php
}

function abrirModalDetallesLote(idLote) {
    // Implementar en DetallesLote.php
}

function abrirModalConfiguracion() {
    // Implementar en ConfiguracionCaducados.php
}
</script>
