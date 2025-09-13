<?php
include_once "../Controladores/db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

if (empty($folio) || empty($codigo)) {
    echo '<div class="alert alert-danger">Faltan parámetros requeridos</div>';
    exit;
}

// Obtener datos del conteo
$sql = "SELECT 
    cd.*,
    s.Nombre_Sucursal
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
WHERE cd.Folio_Ingreso = ? AND cd.Cod_Barra = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        ?>
        <form id="formEditarConteo">
            <input type="hidden" name="folio" value="<?php echo htmlspecialchars($row['Folio_Ingreso']); ?>">
            <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['Cod_Barra']); ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Folio de Ingreso:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['Folio_Ingreso']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Código de Barras:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['Cod_Barra']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Producto:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['Nombre_Producto']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Sucursal:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['Nombre_Sucursal']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Existencia Real:</label>
                        <input type="number" class="form-control" value="<?php echo $row['Existencias_R']; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Existencia Física: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="existenciaFisica" 
                               value="<?php echo $row['ExistenciaFisica'] ?? ''; ?>" required>
                        <div class="form-text">Ingrese la cantidad física contada</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Estado Actual:</label>
                        <select class="form-select" name="estado">
                            <option value="0" <?php echo $row['EnPausa'] == 0 ? 'selected' : ''; ?>>Completado</option>
                            <option value="1" <?php echo $row['EnPausa'] == 1 ? 'selected' : ''; ?>>En Pausa</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Agregado Por:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['AgregadoPor']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Observaciones:</label>
                <textarea class="form-control" name="observaciones" rows="3" placeholder="Ingrese observaciones sobre el conteo..."></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </form>
        
        <script>
        $(document).ready(function() {
            $('#formEditarConteo').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.post('https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ActualizarConteo.php', 
                    formData, 
                    function(data) {
                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                $('#ModalEdDele').modal('hide');
                                CargarConteosDiarios();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    }, 'json')
                    .fail(function() {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });
            });
        });
        </script>
        <?php
    } else {
        echo '<div class="alert alert-danger">No se encontró el conteo especificado</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta: ' . $conn->error . '</div>';
}

$conn->close();
?>
