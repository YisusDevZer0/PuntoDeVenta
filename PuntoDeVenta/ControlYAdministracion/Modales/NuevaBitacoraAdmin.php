<!-- Modal para crear nueva bitácora administrativa -->
<div class="modal fade" id="ModalNuevaBitacoraAdmin" tabindex="-1" aria-labelledby="ModalNuevaBitacoraAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ModalNuevaBitacoraAdminLabel">
                    <i class="fa fa-plus me-2"></i>Nueva Bitácora de Limpieza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaBitacoraAdmin">
                    <div class="row g-3">
                        <!-- Sucursal -->
                        <div class="col-md-6">
                            <label for="sucursal_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                            <select class="form-select" id="sucursal_id" name="sucursal_id" required>
                                <option value="">Seleccionar sucursal</option>
                                <?php 
                                $sucursales = $controller->obtenerSucursales();
                                foreach($sucursales as $sucursal): 
                                ?>
                                    <option value="<?php echo $sucursal['ID_Sucursal']; ?>">
                                        <?php echo htmlspecialchars($sucursal['Nombre_Sucursal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Área -->
                        <div class="col-md-6">
                            <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="area" name="area" 
                                   placeholder="Ej: Cocina, Sala, Baños, etc." required>
                        </div>
                        
                        <!-- Semana -->
                        <div class="col-md-6">
                            <label for="semana" class="form-label">Semana <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="semana" name="semana" 
                                   placeholder="Ej: Semana 1, Semana 2, etc." required>
                        </div>
                        
                        <!-- Fecha Inicio -->
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        
                        <!-- Fecha Fin -->
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        
                        <!-- Responsable -->
                        <div class="col-md-6">
                            <label for="responsable" class="form-label">Responsable <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="responsable" name="responsable" 
                                   placeholder="Nombre del responsable" required>
                        </div>
                        
                        <!-- Supervisor -->
                        <div class="col-md-6">
                            <label for="supervisor" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="supervisor" name="supervisor" 
                                   placeholder="Nombre del supervisor" required>
                        </div>
                        
                        <!-- Auxiliar -->
                        <div class="col-md-6">
                            <label for="aux_res" class="form-label">Auxiliar de Respaldo</label>
                            <input type="text" class="form-control" id="aux_res" name="aux_res" 
                                   placeholder="Nombre del auxiliar (opcional)">
                        </div>
                    </div>
                    
                    <!-- Notas adicionales -->
                    <div class="mt-3">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3" 
                                  placeholder="Observaciones o instrucciones especiales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarBitacoraAdmin">
                    <i class="fa fa-save me-2"></i>Guardar Bitácora
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Configurar fecha de inicio como hoy
    $('#fecha_inicio').val(new Date().toISOString().split('T')[0]);
    
    // Configurar fecha fin como 7 días después
    const fechaInicio = new Date();
    const fechaFin = new Date(fechaInicio);
    fechaFin.setDate(fechaFin.getDate() + 7);
    $('#fecha_fin').val(fechaFin.toISOString().split('T')[0]);
    
    // Validar que fecha fin sea mayor que fecha inicio
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        const fechaInicio = new Date($('#fecha_inicio').val());
        const fechaFin = new Date($('#fecha_fin').val());
        
        if (fechaFin <= fechaInicio) {
            $('#fecha_fin').val('');
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inválida',
                text: 'La fecha fin debe ser posterior a la fecha inicio'
            });
        }
    });
    
    // Guardar nueva bitácora
    $('#btnGuardarBitacoraAdmin').click(function() {
        const formData = {
            sucursal_id: $('#sucursal_id').val(),
            area: $('#area').val(),
            semana: $('#semana').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val(),
            responsable: $('#responsable').val(),
            supervisor: $('#supervisor').val(),
            aux_res: $('#aux_res').val(),
            notas: $('#notas').val()
        };
        
        // Validar campos requeridos
        if (!formData.sucursal_id || !formData.area || !formData.semana || 
            !formData.fecha_inicio || !formData.fecha_fin || 
            !formData.responsable || !formData.supervisor) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Por favor complete todos los campos obligatorios'
            });
            return;
        }
        
        // Validar fechas
        const fechaInicio = new Date(formData.fecha_inicio);
        const fechaFin = new Date(formData.fecha_fin);
        
        if (fechaFin <= fechaInicio) {
            Swal.fire({
                icon: 'warning',
                title: 'Fechas inválidas',
                text: 'La fecha fin debe ser posterior a la fecha inicio'
            });
            return;
        }
        
        // Mostrar loading
        Swal.fire({
            title: 'Creando bitácora...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: 'api/crear_bitacora_admin.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    }).then(() => {
                        $('#ModalNuevaBitacoraAdmin').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al crear la bitácora'
                });
            }
        });
    });
    
    // Limpiar formulario al cerrar modal
    $('#ModalNuevaBitacoraAdmin').on('hidden.bs.modal', function() {
        $('#formNuevaBitacoraAdmin')[0].reset();
        $('#fecha_inicio').val(new Date().toISOString().split('T')[0]);
        const fechaInicio = new Date();
        const fechaFin = new Date(fechaInicio);
        fechaFin.setDate(fechaFin.getDate() + 7);
        $('#fecha_fin').val(fechaFin.toISOString().split('T')[0]);
    });
});
</script>
