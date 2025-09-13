<?php
include_once "../Controladores/db_connect.php";

// Obtener sucursales para el select
$sqlSucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
$resultSucursales = $conn->query($sqlSucursales);

// Obtener productos para el select
$sqlProductos = "SELECT DISTINCT Cod_Barra, Nombre_Producto FROM ConteosDiarios ORDER BY Nombre_Producto";
$resultProductos = $conn->query($sqlProductos);
?>

<form id="formNuevoConteo">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Sucursal: <span class="text-danger">*</span></label>
                <select class="form-select" name="sucursal" required>
                    <option value="">Seleccione una sucursal</option>
                    <?php
                    if ($resultSucursales && $resultSucursales->num_rows > 0) {
                        while ($row = $resultSucursales->fetch_assoc()) {
                            echo '<option value="' . $row['ID_Sucursal'] . '">' . htmlspecialchars($row['Nombre_Sucursal']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Producto: <span class="text-danger">*</span></label>
                <select class="form-select" name="producto" id="selectProducto" required>
                    <option value="">Seleccione un producto</option>
                    <?php
                    if ($resultProductos && $resultProductos->num_rows > 0) {
                        while ($row = $resultProductos->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['Cod_Barra']) . '" data-nombre="' . htmlspecialchars($row['Nombre_Producto']) . '">' . htmlspecialchars($row['Nombre_Producto']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Código de Barras:</label>
                <input type="text" class="form-control" id="codigoBarras" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Existencia Real:</label>
                <input type="number" class="form-control" id="existenciaReal" readonly>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Existencia Física: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="existenciaFisica" required min="0">
                <div class="form-text">Ingrese la cantidad física contada</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Folio de Ingreso:</label>
                <input type="text" class="form-control" name="folioIngreso" placeholder="Se generará automáticamente" readonly>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Observaciones:</label>
        <textarea class="form-control" name="observaciones" rows="3" placeholder="Ingrese observaciones sobre el conteo..."></textarea>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i>Crear Conteo
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Manejar cambio de producto
    $('#selectProducto').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const codigo = selectedOption.val();
        const nombre = selectedOption.data('nombre');
        
        if (codigo) {
            $('#codigoBarras').val(codigo);
            
            // Obtener existencia real del producto
            $.get('https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ObtenerExistenciaReal.php', 
                { codigo: codigo, sucursal: $('select[name="sucursal"]').val() }, 
                function(data) {
                    if (data.success) {
                        $('#existenciaReal').val(data.existenciaReal);
                    } else {
                        $('#existenciaReal').val('0');
                    }
                }, 'json');
        } else {
            $('#codigoBarras').val('');
            $('#existenciaReal').val('');
        }
    });
    
    // Manejar cambio de sucursal
    $('select[name="sucursal"]').on('change', function() {
        if ($('#selectProducto').val()) {
            $('#selectProducto').trigger('change');
        }
    });
    
    // Envío del formulario
    $('#formNuevoConteo').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.post('https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CrearConteo.php', 
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
