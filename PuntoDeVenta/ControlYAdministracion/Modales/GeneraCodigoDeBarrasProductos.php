<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Productos_POS WHERE ID_Prod_POS = ". $_POST["id"];
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
        <div class="col-md-4">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto</label>
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo $Producto->Nombre_Prod; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Cod_Barra">Código de Barra</label>
                <input type="text" class="form-control" id="Cod_Barra" name="Cod_Barra" value="<?php echo $Producto->Cod_Barra; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Tipo_Servicio">Tipo de Servicio</label>
                <select id="tiposervicio" class="form-control" name="Tipo_Servicio">
                    <option value="<?php echo $Producto->Tipo_Servicio; ?>"><?php echo $Producto->Tipo_Servicio; ?></option>
                    <?php
                    $query = $conn->query("SELECT * FROM Servicios_POS WHERE Licencia='".$Producto->Licencia."'");
                    while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.$valores["Servicio_ID"].'">'.$valores["Nom_Serv"].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <input type="hidden" name="ID_Prod_POS" id="id" value="<?php echo $Producto->ID_Prod_POS; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<script src="js/ActualizaDataDeProductos.js"></script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
