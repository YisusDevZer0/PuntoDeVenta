<!-- Modal para Transferir Lote -->
<div class="modal fade" id="modalTransferirLote" tabindex="-1" role="dialog" aria-labelledby="modalTransferirLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTransferirLoteLabel">
                    <i class="fa fa-truck mr-2"></i>Transferir Lote
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTransferirLote">
                    <input type="hidden" id="idLoteTransferir" name="idLote">
                    
                    <!-- Información del lote origen -->
                    <div class="alert alert-info">
                        <h6><i class="fa fa-info-circle mr-2"></i>Lote Origen</h6>
                        <div id="infoLoteOrigen">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sucursalDestino" class="form-label">Sucursal Destino *</label>
                                <select class="form-control" id="sucursalDestino" name="sucursalDestino" required>
                                    <option value="">Seleccionar sucursal destino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cantidadTransferir" class="form-label">Cantidad a Transferir *</label>
                                <input type="number" class="form-control" id="cantidadTransferir" name="cantidadTransferir" min="1" required>
                                <div class="form-text">Cantidad disponible: <span id="cantidadDisponible">0</span> unidades</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivoTransferencia" class="form-label">Motivo de la Transferencia *</label>
                        <select class="form-control" id="motivoTransferencia" name="motivoTransferencia" required>
                            <option value="">Seleccionar motivo</option>
                            <option value="redistribucion">Redistribución de inventario</option>
                            <option value="demanda_sucursal">Demanda de la sucursal</option>
                            <option value="vencimiento_proximo">Vencimiento próximo</option>
                            <option value="ajuste_stock">Ajuste de stock</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacionesTransferencia" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesTransferencia" name="observacionesTransferencia" rows="3" placeholder="Describe el motivo de la transferencia..."></textarea>
                    </div>
                    
                    <!-- Resumen de la transferencia -->
                    <div class="alert alert-warning">
                        <h6><i class="fa-solid fa-exclamation-triangle me-2"></i>Resumen de la Transferencia</h6>
                        <div id="resumenTransferencia">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarTransferencia()">
                    <i class="fa fa-truck mr-1"></i>Transferir Lote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalTransferirLote(idLote, datosLote) {
    // Llenar información del lote origen
    document.getElementById('idLoteTransferir').value = idLote;
    document.getElementById('infoLoteOrigen').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <strong>Producto:</strong> ${datosLote.nombre_producto}<br>
                <strong>Código:</strong> ${datosLote.cod_barra}<br>
                <strong>Lote:</strong> ${datosLote.lote}
            </div>
            <div class="col-md-6">
                <strong>Fecha Caducidad:</strong> ${datosLote.fecha_caducidad}<br>
                <strong>Cantidad Disponible:</strong> ${datosLote.cantidad_actual}<br>
                <strong>Sucursal Actual:</strong> ${datosLote.sucursal}
            </div>
        </div>
    `;
    
    // Establecer cantidad máxima
    document.getElementById('cantidadDisponible').textContent = datosLote.cantidad_actual;
    document.getElementById('cantidadTransferir').max = datosLote.cantidad_actual;
    document.getElementById('cantidadTransferir').value = 1;
    
    // Cargar sucursales destino (excluyendo la actual)
    cargarSucursalesDestino(datosLote.sucursal_id);
    
    // Limpiar otros campos
    document.getElementById('motivoTransferencia').value = '';
    document.getElementById('observacionesTransferencia').value = '';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalTransferirLote'));
    modal.show();
}

function cargarSucursalesDestino(sucursalOrigen) {
    fetch('api/obtener_sucursales.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sucursalDestino');
            select.innerHTML = '<option value="">Seleccionar sucursal destino</option>';
            
            data.sucursales.forEach(sucursal => {
                if (sucursal.id != sucursalOrigen) {
                    const option = document.createElement('option');
                    option.value = sucursal.id;
                    option.textContent = sucursal.nombre;
                    select.appendChild(option);
                }
            });
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
}

function actualizarResumenTransferencia() {
    const cantidad = document.getElementById('cantidadTransferir').value;
    const sucursalDestino = document.getElementById('sucursalDestino');
    const motivo = document.getElementById('motivoTransferencia').value;
    
    if (cantidad && sucursalDestino.value) {
        const nombreSucursal = sucursalDestino.options[sucursalDestino.selectedIndex].text;
        document.getElementById('resumenTransferencia').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Cantidad:</strong> ${cantidad} unidades<br>
                    <strong>Destino:</strong> ${nombreSucursal}
                </div>
                <div class="col-md-6">
                    <strong>Motivo:</strong> ${motivo || 'No especificado'}<br>
                    <strong>Estado:</strong> Listo para transferir
                </div>
            </div>
        `;
    } else {
        document.getElementById('resumenTransferencia').innerHTML = `
            <div class="text-muted">Complete los campos para ver el resumen</div>
        `;
    }
}

// Event listeners
document.getElementById('cantidadTransferir').addEventListener('input', actualizarResumenTransferencia);
document.getElementById('sucursalDestino').addEventListener('change', actualizarResumenTransferencia);
document.getElementById('motivoTransferencia').addEventListener('change', actualizarResumenTransferencia);

function guardarTransferencia() {
    const form = document.getElementById('formTransferirLote');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const requiredFields = ['sucursalDestino', 'cantidadTransferir', 'motivoTransferencia'];
    for (let field of requiredFields) {
        if (!formData.get(field)) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: `Por favor completa el campo: ${field}`
            });
            return;
        }
    }
    
    // Validar cantidad
    const cantidad = parseInt(formData.get('cantidadTransferir'));
    const cantidadDisponible = parseInt(document.getElementById('cantidadDisponible').textContent);
    
    if (cantidad > cantidadDisponible) {
        Swal.fire({
            icon: 'error',
            title: 'Cantidad inválida',
            text: 'La cantidad a transferir no puede ser mayor a la disponible'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Transferiendo lote...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const datos = {
        id_lote: formData.get('idLote'),
        sucursal_destino: formData.get('sucursalDestino'),
        cantidad_transferir: cantidad,
        motivo: formData.get('motivoTransferencia'),
        observaciones: formData.get('observacionesTransferencia'),
        usuario_movimiento: <?php echo isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1; ?>
    };
    
    fetch('api/transferir_lote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Lote transferido',
                text: data.message
            }).then(() => {
                // Cerrar modal y recargar datos
                bootstrap.Modal.getInstance(document.getElementById('modalTransferirLote')).hide();
                cargarProductosCaducados();
                cargarEstadisticas();
            });
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
            text: 'Error al transferir el lote: ' + error.message
        });
    });
}
</script>
