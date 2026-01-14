<?php
include_once "../db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

$id_historial = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$lote_actual = null;

if ($id_historial > 0) {
    $sql = "SELECT * FROM Historial_Lotes WHERE ID_Historial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_historial);
    $stmt->execute();
    $result = $stmt->get_result();
    $lote_actual = $result->fetch_assoc();
    $stmt->close();
}
?>

<form id="formActualizarLote">
    <input type="hidden" name="id_historial" value="<?php echo $id_historial; ?>">
    
    <div class="mb-3">
        <label class="form-label">Código de Barras:</label>
        <input type="text" class="form-control" id="cod_barra" name="cod_barra" 
               value="<?php echo $lote_actual ? '' : ''; ?>" 
               <?php echo $lote_actual ? 'readonly' : 'required'; ?>>
        <small class="form-text text-muted"><?php echo $lote_actual ? 'Código: ' . htmlspecialchars($lote_actual['Cod_Barra'] ?? 'N/A') : 'Ingrese el código de barras del producto'; ?></small>
    </div>
    
    <?php if ($lote_actual): ?>
    <div class="mb-3">
        <label class="form-label">Producto:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($lote_actual['Nombre_Prod'] ?? 'N/A'); ?>" readonly>
    </div>
    <?php endif; ?>
    
    <div class="mb-3">
        <label class="form-label">Lote:</label>
        <input type="text" class="form-control" id="lote_nuevo" name="lote_nuevo" 
               value="<?php echo $lote_actual ? htmlspecialchars($lote_actual['Lote']) : ''; ?>" 
               required>
        <small class="form-text text-muted">Número o código del lote</small>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Fecha de Caducidad:</label>
        <input type="date" class="form-control" id="fecha_caducidad_nueva" name="fecha_caducidad_nueva" 
               value="<?php echo $lote_actual ? date('Y-m-d', strtotime($lote_actual['Fecha_Caducidad'])) : ''; ?>" 
               required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Cantidad:</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" 
               value="<?php echo $lote_actual ? $lote_actual['Existencias'] : ''; ?>" 
               min="0" required>
        <small class="form-text text-muted">Cantidad de unidades en este lote</small>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Observaciones:</label>
        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i>Guardar
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Si no hay lote actual, permitir búsqueda de producto
    <?php if (!$lote_actual): ?>
    $('#cod_barra').on('blur', function() {
        var codigo = $(this).val();
        if (codigo.length > 0) {
            // Buscar producto y llenar información si existe
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/buscar_producto.php',
                type: 'POST',
                data: { cod_barra: codigo },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.producto) {
                        // Producto encontrado, mostrar información
                        console.log('Producto encontrado:', response.producto);
                    }
                }
            });
        }
    });
    <?php endif; ?>
    
    $('#formActualizarLote').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/actualizar_lote_caducidad.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('¡Éxito!', response.message, 'success').then(() => {
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
            error: function() {
                Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
            }
        });
    });
});
</script>
