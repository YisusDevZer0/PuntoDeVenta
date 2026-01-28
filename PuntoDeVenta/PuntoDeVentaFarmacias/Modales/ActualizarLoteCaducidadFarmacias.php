<?php
include_once __DIR__ . '/../Controladores/db_connect.php';
include_once __DIR__ . '/../Controladores/ControladorUsuario.php';

$id_historial = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$sucursal_modal = isset($_POST['sucursal']) ? (int)$_POST['sucursal'] : (int)($row['Fk_Sucursal'] ?? $row['Fk_sucursal'] ?? 0);
$lote_actual = null;

if ($id_historial > 0) {
    $sql = "SELECT hl.*, sp.Cod_Barra, sp.Nombre_Prod 
            FROM Historial_Lotes hl
            INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS AND hl.Fk_sucursal = sp.Fk_sucursal
            WHERE hl.ID_Historial = ? AND hl.Fk_sucursal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_historial, $sucursal_modal);
    $stmt->execute();
    $res = $stmt->get_result();
    $lote_actual = $res->fetch_assoc();
    $stmt->close();
}
?>
<form id="formActualizarLote">
    <input type="hidden" name="id_historial" id="id_historial" value="<?php echo $id_historial; ?>">
    <input type="hidden" name="id_prod_pos" id="id_prod_pos" value="<?php echo $lote_actual ? $lote_actual['ID_Prod_POS'] : ''; ?>">
    <input type="hidden" name="fk_sucursal" id="fk_sucursal" value="<?php echo $lote_actual ? $lote_actual['Fk_sucursal'] : $sucursal_modal; ?>">
    
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
               value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Nombre_Prod']) : ''; ?>" readonly>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Lote:</label>
            <input type="text" class="form-control" id="lote_nuevo" name="lote_nuevo" 
                   value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Lote']) : ''; ?>" 
                   placeholder="Ej: LOTE-2024-001" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fa-solid fa-calendar-days me-2"></i>Fecha de Caducidad:</label>
            <input type="date" class="form-control" id="fecha_caducidad_nueva" name="fecha_caducidad_nueva" 
                   value="<?php echo $lote_actual ? date('Y-m-d', strtotime($lote_actual['Fecha_Caducidad'])) : ''; ?>" required>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label"><i class="fa-solid fa-boxes-stacked me-2"></i>Cantidad:</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" 
               value="<?php echo $lote_actual ? $lote_actual['Existencias'] : ''; ?>" 
               min="0" step="1" required>
        <small class="form-text text-muted" id="cantidad-ayuda">Cantidad de unidades en este lote</small>
    </div>
    
    <div id="div-lotes-existentes" style="display:none;" class="mb-3">
        <label class="form-label">Lotes existentes del producto:</label>
        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Lote</th><th>Caducidad</th><th>Cantidad</th><th>Días</th></tr></thead>
                <tbody id="tbody-lotes-existentes"></tbody>
            </table>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label"><i class="fa-solid fa-comment me-2"></i>Observaciones:</label>
        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Opcional"></textarea>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="btn-guardar-lote">
            <i class="fa-solid fa-save me-2"></i>Guardar
        </button>
    </div>
</form>

