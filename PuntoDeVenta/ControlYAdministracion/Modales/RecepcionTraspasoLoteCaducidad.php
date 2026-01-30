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
        <div class="col-md-4">
            <label class="form-label">Cantidad recibida <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="cantidad_recibida" id="cantidad_recibida" 
                   value="<?php echo (int) $traspaso['Cantidad']; ?>" min="1" required>
        </div>
        <div class="col-md-4">
            <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Lote <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="lote" id="lote" placeholder="Ej. LOTE-2024-001" required>
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
    var maxRecibir = parseInt($('#cantidad_enviada').val(), 10) || 1;
    $('#cantidad_recibida').attr('max', maxRecibir);

    $('#formRecepcionTraspasoLote').on('submit', function(e) {
        e.preventDefault();
        var cant = parseInt($('#cantidad_recibida').val(), 10);
        if (cant < 1 || cant > maxRecibir) {
            Swal.fire('Atención', 'La cantidad recibida debe estar entre 1 y ' + maxRecibir, 'warning');
            return;
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
