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
        <select id = "tipogasto" class = "form-control" name = "Concepto_Categoria">
                                               <option value="">Seleccione un gasto:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM TiposDeGastos");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Gasto"].'">'.$valores["Nom_Gasto"].'</option>';
          }
        ?>  </select>
    </div>

    <div class="mb-3">
        <label for="cantidad_gasto" class="form-label">Cantidad en efectivo:</label>
        <input type="text" name="Importe_Total" id="cantidad_gasto" class="form-control">
    </div>

    <div class="mb-3">
        <label for="nombre_recibe" class="form-label">Nombre de quien recibe:</label>
        <input type="text" name="Recibe" id="nombre_recibe" class="form-control">
    </div>

    <!-- Manten el input oculto con el ID_Caja -->
    <input type="hidden" name="Fk_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
    <input type="hidden" name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']?>">
    <input type="hidden" name="AgregadoPor" id="AgregadoPor" value="<?php echo $row['Nombre_Apellidos']?>">
    <input type="hidden" name="Fk_sucursal" id="sucursal" value="<?php echo $row['Fk_Sucursal']?>">
    <input type="hidden" name="Sistema" id="licencia" value="Administrador">

    <input type="hidden" name="Licencia" id="licencia" value="<?php echo $row['Licencia']?>">

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<script src="js/RegistraElNuevoGasto.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
