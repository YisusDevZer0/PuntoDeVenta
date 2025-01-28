<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../Controladores/db_connect.php";

// Validación del ID recibido
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

$sql1 = "SELECT 
    Stock_POS.Folio_Prod_Stock, Stock_POS.Nombre_Prod, Stock_POS.Cod_Barra, 
    Stock_POS.Precio_Venta, Stock_POS.Precio_C, Stock_POS.Lote, Stock_POS.Fecha_Caducidad
FROM Stock_POS
WHERE Stock_POS.Folio_Prod_Stock = ?";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$Especialistas = $result->fetch_object();
?>

<?php if($Especialistas != null): ?>
<!-- Modal HTML -->
<form action="javascript:void(0)" method="post" id="ActualizaProducto">
    <div class="row">
        <!-- Columna 1 -->
        <div class="col-md-6">
            <div class="form-group">
                <label>Nombre del Producto</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="nombre_prod" name="nombre_prod" 
                           value="<?php echo $Especialistas->Nombre_Prod; ?>" maxlength="60">
                </div>
            </div>
            <div class="form-group">
                <label>Precio de Venta</label>
                <div class="input-group mb-3">
                    <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" 
                           value="<?php echo $Especialistas->Precio_Venta; ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Lote</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="lote" name="lote" 
                           value="<?php echo $Especialistas->Lote; ?>" maxlength="30">
                </div>
            </div>
        </div>
        <!-- Columna 2 -->
        <div class="col-md-6">
            <div class="form-group">
                <label>Código de Barras</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Precio de Compra</label>
                <div class="input-group mb-3">
                    <input type="number" step="0.01" class="form-control" id="precio_c" name="precio_c" 
                           value="<?php echo $Especialistas->Precio_C; ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Fecha de Caducidad</label>
                <div class="input-group mb-3">
                    <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad" 
                           value="<?php echo $Especialistas->Fecha_Caducidad; ?>">
                </div>
            </div>
        </div>
    </div>
    <!-- Campos ocultos -->
    <input type="hidden" name="folio_prod_stock" id="folio_prod_stock" value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar Cambios <i class="fas fa-check"></i></button>
</form>

<script src="js/ActualizaProducto.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
