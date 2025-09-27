<!-- Modal Detalles Lote -->
<div class="modal fade" id="modalDetallesLote" tabindex="-1" aria-labelledby="modalDetallesLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetallesLoteLabel">
                    <i class="fa fa-info-circle me-2"></i>Detalles del Lote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del lote -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Información del Lote</h6>
                        <div id="detallesLoteInfo" class="row"></div>
                    </div>
                </div>
                
                <!-- Estado y observaciones -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div id="estadoLote"></div>
                    </div>
                </div>
                
                <!-- Historial de movimientos -->
                <div class="row">
                    <div class="col-12">
                        <h6>Historial de Movimientos</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cambios</th>
                                        <th>Usuario</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="historialLote">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Cargando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-warning" onclick="abrirModalActualizarCaducidad()" id="btnActualizarDesdeDetalles">
                    <i class="fa fa-edit me-1"></i>Actualizar Fecha
                </button>
                <button type="button" class="btn btn-primary" onclick="abrirModalTransferirDesdeDetalles()" id="btnTransferirDesdeDetalles">
                    <i class="fa fa-truck me-1"></i>Transferir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalActualizarCaducidad() {
    // Obtener datos del lote actual del modal de detalles
    const idLote = document.getElementById('modalDetallesLote').getAttribute('data-lote-id');
    if (idLote) {
        // Cerrar modal actual
        var modalActual = bootstrap.Modal.getInstance(document.getElementById('modalDetallesLote'));
        modalActual.hide();
        
        // Abrir modal de actualización
        setTimeout(() => {
            abrirModalActualizarCaducidad(idLote, '{}');
        }, 300);
    }
}

function abrirModalTransferirDesdeDetalles() {
    // Obtener datos del lote actual del modal de detalles
    const idLote = document.getElementById('modalDetallesLote').getAttribute('data-lote-id');
    if (idLote) {
        // Cerrar modal actual
        var modalActual = bootstrap.Modal.getInstance(document.getElementById('modalDetallesLote'));
        modalActual.hide();
        
        // Abrir modal de transferencia
        setTimeout(() => {
            abrirModalTransferirLote(idLote, '{}');
        }, 300);
    }
}
</script>
