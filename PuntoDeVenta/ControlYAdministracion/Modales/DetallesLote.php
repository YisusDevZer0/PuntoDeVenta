<!-- Modal para Detalles del Lote -->
<div class="modal fade" id="modalDetallesLote" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLoteLabel">
                    <i class="fa fa-info-circle mr-2"></i>Detalles del Lote
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Información principal del lote -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fa-solid fa-box me-2"></i>Información del Lote</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="detallesLoteInfo">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fa-solid fa-chart-line me-2"></i>Estado</h6>
                            </div>
                            <div class="card-body">
                                <div id="estadoLote">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historial de movimientos -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fa-solid fa-history me-2"></i>Historial de Movimientos</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Usuario</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historialLote">
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-warning" onclick="abrirModalActualizarCaducidad()">
                    <i class="fa fa-edit mr-1"></i>Actualizar Fecha
                </button>
                <button type="button" class="btn btn-primary" onclick="abrirModalTransferirLote()">
                    <i class="fa fa-truck mr-1"></i>Transferir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let loteActual = null;

function abrirModalDetallesLote(idLote) {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando detalles...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Cargar detalles del lote
    fetch(`api/obtener_detalles_lote.php?id=${idLote}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                loteActual = data.lote;
                mostrarDetallesLote(data.lote);
                mostrarHistorialLote(data.historial);
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('modalDetallesLote'));
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los detalles: ' + error.message
            });
        });
}

function mostrarDetallesLote(lote) {
    // Información principal
    document.getElementById('detallesLoteInfo').innerHTML = `
        <div class="col-md-6">
            <div class="mb-3">
                <strong>Producto:</strong><br>
                <span class="text-primary">${lote.nombre_producto}</span>
            </div>
            <div class="mb-3">
                <strong>Código de Barras:</strong><br>
                <code>${lote.cod_barra}</code>
            </div>
            <div class="mb-3">
                <strong>Número de Lote:</strong><br>
                <span class="badge bg-secondary">${lote.lote}</span>
            </div>
            <div class="mb-3">
                <strong>Proveedor:</strong><br>
                ${lote.proveedor || 'No especificado'}
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <strong>Fecha de Caducidad:</strong><br>
                <span class="text-danger">${lote.fecha_caducidad}</span>
            </div>
            <div class="mb-3">
                <strong>Cantidad Actual:</strong><br>
                <span class="badge bg-primary">${lote.cantidad_actual} unidades</span>
            </div>
            <div class="mb-3">
                <strong>Sucursal:</strong><br>
                ${lote.sucursal}
            </div>
            <div class="mb-3">
                <strong>Precio Venta:</strong><br>
                $${lote.precio_venta}
            </div>
        </div>
    `;
    
    // Estado del lote
    const diasRestantes = lote.dias_restantes;
    let estadoHtml = '';
    let estadoClass = '';
    
    if (diasRestantes < 0) {
        estadoHtml = '<span class="badge bg-danger">VENCIDO</span>';
        estadoClass = 'text-danger';
    } else if (diasRestantes <= 90) {
        estadoHtml = '<span class="badge bg-warning">3 MESES</span>';
        estadoClass = 'text-warning';
    } else if (diasRestantes <= 180) {
        estadoHtml = '<span class="badge bg-info">6 MESES</span>';
        estadoClass = 'text-info';
    } else if (diasRestantes <= 270) {
        estadoHtml = '<span class="badge bg-secondary">9 MESES</span>';
        estadoClass = 'text-secondary';
    } else {
        estadoHtml = '<span class="badge bg-success">NORMAL</span>';
        estadoClass = 'text-success';
    }
    
    document.getElementById('estadoLote').innerHTML = `
        <div class="text-center">
            <div class="mb-2">${estadoHtml}</div>
            <div class="${estadoClass}">
                <strong>${Math.abs(diasRestantes)} días</strong><br>
                <small>${diasRestantes < 0 ? 'vencido' : 'restantes'}</small>
            </div>
            <div class="mt-2">
                <small class="text-muted">
                    Registrado: ${lote.fecha_registro}
                </small>
            </div>
        </div>
    `;
}

function mostrarHistorialLote(historial) {
    const tbody = document.getElementById('historialLote');
    tbody.innerHTML = '';
    
    if (historial.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay movimientos registrados</td></tr>';
        return;
    }
    
    historial.forEach(movimiento => {
        const row = document.createElement('tr');
        
        let tipoIcon = '';
        let tipoClass = '';
        switch (movimiento.tipo_movimiento) {
            case 'registro':
                tipoIcon = 'fa-plus-circle';
                tipoClass = 'text-success';
                break;
            case 'actualizacion':
                tipoIcon = 'fa-edit';
                tipoClass = 'text-warning';
                break;
            case 'transferencia':
                tipoIcon = 'fa-truck';
                tipoClass = 'text-primary';
                break;
            case 'venta':
                tipoIcon = 'fa-shopping-cart';
                tipoClass = 'text-info';
                break;
            case 'ajuste':
                tipoIcon = 'fa-adjust';
                tipoClass = 'text-secondary';
                break;
            case 'vencimiento':
                tipoIcon = 'fa-calendar-times';
                tipoClass = 'text-danger';
                break;
        }
        
        row.innerHTML = `
            <td>${formatearFecha(movimiento.fecha_movimiento)}</td>
            <td>
                <i class="fa-solid ${tipoIcon} ${tipoClass} me-1"></i>
                ${movimiento.tipo_movimiento.toUpperCase()}
            </td>
            <td>${generarDescripcionMovimiento(movimiento)}</td>
            <td>${movimiento.usuario_nombre || 'Sistema'}</td>
            <td>${movimiento.observaciones || '-'}</td>
        `;
        
        tbody.appendChild(row);
    });
}

function generarDescripcionMovimiento(movimiento) {
    let descripcion = '';
    
    switch (movimiento.tipo_movimiento) {
        case 'registro':
            descripcion = `Registro inicial: ${movimiento.cantidad_nueva} unidades`;
            break;
        case 'actualizacion':
            descripcion = `Fecha actualizada: ${movimiento.fecha_caducidad_anterior} → ${movimiento.fecha_caducidad_nueva}`;
            break;
        case 'transferencia':
            descripcion = `Transferencia: ${movimiento.cantidad_nueva} unidades`;
            if (movimiento.sucursal_origen && movimiento.sucursal_destino) {
                descripcion += ` (Sucursal ${movimiento.sucursal_origen} → ${movimiento.sucursal_destino})`;
            }
            break;
        case 'venta':
            descripcion = `Venta: ${movimiento.cantidad_anterior} → ${movimiento.cantidad_nueva} unidades`;
            break;
        case 'ajuste':
            descripcion = `Ajuste: ${movimiento.cantidad_anterior} → ${movimiento.cantidad_nueva} unidades`;
            break;
        case 'vencimiento':
            descripcion = 'Producto marcado como vencido';
            break;
    }
    
    return descripcion;
}

function formatearFecha(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Funciones para abrir modales desde el modal de detalles
function abrirModalActualizarCaducidad() {
    if (loteActual) {
        // Cerrar modal actual
        bootstrap.Modal.getInstance(document.getElementById('modalDetallesLote')).hide();
        
        // Abrir modal de actualización
        setTimeout(() => {
            abrirModalActualizarCaducidad(loteActual.id_lote, loteActual);
        }, 300);
    }
}

function abrirModalTransferirLote() {
    if (loteActual) {
        // Cerrar modal actual
        bootstrap.Modal.getInstance(document.getElementById('modalDetallesLote')).hide();
        
        // Abrir modal de transferencia
        setTimeout(() => {
            abrirModalTransferirLote(loteActual.id_lote, loteActual);
        }, 300);
    }
}
</script>
