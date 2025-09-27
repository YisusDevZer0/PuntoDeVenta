<!-- Modal para Actualizar Caducidad -->
<div class="modal fade" id="modalActualizarCaducidad" tabindex="-1" role="dialog" aria-labelledby="modalActualizarCaducidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalActualizarCaducidadLabel">
                    <i class="fa fa-calendar-edit me-2"></i>Actualizar Fecha de Caducidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formActualizarCaducidad">
                    <input type="hidden" id="idLoteActualizar" name="idLote">
                    
                    <!-- Información del lote actual -->
                    <div class="alert alert-info">
                        <h6><i class="fa fa-info-circle mr-2"></i>Información del Lote</h6>
                        <div id="infoLoteActual">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fechaCaducidadNueva" class="form-label">Nueva Fecha de Caducidad *</label>
                        <input type="date" class="form-control" id="fechaCaducidadNueva" name="fechaCaducidadNueva" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivoActualizacion" class="form-label">Motivo de la Actualización *</label>
                        <select class="form-select" id="motivoActualizacion" name="motivoActualizacion" required>
                            <option value="">Seleccionar motivo</option>
                            <option value="correccion_error">Corrección de error</option>
                            <option value="actualizacion_proveedor">Actualización del proveedor</option>
                            <option value="revision_inventario">Revisión de inventario</option>
                            <option value="otro">Otro</option>
                        </select>
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
    const form = document.getElementById('formActualizarCaducidad');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    if (!formData.get('fechaCaducidadNueva') || !formData.get('motivoActualizacion')) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor completa todos los campos obligatorios'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Actualizando fecha de caducidad...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const datos = {
        id_lote: formData.get('idLote'),
        fecha_caducidad_nueva: formData.get('fechaCaducidadNueva'),
        motivo: formData.get('motivoActualizacion'),
        observaciones: formData.get('observacionesActualizacion'),
        usuario_movimiento: 1
    };
    
    fetch('api/actualizar_caducidad.php', {
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
                title: 'Fecha actualizada',
                text: data.message
            }).then(() => {
                // Cerrar modal y recargar datos
                $('#modalActualizarCaducidad').modal('hide');
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
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
            text: 'Error al actualizar la fecha: ' + error.message
        });
    });
}
</script>