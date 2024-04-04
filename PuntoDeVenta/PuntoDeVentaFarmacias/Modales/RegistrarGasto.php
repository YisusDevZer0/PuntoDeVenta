<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="EliminaServiciosForm" class="mb-3">
 
    <div class="mb-3">
        <label for="tipo_gasto" class="form-label">Tipo de gasto:</label>
        <select name="tipo_gasto" id="tipo_gasto" class="form-control">
            <!-- Aquí puedes rellenar con los datos que desees -->
            <option value="opcion1">Opción 1</option>
            <option value="opcion2">Opción 2</option>
            <option value="opcion3">Opción 3</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="cantidad_gasto" class="form-label">Cantidad de gasto:</label>
        <input type="text" name="cantidad_gasto" id="cantidad_gasto" class="form-control">
    </div>

    <div class="mb-3">
        <label for="nombre_recibe" class="form-label">Nombre de quien recibe el dinero:</label>
        <input type="text" name="nombre_recibe" id="nombre_recibe" class="form-control">
    </div>

    <!-- Manten el input oculto con el ID_Caja -->
    <input type="hidden" name="ID_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<script src="js/RegistraElNuevoGasto.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
