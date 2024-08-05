<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Obtener valores de la sesión
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$Id_PvUser = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : '';

// Obtener el ID del producto desde el POST
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

// Consulta para obtener datos del producto de la tabla CEDIS
$sql1 = "SELECT 
    CEDIS.IdProdCedis as IdProdCedis, 
    CEDIS.Nombre_Prod
FROM 
    CEDIS
WHERE 
    CEDIS.IdProdCedis = " . $id;

$query = $conn->query($sql1);
$Producto = null;

if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Producto = $r;
        break;
    }
}
?>

<?php if ($Producto != null): ?>

<form action="procesar_datos_producto.php" method="post" id="FormularioProducto">
    <div class="product-info">
        <h2>Datos del Producto</h2>
        <p>Nombre del producto: <?php echo htmlspecialchars($Producto->Nombre_Prod, ENT_QUOTES, 'UTF-8'); ?></p>
        <p>ID del producto: <?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?></p>
        <input type="hidden" id="ID_Prod_Cedis" name="ID_Prod_Cedis" value="<?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="numeroFactura">Codigo de barras:</label>
            <input type="text" class="form-control" id="codbarrA"  value="<?php echo htmlspecialchars($Producto->Cod_Barra, ENT_QUOTES, 'UTF-8'); ?>" name="CodBarra" required>
        </div>
        <div class="form-group">
            <label for="numeroFactura">Número de Factura o Nota:</label>
            <input type="text" class="form-control" id="numeroFactura" name="numeroFactura" required>
        </div>
        <div class="form-group">
            <label for="cantidadPiezas">Cantidad de Piezas:</label>
            <input type="number" class="form-control" id="cantidadPiezas" name="cantidadPiezas" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>

<style>
.product-info {
    background-color: #e2e3e5;
    color: #383d41;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-top: 20px; /* Espacio superior para separación del formulario */
}
.product-info h2, .product-info p, .product-info .form-group {
    margin: 10px 0;
}
.product-info .form-group label {
    display: block;
    margin-bottom: 5px;
}
.product-info .form-group input {
    width: 100%;
}
.product-info button {
    margin-top: 10px;
}
</style>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
