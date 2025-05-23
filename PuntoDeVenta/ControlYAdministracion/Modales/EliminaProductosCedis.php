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

<form action="javascript:void(0)" method="post" id="EliminaDatosDeProductos">
    <div id="confirmationMessage" class="confirmation-message">
        <h2>¿Estás seguro que deseas eliminar estos datos?</h2>
        <p>Nombre del producto: <?php echo htmlspecialchars($Producto->Nombre_Prod, ENT_QUOTES, 'UTF-8'); ?></p>
        <p>ID del producto: <?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?></p>
        <input type="text" hidden class="form-control" id="ID_Prod_POS" name="ID_Prod_Cedis" value="<?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        <button type="submit" id="submit" class="btn btn-danger">Eliminar</button>
    </div>
</form>

<style>
.confirmation-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-top: 20px; /* Espacio superior para separación del formulario */
}
.confirmation-message h2, .confirmation-message p {
    margin: 10px 0;
}
.confirmation-message button {
    margin: 10px;
}
</style>

<script src="js/DeleteDataDeProductosCedis.js"></script>
<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
