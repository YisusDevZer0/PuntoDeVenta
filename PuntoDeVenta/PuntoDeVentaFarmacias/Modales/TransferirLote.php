<!-- Modal Transferir Lote -->
<div class="modal fade" id="modalTransferirLote" tabindex="-1" aria-labelledby="modalTransferirLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTransferirLoteLabel">
                    <i class="fa fa-truck me-2"></i>Transferir Lote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idLoteTransferir" name="idLoteTransferir">
                
                <!-- Información del lote origen -->
                <div class="alert alert-info">
                    <h6>Información del Lote Origen:</h6>
                    <div id="infoLoteOrigen"></div>
                </div>
                
                <form id="formTransferirLote">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sucursalDestino" class="form-label">Sucursal Destino *</label>
                                <select class="form-select" id="sucursalDestino" name="sucursalDestino" required>
                                    <option value="">Seleccionar sucursal destino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cantidadTransferir" class="form-label">Cantidad a Transferir *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="cantidadTransferir" name="cantidadTransferir" min="1" required>
                                    <span class="input-group-text">Máximo: <span id="cantidadDisponible">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="motivoTransferencia" class="form-label">Motivo de Transferencia *</label>
                                <select class="form-select" id="motivoTransferencia" name="motivoTransferencia" required>
                                    <option value="">Seleccionar motivo</option>
                                    <option value="reabastecimiento">Reabastecimiento</option>
                                    <option value="demanda_alta">Demanda alta</option>
                                    <option value="rotacion">Rotación de inventario</option>
                                    <option value="emergencia">Emergencia</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observacionesTransferencia" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacionesTransferencia" name="observacionesTransferencia" rows="2" placeholder="Detalles adicionales..."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarTransferencia()">
                    <i class="fa fa-truck me-1"></i>Transferir Lote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarTransferencia() {
    const idLote = document.getElementById('idLoteTransferir').value;
    const sucursalDestino = document.getElementById('sucursalDestino').value;
    const cantidad = document.getElementById('cantidadTransferir').value;
    const motivo = document.getElementById('motivoTransferencia').value;
    const observaciones = document.getElementById('observacionesTransferencia').value;
    
    if (!sucursalDestino || !cantidad || !motivo) {
        Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    const data = {
        id_lote: idLote,
        sucursal_destino: sucursalDestino,
        cantidad_transferir: parseInt(cantidad),
        motivo: motivo,
        observaciones: observaciones,
        usuario_movimiento: 1 // ID del usuario actual
    };
    
    fetch('api/transferir_lote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', data.message, 'success');
            // Cerrar modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalTransferirLote'));
            modal.hide();
            // Recargar tabla
            if (typeof tabla !== 'undefined') {
                tabla.ajax.reload();
            }
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}
</script>
