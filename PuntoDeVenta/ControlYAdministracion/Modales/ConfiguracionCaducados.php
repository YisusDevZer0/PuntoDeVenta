<!-- Modal para Configuración de Caducados -->
<div class="modal fade" id="modalConfiguracionCaducados" tabindex="-1" aria-labelledby="modalConfiguracionCaducadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfiguracionCaducadosLabel">
                    <i class="fa-solid fa-cog me-2"></i>Configuración de Alertas de Caducidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formConfiguracionCaducados">
                    <input type="hidden" id="idConfiguracion" name="idConfiguracion">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sucursalConfig" class="form-label">Sucursal *</label>
                                <select class="form-select" id="sucursalConfig" name="sucursalConfig" required>
                                    <option value="">Seleccionar sucursal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notificacionesActivas" name="notificacionesActivas">
                                    <label class="form-check-label" for="notificacionesActivas">
                                        Notificaciones Activas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6><i class="fa-solid fa-bell me-2"></i>Configuración de Alertas</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-exclamation-triangle me-2"></i>Alerta 3 Meses</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="diasAlerta3Meses" class="form-label">Días antes de caducar</label>
                                        <input type="number" class="form-control" id="diasAlerta3Meses" name="diasAlerta3Meses" min="1" max="365" value="90">
                                        <div class="form-text">Días antes de la fecha de caducidad</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-exclamation-circle me-2"></i>Alerta 6 Meses</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="diasAlerta6Meses" class="form-label">Días antes de caducar</label>
                                        <input type="number" class="form-control" id="diasAlerta6Meses" name="diasAlerta6Meses" min="1" max="365" value="180">
                                        <div class="form-text">Días antes de la fecha de caducidad</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Alerta 9 Meses</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="diasAlerta9Meses" class="form-label">Días antes de caducar</label>
                                        <input type="number" class="form-control" id="diasAlerta9Meses" name="diasAlerta9Meses" min="1" max="365" value="270">
                                        <div class="form-text">Días antes de la fecha de caducidad</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6><i class="fa-solid fa-user me-2"></i>Contacto Responsable</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="emailResponsable" class="form-label">Email Responsable</label>
                                <input type="email" class="form-control" id="emailResponsable" name="emailResponsable" placeholder="responsable@doctorpez.mx">
                                <div class="form-text">Email para recibir notificaciones</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefonoWhatsapp" class="form-label">Teléfono WhatsApp</label>
                                <input type="tel" class="form-control" id="telefonoWhatsapp" name="telefonoWhatsapp" placeholder="+52 55 1234 5678">
                                <div class="form-text">Número para notificaciones por WhatsApp</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa de la configuración -->
                    <div class="alert alert-info">
                        <h6><i class="fa-solid fa-eye me-2"></i>Vista Previa de la Configuración</h6>
                        <div id="vistaPreviaConfiguracion">
                            <!-- Se actualizará dinámicamente -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">
                    <i class="fa-solid fa-save me-1"></i>Guardar Configuración
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalConfiguracion() {
    // Cargar sucursales
    cargarSucursalesConfiguracion();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfiguracionCaducados'));
    modal.show();
}

function cargarSucursalesConfiguracion() {
    fetch('api/obtener_sucursales.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sucursalConfig');
            select.innerHTML = '<option value="">Seleccionar sucursal</option>';
            
            data.sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.id;
                option.textContent = sucursal.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
}

function actualizarVistaPrevia() {
    const sucursal = document.getElementById('sucursalConfig').value;
    const notificaciones = document.getElementById('notificacionesActivas').checked;
    const dias3 = document.getElementById('diasAlerta3Meses').value;
    const dias6 = document.getElementById('diasAlerta6Meses').value;
    const dias9 = document.getElementById('diasAlerta9Meses').value;
    const email = document.getElementById('emailResponsable').value;
    const telefono = document.getElementById('telefonoWhatsapp').value;
    
    if (sucursal) {
        const sucursalNombre = document.getElementById('sucursalConfig').options[document.getElementById('sucursalConfig').selectedIndex].text;
        
        document.getElementById('vistaPreviaConfiguracion').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Sucursal:</strong> ${sucursalNombre}<br>
                    <strong>Notificaciones:</strong> ${notificaciones ? 'Activas' : 'Inactivas'}<br>
                    <strong>Email:</strong> ${email || 'No configurado'}
                </div>
                <div class="col-md-6">
                    <strong>WhatsApp:</strong> ${telefono || 'No configurado'}<br>
                    <strong>Alertas:</strong><br>
                    • 3 meses: ${dias3} días antes<br>
                    • 6 meses: ${dias6} días antes<br>
                    • 9 meses: ${dias9} días antes
                </div>
            </div>
        `;
    } else {
        document.getElementById('vistaPreviaConfiguracion').innerHTML = `
            <div class="text-muted">Selecciona una sucursal para ver la vista previa</div>
        `;
    }
}

// Event listeners para actualizar vista previa
document.getElementById('sucursalConfig').addEventListener('change', actualizarVistaPrevia);
document.getElementById('notificacionesActivas').addEventListener('change', actualizarVistaPrevia);
document.getElementById('diasAlerta3Meses').addEventListener('input', actualizarVistaPrevia);
document.getElementById('diasAlerta6Meses').addEventListener('input', actualizarVistaPrevia);
document.getElementById('diasAlerta9Meses').addEventListener('input', actualizarVistaPrevia);
document.getElementById('emailResponsable').addEventListener('input', actualizarVistaPrevia);
document.getElementById('telefonoWhatsapp').addEventListener('input', actualizarVistaPrevia);

function guardarConfiguracion() {
    const form = document.getElementById('formConfiguracionCaducados');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    if (!formData.get('sucursalConfig')) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Por favor selecciona una sucursal'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando configuración...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const datos = {
        sucursal_id: formData.get('sucursalConfig'),
        dias_alerta_3_meses: formData.get('diasAlerta3Meses'),
        dias_alerta_6_meses: formData.get('diasAlerta6Meses'),
        dias_alerta_9_meses: formData.get('diasAlerta9Meses'),
        notificaciones_activas: formData.get('notificacionesActivas') ? 1 : 0,
        email_responsable: formData.get('emailResponsable'),
        telefono_whatsapp: formData.get('telefonoWhatsapp')
    };
    
    fetch('api/configurar_alertas.php', {
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
                title: 'Configuración guardada',
                text: data.message
            }).then(() => {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalConfiguracionCaducados')).hide();
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
            text: 'Error al guardar la configuración: ' + error.message
        });
    });
}
</script>
