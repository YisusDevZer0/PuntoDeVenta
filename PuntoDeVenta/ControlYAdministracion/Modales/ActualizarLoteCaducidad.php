<?php
include_once "../db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

$id_historial = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$lote_actual = null;

if ($id_historial > 0) {
    $sql = "SELECT hl.*, sp.Cod_Barra, sp.Nombre_Prod 
            FROM Historial_Lotes hl
            INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS 
                AND hl.Fk_sucursal = sp.Fk_sucursal
            WHERE hl.ID_Historial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_historial);
    $stmt->execute();
    $result = $stmt->get_result();
    $lote_actual = $result->fetch_assoc();
    $stmt->close();
}
?>

<form id="formActualizarLote">
    <input type="hidden" name="id_historial" id="id_historial" value="<?php echo $id_historial; ?>">
    <input type="hidden" name="id_prod_pos" id="id_prod_pos" value="<?php echo $lote_actual ? $lote_actual['ID_Prod_POS'] : ''; ?>">
    <input type="hidden" name="fk_sucursal" id="fk_sucursal" value="<?php echo $lote_actual ? $lote_actual['Fk_sucursal'] : ''; ?>">
    
    <div class="mb-3">
        <label class="form-label"><i class="fa-solid fa-barcode me-2"></i>Código de Barras:</label>
        <div class="input-group">
            <input type="text" class="form-control" id="cod_barra" name="cod_barra" 
                   value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Cod_Barra']) : ''; ?>" 
                   placeholder="Escanee o ingrese código" 
                   <?php echo $lote_actual ? 'readonly' : 'required'; ?>>
            <?php if (!$lote_actual): ?>
            <button class="btn btn-outline-primary" type="button" id="btn-buscar-producto">
                <i class="fa-solid fa-search"></i> Buscar
            </button>
            <?php endif; ?>
        </div>
        <small class="form-text text-muted" id="info-producto"></small>
    </div>
    
    <div class="mb-3" id="div-info-producto" style="<?php echo $lote_actual ? '' : 'display:none;'; ?>">
        <label class="form-label">Producto:</label>
        <input type="text" class="form-control" id="nombre_producto" 
               value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Nombre_Prod']) : ''; ?>" 
               readonly>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Lote:</label>
            <input type="text" class="form-control" id="lote_nuevo" name="lote_nuevo" 
                   value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Lote']) : ''; ?>" 
                   placeholder="Ej: LOTE-2024-001" required>
            <small class="form-text text-muted">Número o código del lote</small>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fa-solid fa-calendar-days me-2"></i>Fecha de Caducidad:</label>
            <input type="date" class="form-control" id="fecha_caducidad_nueva" name="fecha_caducidad_nueva" 
                   value="<?php echo $lote_actual ? date('Y-m-d', strtotime($lote_actual['Fecha_Caducidad'])) : ''; ?>" 
                   required>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label"><i class="fa-solid fa-boxes-stacked me-2"></i>Cantidad:</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" 
               value="<?php echo $lote_actual ? $lote_actual['Existencias'] : ''; ?>" 
               min="0" step="1" required>
        <small class="form-text text-muted">Cantidad de unidades en este lote</small>
    </div>
    
    <div id="div-lotes-existentes" style="display:none;" class="mb-3">
        <label class="form-label">Lotes existentes del producto:</label>
        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Caducidad</th>
                        <th>Cantidad</th>
                        <th>Días</th>
                    </tr>
                </thead>
                <tbody id="tbody-lotes-existentes">
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label"><i class="fa-solid fa-comment me-2"></i>Observaciones:</label>
        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" 
                  placeholder="Notas adicionales (opcional)"><?php echo $lote_actual ? '' : ''; ?></textarea>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i>Guardar
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Buscar producto por código de barras
    $('#btn-buscar-producto').on('click', function() {
        buscarProducto();
    });
    
    $('#cod_barra').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarProducto();
        }
    });
    
    function buscarProducto() {
        var codigo = $('#cod_barra').val().trim();
        
        if (!codigo) {
            Swal.fire('Atención', 'Ingrese un código de barras', 'warning');
            return;
        }
        
        $.ajax({
            url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/buscar_producto.php',
            type: 'POST',
            data: { cod_barra: codigo },
            dataType: 'json',
            beforeSend: function() {
                $('#info-producto').html('<i class="fa-solid fa-spinner fa-spin"></i> Buscando producto...');
            },
            success: function(response) {
                if (response.success && response.producto) {
                    var producto = response.producto;
                    $('#id_prod_pos').val(producto.ID_Prod_POS);
                    $('#fk_sucursal').val(producto.Fk_sucursal);
                    $('#nombre_producto').val(producto.Nombre_Prod);
                    $('#div-info-producto').show();
                    var controlLotes = producto.Control_Lotes_Caducidad == 1 ? 
                        '<span class="badge bg-success ms-2">Requiere Control de Lotes</span>' : 
                        '<span class="badge bg-secondary ms-2">No Requiere Control de Lotes</span>';
                    
                    $('#info-producto').html(
                        '<i class="fa-solid fa-check text-success"></i> ' +
                        'Producto encontrado - Stock actual: ' + producto.Existencias_R + 
                        ' | Total en lotes: ' + producto.Total_Lotes + controlLotes
                    );
                    
                    // Mostrar lotes existentes
                    if (producto.lotes && producto.lotes.length > 0) {
                        var html = '';
                        producto.lotes.forEach(function(lote) {
                            var badgeClass = lote.Dias_restantes < 0 ? 'danger' : 
                                           (lote.Dias_restantes <= 15 ? 'warning' : 'success');
                            html += '<tr>' +
                                '<td>' + lote.Lote + '</td>' +
                                '<td>' + new Date(lote.Fecha_Caducidad).toLocaleDateString('es-MX') + '</td>' +
                                '<td>' + lote.Existencias + '</td>' +
                                '<td><span class="badge bg-' + badgeClass + '">' + 
                                    (lote.Dias_restantes < 0 ? 'Vencido' : lote.Dias_restantes + ' días') +
                                '</span></td>' +
                                '</tr>';
                        });
                        $('#tbody-lotes-existentes').html(html);
                        $('#div-lotes-existentes').show();
                    } else {
                        $('#div-lotes-existentes').hide();
                    }
                } else {
                    Swal.fire('Error', response.message || 'Producto no encontrado', 'error');
                    $('#info-producto').html('<i class="fa-solid fa-times text-danger"></i> Producto no encontrado');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                $('#info-producto').html('<i class="fa-solid fa-times text-danger"></i> Error en la búsqueda');
            }
        });
    }
    
    // Validación de fecha de caducidad
    $('#fecha_caducidad_nueva').on('change', function() {
        var fecha = new Date($(this).val());
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fecha < hoy) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha de caducidad vencida',
                text: 'La fecha ingresada ya ha pasado. ¿Desea continuar?',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Corregir'
            }).then((result) => {
                if (!result.isConfirmed) {
                    $(this).focus();
                }
            });
        }
    });
    
    // Enviar formulario
    $('#formActualizarLote').on('submit', function(e) {
        e.preventDefault();
        
        // Validar que se haya buscado el producto si es nuevo
        if (!$('#id_historial').val() && !$('#id_prod_pos').val()) {
            Swal.fire('Atención', 'Debe buscar un producto primero', 'warning');
            $('#cod_barra').focus();
            return;
        }
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/actualizar_lote_caducidad.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#ModalEdDele').modal('hide');
                        if (typeof CargarLotesCaducidades === 'function') {
                            CargarLotesCaducidades();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                var errorMsg = 'Error al comunicarse con el servidor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    });
});
</script>
