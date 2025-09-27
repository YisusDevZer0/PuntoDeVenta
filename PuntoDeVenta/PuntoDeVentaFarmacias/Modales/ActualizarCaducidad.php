<!-- Modal Actualizar Caducidad -->
<div class="modal fade" id="modalActualizarCaducidad" tabindex="-1" aria-labelledby="modalActualizarCaducidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalActualizarCaducidadLabel">
                    <i class="fa fa-edit me-2"></i>Actualizar Fecha de Caducidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idLoteActualizar" name="idLoteActualizar">
                
                <!-- Información del lote actual -->
                <div class="alert alert-info">
                    <h6>Información del Lote Actual:</h6>
                    <div id="infoLoteActual"></div>
                </div>
                
                <form id="formActualizarCaducidad">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaCaducidadNueva" class="form-label">Nueva Fecha de Caducidad *</label>
                                <input type="date" class="form-control" id="fechaCaducidadNueva" name="fechaCaducidadNueva" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="motivoActualizacion" class="form-label">Motivo de Actualización *</label>
                                <select class="form-select" id="motivoActualizacion" name="motivoActualizacion" required>
                                    <option value="">Seleccionar motivo</option>
                                    <option value="error_registro">Error en registro inicial</option>
                                    <option value="correccion_fecha">Corrección de fecha</option>
                                    <option value="extencion_plazo">Extensión de plazo</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacionesActualizacion" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesActualizacion" name="observacionesActualizacion" rows="3" placeholder="Describe el motivo de la actualización..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="guardarActualizacionCaducidad()">
                    <i class="fa fa-save me-1"></i>Actualizar Fecha
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarActualizacionCaducidad() {
    const idLote = document.getElementById('idLoteActualizar').value;
    const fechaNueva = document.getElementById('fechaCaducidadNueva').value;
    const motivo = document.getElementById('motivoActualizacion').value;
    const observaciones = document.getElementById('observacionesActualizacion').value;
    
    if (!idLote || !fechaNueva || !motivo) {
        Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    const data = {
        id_lote: idLote,
        fecha_caducidad_nueva: fechaNueva,
        motivo: motivo,
        observaciones: observaciones,
        usuario_movimiento: 1 // ID del usuario actual
    };
    
    fetch('api/actualizar_caducidad.php', {
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
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarCaducidad'));
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
