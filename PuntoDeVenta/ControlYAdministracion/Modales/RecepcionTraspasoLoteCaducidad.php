<?php
include_once __DIR__ . '/../Controladores/db_connect.php';
include_once __DIR__ . '/../Controladores/ControladorUsuario.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$traspaso = null;

if ($id > 0 && isset($conn) && $conn) {
    $stmt = $conn->prepare("
        SELECT tyc.TraspaNotID, tyc.Cod_Barra, tyc.Nombre_Prod, tyc.Cantidad,
               tyc.Fk_SucursalDestino, tyc.Folio_Ticket, tyc.Fk_sucursal
        FROM TraspasosYNotasC tyc
        WHERE tyc.TraspaNotID = ? AND tyc.Estatus = 'Generado'
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $traspaso = $res->fetch_assoc();
    $stmt->close();
}
?>

<?php if ($traspaso): ?>
<form id="formRecepcionTraspasoLote">
    <input type="hidden" name="id_traspaso" value="<?php echo (int) $traspaso['TraspaNotID']; ?>">
    <input type="hidden" name="fk_sucursal" value="<?php echo (int) $traspaso['Fk_SucursalDestino']; ?>">

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label"><i class="fa-solid fa-barcode me-2"></i>Código de barras</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($traspaso['Cod_Barra']); ?>" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label">Producto</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($traspaso['Nombre_Prod']); ?>" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Cantidad enviada</label>
            <input type="number" class="form-control" id="cantidad_enviada" value="<?php echo (int) $traspaso['Cantidad']; ?>" readonly>
        </div>
        <div class="col-md-4" id="campo_cantidad_total_normal">
            <label class="form-label">Cantidad recibida TOTAL <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="cantidad_recibida" id="cantidad_recibida" 
                   value="<?php echo (int) $traspaso['Cantidad']; ?>" min="1" required>
            <small class="text-muted">Ingrese el total recibido</small>
        </div>
        <div class="col-md-4" id="campo_cantidad_total_calculada" style="display: none;">
            <label class="form-label">Cantidad recibida TOTAL <span class="text-danger">*</span></label>
            <input type="number" class="form-control bg-light" id="cantidad_recibida_calculada" readonly>
            <small class="text-muted">Se calcula automáticamente sumando ambos lotes</small>
            <input type="hidden" name="cantidad_recibida" id="cantidad_recibida_hidden">
        </div>
        <div class="col-md-4">
            <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Lote principal <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="lote" id="lote" placeholder="Ej. LOTE-2024-001" required>
        </div>
    </div>

    <!-- Sección para diferencia de cantidad -->
    <div id="seccion_diferencia" class="alert alert-warning mb-3" style="display: none;">
        <strong><i class="fa-solid fa-exclamation-triangle me-2"></i>Diferencia detectada:</strong>
        <p class="mb-2">La cantidad recibida es diferente a la enviada. Por favor, indique el motivo:</p>
        <div class="row">
            <div class="col-md-12 mb-2">
                <label class="form-label">Motivo de la diferencia <span class="text-danger">*</span></label>
                <select class="form-select" name="motivo_diferencia" id="motivo_diferencia">
                    <option value="">Seleccione un motivo...</option>
                    <option value="otro_lote">Es por otro lote (se recibió en diferentes lotes)</option>
                    <option value="no_completo">No llegó completo</option>
                    <option value="otra_razon">Otra razón</option>
                </select>
            </div>
        </div>
        
        <!-- Campos adicionales para otro lote -->
        <div id="campos_otro_lote" style="display: none;" class="mt-3 p-3 bg-light rounded">
            <h6 class="mb-3"><i class="fa-solid fa-boxes me-2"></i>Distribución por lotes</h6>
            <p class="text-muted mb-3">Ingrese las cantidades recibidas de cada lote. El total se calculará automáticamente.</p>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Cantidad del lote principal <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="cantidad_lote_principal" id="cantidad_lote_principal" min="1" placeholder="Ej. 1">
                    <small class="text-muted">Cantidad recibida del lote: <strong id="nombre_lote_principal">-</strong></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Cantidad del lote adicional <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="cantidad_lote_adicional" id="cantidad_lote_adicional" min="1" placeholder="Ej. 1">
                    <small class="text-muted">Cantidad recibida del lote adicional</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Lote adicional <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lote_adicional" id="lote_adicional" placeholder="Ej. LOTE-2024-002">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de caducidad adicional <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_caducidad_adicional" id="fecha_caducidad_adicional">
                </div>
            </div>
            
            <div class="alert alert-info mb-0">
                <strong><i class="fa-solid fa-calculator me-2"></i>Resumen:</strong><br>
                • Cantidad lote principal: <span id="info_cantidad_principal" class="fw-bold">0</span><br>
                • Cantidad lote adicional: <span id="info_cantidad_adicional" class="fw-bold">0</span><br>
                • <strong>Cantidad total recibida (calculada): <span id="info_cantidad_total" class="fw-bold text-primary">0</span></strong>
            </div>
        </div>
        
        <!-- Mensaje para otra razón -->
        <div id="mensaje_otra_razon" style="display: none;" class="mt-2">
            <div class="alert alert-info mb-0">
                <i class="fa-solid fa-info-circle me-2"></i>
                Por favor, detalle la razón en el campo de <strong>Observaciones</strong> al final del formulario.
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label"><i class="fa-solid fa-calendar-days me-2"></i>Fecha de caducidad <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="fecha_caducidad" id="fecha_caducidad" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><i class="fa-solid fa-comment me-2"></i>Observaciones</label>
            <textarea class="form-control" name="observaciones" id="observaciones" rows="2" placeholder="Opcional"></textarea>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="btnGuardarRecepcion">
            <i class="fa-solid fa-check me-2"></i>Recibir y registrar
        </button>
    </div>
</form>

<script>
$(function() {
    var cantidadEnviada = parseInt($('#cantidad_enviada').val(), 10) || 1;
    var $cantidadRecibida = $('#cantidad_recibida');
    var $seccionDiferencia = $('#seccion_diferencia');
    var $motivoDiferencia = $('#motivo_diferencia');
    var $camposOtroLote = $('#campos_otro_lote');
    var $mensajeOtraRazon = $('#mensaje_otra_razon');
    
    // Función para actualizar información de distribución (nueva lógica: suma de lotes)
    function actualizarDistribucion() {
        var cantidadPrincipal = parseInt($('#cantidad_lote_principal').val(), 10) || 0;
        var cantidadAdicional = parseInt($('#cantidad_lote_adicional').val(), 10) || 0;
        var cantidadTotal = cantidadPrincipal + cantidadAdicional;
        
        $('#info_cantidad_principal').text(cantidadPrincipal);
        $('#info_cantidad_adicional').text(cantidadAdicional);
        $('#info_cantidad_total').text(cantidadTotal);
        
        // Actualizar campo oculto y campo visible calculado
        $('#cantidad_recibida_hidden').val(cantidadTotal);
        $('#cantidad_recibida_calculada').val(cantidadTotal);
        
        // Validar que ambas cantidades sean mayores a 0
        if (cantidadPrincipal > 0 && cantidadAdicional > 0) {
            $('#cantidad_lote_principal').removeClass('is-invalid');
            $('#cantidad_lote_adicional').removeClass('is-invalid');
        } else {
            if (cantidadPrincipal <= 0) {
                $('#cantidad_lote_principal').addClass('is-invalid');
            }
            if (cantidadAdicional <= 0) {
                $('#cantidad_lote_adicional').addClass('is-invalid');
            }
        }
    }
    
    // Actualizar nombre del lote principal cuando cambia
    $('#lote').on('change keyup', function() {
        $('#nombre_lote_principal').text($(this).val() || '-');
    });
    $('#nombre_lote_principal').text($('#lote').val() || '-');
    
    // Detectar cambios en cantidad recibida (solo cuando NO es otro lote)
    $cantidadRecibida.on('change keyup', function() {
        if ($motivoDiferencia.val() === 'otro_lote') {
            // Si es otro lote, no usar este campo, usar los campos individuales
            return;
        }
        
        var cantidadRecibida = parseInt($(this).val(), 10) || 0;
        
        // Mostrar sección de diferencia solo si hay diferencia Y no es otro lote
        if (cantidadRecibida !== cantidadEnviada && cantidadRecibida > 0 && $motivoDiferencia.val() !== 'otro_lote') {
            $seccionDiferencia.show();
        } else if ($motivoDiferencia.val() !== 'otro_lote') {
            $seccionDiferencia.hide();
            $motivoDiferencia.val('');
            $camposOtroLote.hide();
            $mensajeOtraRazon.hide();
            // Limpiar campos de otro lote
            $('#lote_adicional').val('').removeAttr('required');
            $('#fecha_caducidad_adicional').val('').removeAttr('required');
            $('#cantidad_lote_adicional').val('').removeAttr('required');
            $('#cantidad_lote_principal').val('').removeAttr('required');
        }
    });
    
    // Cuando cambian las cantidades individuales de lotes
    $('#cantidad_lote_principal').on('change keyup', function() {
        if ($motivoDiferencia.val() === 'otro_lote') {
            actualizarDistribucion();
        }
    });
    
    $('#cantidad_lote_adicional').on('change keyup', function() {
        if ($motivoDiferencia.val() === 'otro_lote') {
            actualizarDistribucion();
        }
    });
    
    // Manejar cambio de motivo
    $motivoDiferencia.on('change', function() {
        var motivo = $(this).val();
        $camposOtroLote.hide();
        $mensajeOtraRazon.hide();
        
        // Limpiar campos
        $('#lote_adicional').val('').removeAttr('required');
        $('#fecha_caducidad_adicional').val('').removeAttr('required');
        $('#cantidad_lote_adicional').val('').removeAttr('required').removeClass('is-invalid');
        $('#cantidad_lote_principal').val('').removeAttr('required').removeClass('is-invalid');
        
        if (motivo === 'otro_lote') {
            $camposOtroLote.show();
            // Ocultar campo de cantidad total normal y mostrar el calculado
            $('#campo_cantidad_total_normal').hide();
            $('#campo_cantidad_total_calculada').show();
            // Hacer requeridos los campos de cantidades individuales
            $('#cantidad_lote_principal').attr('required', 'required');
            $('#lote_adicional').attr('required', 'required');
            $('#fecha_caducidad_adicional').attr('required', 'required');
            $('#cantidad_lote_adicional').attr('required', 'required');
            // El campo de cantidad recibida ya no es requerido directamente
            $('#cantidad_recibida').removeAttr('required');
            actualizarDistribucion();
        } else if (motivo === 'no_completo' || motivo === 'otra_razon') {
            // Si no es otro lote, mostrar campo normal y ocultar calculado
            $('#campo_cantidad_total_normal').show();
            $('#campo_cantidad_total_calculada').hide();
            $('#cantidad_recibida').attr('required', 'required');
            $('#cantidad_lote_principal').removeAttr('required');
            $('#cantidad_lote_adicional').removeAttr('required');
        }
        
        if (motivo === 'no_completo') {
            // Agregar automáticamente a observaciones
            var obsActual = $('#observaciones').val();
            var nuevaObs = 'No llegó completo. Cantidad enviada: ' + cantidadEnviada + ', cantidad recibida: ' + parseInt($cantidadRecibida.val(), 10);
            if (obsActual && !obsActual.includes('No llegó completo')) {
                $('#observaciones').val(obsActual + '\n' + nuevaObs);
            } else if (!obsActual) {
                $('#observaciones').val(nuevaObs);
            }
        } else if (motivo === 'otra_razon') {
            $mensajeOtraRazon.show();
        }
    });
    
    // Validar y actualizar distribución cuando es otro lote
    $('#cantidad_lote_adicional').on('change keyup', function() {
        actualizarDistribucion();
        
        var cantidadTotal = parseInt($cantidadRecibida.val(), 10) || 0;
        var cantidadAdicional = parseInt($(this).val(), 10) || 0;
        var cantidadPrincipal = cantidadTotal - cantidadAdicional;
        
        if (cantidadAdicional > cantidadTotal) {
            Swal.fire('Atención', 'La cantidad del lote adicional no puede ser mayor a la cantidad recibida total.', 'warning');
            $(this).val('');
            actualizarDistribucion();
            return;
        }
        
        if (cantidadPrincipal <= 0) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('#formRecepcionTraspasoLote').on('submit', function(e) {
        e.preventDefault();
        var cant = parseInt($('#cantidad_recibida').val(), 10);
        
        if (cant < 1) {
            Swal.fire('Atención', 'La cantidad recibida debe ser mayor a 0', 'warning');
            return;
        }
        
        // Validar diferencia de cantidad (solo si NO es otro lote)
        var motivo = $motivoDiferencia.val();
        if (motivo === 'otro_lote') {
            // Para otro lote, la cantidad ya se calculó arriba
            // No validamos diferencia aquí porque puede ser igual o diferente
        } else if (cant !== cantidadEnviada) {
            if (!motivo) {
                Swal.fire('Atención', 'Debe seleccionar el motivo de la diferencia en la cantidad', 'warning');
                return;
            }
            
            if (motivo === 'otro_lote') {
                var cantidadPrincipal = parseInt($('#cantidad_lote_principal').val(), 10) || 0;
                var cantidadAdicional = parseInt($('#cantidad_lote_adicional').val(), 10) || 0;
                var loteAdicional = $('#lote_adicional').val().trim();
                var fechaAdicional = $('#fecha_caducidad_adicional').val();
                
                if (cantidadPrincipal < 1) {
                    Swal.fire('Atención', 'Debe ingresar la cantidad del lote principal', 'warning');
                    return;
                }
                
                if (cantidadAdicional < 1) {
                    Swal.fire('Atención', 'Debe ingresar la cantidad del lote adicional', 'warning');
                    return;
                }
                
                if (!loteAdicional || !fechaAdicional) {
                    Swal.fire('Atención', 'Debe completar todos los campos del lote adicional', 'warning');
                    return;
                }
                
                // Validar que los lotes sean diferentes
                if ($('#lote').val().trim() === loteAdicional) {
                    Swal.fire('Atención', 'El lote adicional debe ser diferente al lote principal', 'warning');
                    return;
                }
                
                // Actualizar cantidad recibida total (suma de ambos)
                cant = cantidadPrincipal + cantidadAdicional;
            }
        }
        
        if (!$('#lote').val().trim()) {
            Swal.fire('Atención', 'Ingrese el lote', 'warning');
            return;
        }
        if (!$('#fecha_caducidad').val()) {
            Swal.fire('Atención', 'Ingrese la fecha de caducidad', 'warning');
            return;
        }

        Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
        $.ajax({
            url: 'api/recepcion_traspaso_lote_caducidad.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(r) {
                Swal.close();
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Listo', text: r.message || 'Traspaso recibido correctamente.', timer: 2000, showConfirmButton: false })
                        .then(function() {
                            $('#ModalRecepcionTraspaso').modal('hide');
                            if (typeof CargarRecepcionTraspasos === 'function') CargarRecepcionTraspasos();
                            else location.reload();
                        });
                } else {
                    Swal.fire('Error', r.error || r.message || 'Error al recibir', 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                var msg = (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message)) || 'Error de conexión';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
<?php else: ?>
<p class="alert alert-danger mb-0">Traspaso no encontrado o ya fue recibido.</p>
<?php endif; ?>
