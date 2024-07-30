<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Obtener valores de la sesión
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$Id_PvUser = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : '';

$sql1 = "SELECT 
    Productos_POS.ID_Prod_POS as IdProdCedis, 
    Productos_POS.Nombre_Prod
FROM 
    Productos_POS
WHERE 
    Productos_POS.ID_Prod_POS = " . $_POST["id"];
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

<form action="javascript:void(0)" method="post" id="ActualizaDatosDeProductos">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto</label>
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo htmlspecialchars($Producto->Nombre_Prod, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="ID_Prod_POS">ID del Producto</label>
                <input type="text" class="form-control" id="ID_Prod_POS" name="ID_Prod_POS" value="<?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
        </div>
    </div>

    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<!-- Modal de confirmación -->
<div id="confirmationMessage" class="confirmation-message">
    <h2>¿Estás seguro que deseas eliminar estos datos?</h2>
    <p>Nombre del producto: <?php echo htmlspecialchars($Producto->Nombre_Prod, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>ID del producto: <?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?></p>
    <button id="confirmDelete" class="btn btn-danger">Eliminar</button>
    <button id="cancelDelete" class="btn btn-secondary">Cancelar</button>
</div>

<style>
.confirmation-message {
    display: block; /* Muestra el modal automáticamente */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #f8d7da;
    color: #721c24;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    z-index: 1000;
}
.confirmation-message h2, .confirmation-message p {
    margin: 10px 0;
}
.confirmation-message button {
    margin: 10px;
}
</style>

<script>
document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('confirmationMessage').style.display = 'none';
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    // Aquí puedes colocar la lógica para eliminar el producto
    console.log('Producto eliminado');
    document.getElementById('confirmationMessage').style.display = 'none';
});
</script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
