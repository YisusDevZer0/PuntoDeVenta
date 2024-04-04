<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<?php if ($Especialistas) : ?>
<<form action="javascript:void(0)" method="post" id="EliminaServiciosForm">
    <i id="lockIcon" class="fas fa-unlock fa-5x text-success"></i>
    <p>¿Está seguro de que desea bloquear la caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día <?php echo $Especialistas->Fecha_Apertura; ?>?</p>
    <input type="hidden" name="ID_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">

    <label for="tipo_gasto">Tipo de gasto:</label>
    <select name="tipo_gasto" id="tipo_gasto">
        <!-- Aquí puedes rellenar con los datos que desees -->
        <option value="opcion1">Opción 1</option>
        <option value="opcion2">Opción 2</option>
        <option value="opcion3">Opción 3</option>
    </select><br>

    <label for="cantidad_gasto">Cantidad de gasto:</label>
    <input type="text" name="cantidad_gasto" id="cantidad_gasto"><br>

    <label for="nombre_recibe">Nombre de quien recibe el dinero:</label>
    <input type="text" name="nombre_recibe" id="nombre_recibe"><br>

    <!-- Manten el input oculto con el ID_Caja -->
    <input type="hidden" name="ID_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">

    <button type="submit">Enviar</button>
</form>

   
    <button type="submit" id="submit" class="btn btn-danger">
        Bloquear<i class="fas fa-lock"></i>
    </button>
</form>
<script src="js/RegistraElNuevoGasto.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