<script>
$(function() {
    var sucursalUsuario = <?php echo json_encode($sucursal_modal); ?>;
    var permiteRegistrar = false;

    $('#btn-buscar-producto').on('click', buscarProducto);
    $('#cod_barra').on('keypress', function(e) {
        if (e.which === 13) { e.preventDefault(); buscarProducto(); }
    });

    function buscarProducto() {
        var codigo = $('#cod_barra').val().trim();
        if (!codigo) {
            Swal.fire('Atención', 'Ingrese un código de barras', 'warning');
            return;
        }
        $('#info-producto').html('<i class="fa-solid fa-spinner fa-spin"></i> Buscando...');
        $.ajax({
            url: 'api/buscar_producto_registrar_lote.php',
            type: 'GET',
            data: { codigo: codigo, sucursal: sucursalUsuario },
            dataType: 'json',
            success: function(r) {
                if (!r.success || !r.producto) {
                    $('#info-producto').html('<span class="text-danger">' + (r.error || 'Producto no encontrado') + '</span>');
                    $('#div-info-producto').hide();
                    permiteRegistrar = false;
                    $('#btn-guardar-lote').prop('disabled', true);
                    return;
                }
                var p = r.producto;
                $('#id_prod_pos').val(p.ID_Prod_POS);
                $('#fk_sucursal').val(p.Fk_sucursal);
                $('#nombre_producto').val(p.Nombre_Prod || p.nombre_producto);
                $('#div-info-producto').show();
                permiteRegistrar = !!p.permite_registrar_lote;
                var sinCubrir = p.sin_cubrir || 0;
                var msg = 'Stock: ' + (p.existencia_total || p.Existencias_R) + ' | En lotes: ' + (p.en_lotes || p.Total_Lotes) + ' | Sin cubrir: ' + sinCubrir;
                if (permiteRegistrar) {
                    msg += ' <span class="text-success"><strong>Puede registrar.</strong></span>';
                    $('#cantidad').prop('max', sinCubrir).prop('disabled', false);
                    $('#btn-guardar-lote').prop('disabled', false);
                } else {
                    msg += ' <span class="text-danger"><strong>Todo cubierto. No se permiten más altas.</strong></span>';
                    $('#cantidad').removeAttr('max').prop('disabled', true);
                    $('#btn-guardar-lote').prop('disabled', true);
                }
                $('#info-producto').html('<i class="fa-solid fa-check text-success"></i> ' + msg);
                if (p.lotes && p.lotes.length) {
                    var html = '';
                    p.lotes.forEach(function(l) {
                        var badge = l.Dias_restantes < 0 ? 'danger' : (l.Dias_restantes <= 15 ? 'warning' : 'success');
                        html += '<tr><td>' + (l.Lote||'') + '</td><td>' + (l.Fecha_Caducidad ? new Date(l.Fecha_Caducidad).toLocaleDateString('es-MX') : '') + '</td><td>' + (l.Existencias||0) + '</td><td><span class="badge bg-' + badge + '">' + (l.Dias_restantes < 0 ? 'Vencido' : (l.Dias_restantes + ' días')) + '</span></td></tr>';
                    });
                    $('#tbody-lotes-existentes').html(html);
                    $('#div-lotes-existentes').show();
                } else {
                    $('#div-lotes-existentes').hide();
                }
            },
            error: function() {
                $('#info-producto').html('<span class="text-danger">Error al buscar</span>');
                $('#div-info-producto').hide();
                permiteRegistrar = false;
                $('#btn-guardar-lote').prop('disabled', true);
            }
        });
    }

    $('#formActualizarLote').on('submit', function(e) {
        e.preventDefault();
        if (!$('#id_historial').val() && !$('#id_prod_pos').val()) {
            Swal.fire('Atención', 'Debe buscar un producto primero', 'warning');
            return;
        }
        if (!$('#id_historial').val() && !permiteRegistrar) {
            Swal.fire('Atención', 'Todo el stock tiene lote y caducidad. No se permiten más altas.', 'warning');
            return;
        }
        var formData = $(this).serialize();
        Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
        $.ajax({
            url: 'api/actualizar_lote_caducidad_farmacias.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(r) {
                Swal.close();
                if (r.success) {
                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: r.message || 'Guardado.', timer: 2000, showConfirmButton: false }).then(function() {
                        $('#ModalEdDele').modal('hide');
                        if (typeof CargarLotesCaducidadesFarmacias === 'function') CargarLotesCaducidadesFarmacias();
                        else location.reload();
                    });
                } else {
                    Swal.fire('Error', r.error || r.message || 'Error al guardar', 'error');
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
