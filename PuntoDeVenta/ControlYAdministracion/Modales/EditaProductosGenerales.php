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
                <label for="ID_Prod_POS">ID Producto</label>
                <input type="text" class="form-control" disabled readonly value="<?php echo $Producto->ID_Prod_POS; ?>">
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
                <label for="Clave_adicional">Clave Adicional</label>
                <input type="text" class="form-control" id="Clave_adicional" name="Clave_adicional" value="<?php echo $Producto->Clave_adicional; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Clave_Levic">Clave Levic</label>
                <input type="text" class="form-control" id="Clave_Levic" name="Clave_Levic" value="<?php echo $Producto->Clave_Levic; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto</label>
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo $Producto->Nombre_Prod; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Precio_Venta">Precio de Venta</label>
                <input type="text" class="form-control" id="Precio_Venta" name="Precio_Venta" value="<?php echo $Producto->Precio_Venta; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Precio_C">Precio de Compra</label>
                <input type="text" class="form-control" id="Precio_C" name="Precio_C" value="<?php echo $Producto->Precio_C; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Tipo_Servicio">Tipo de Servicio</label>
                
                <select id = "tiposervicio" class = "form-control" name="Tipo_Servicio">
                                               <option value="<?php echo $Producto->Tipo_Servicio; ?>"><?php echo $Producto->Tipo_Servicio; ?></option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Servicios_POS WHERE  Licencia='".$row['Licencia']."'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Servicio_ID"].'">'.$valores["Nom_Serv"].'</option>';
          }
        ?>  </select>        
          
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Componente_Activo">Componente Activo</label>
                
                <select id = "componente" class = "form-control" name="Componente_Activo">
                                               <option value="<?php echo $Producto->Componente_Activo; ?>"><?php echo $Producto->Componente_Activo; ?></option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Componentes");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Com"].'">'.$valores["Nom_Com"].'</option>';
          }
        ?> 
                                              </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Tipo">Tipo</label>
                <input type="text" class="form-control" id="Tipo" name="Tipo" value="<?php echo $Producto->Tipo; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkCategoria">Categoría</label>
                <input type="text" class="form-control" id="FkCategoria" name="FkCategoria" value="<?php echo $Producto->FkCategoria; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkMarca">Marca</label>
                <input type="text" class="form-control" id="FkMarca" name="FkMarca" value="<?php echo $Producto->FkMarca; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkPresentacion">Presentación</label>
                <input type="text" class="form-control" id="FkPresentacion" name="FkPresentacion" value="<?php echo $Producto->FkPresentacion; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Proveedor1">Proveedor 1</label>
                <input type="text" class="form-control" id="Proveedor1" name="Proveedor1" value="<?php echo $Producto->Proveedor1; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Proveedor2">Proveedor 2</label>
                <input type="text" class="form-control" id="Proveedor2" name="Proveedor2" value="<?php echo $Producto->Proveedor2; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="RecetaMedica">Requiere Receta Médica</label>
                <input type="text" class="form-control" id="RecetaMedica" name="RecetaMedica" value="<?php echo $Producto->RecetaMedica; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="AgregadoPor">Agregado Por</label>
                <input type="text" class="form-control" readonly id="AgregadoPor" name="AgregadoPor" value="<?php echo $Producto->AgregadoPor; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="AgregadoEl">Agregado El</label>
                <input type="text" class="form-control" readonly id="AgregadoEl" name="AgregadoEl" value="<?php echo $Producto->AgregadoEl; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Licencia">Licencia</label>
                <input type="text" class="form-control" readonly id="Licencia" name="Licencia" value="<?php echo $Producto->Licencia; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Ivaal16">IVA al 16%</label>
                <input type="text" class="form-control" id="Ivaal16" name="Ivaal16" value="<?php echo $Producto->Ivaal16; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ActualizadoPor">Ultima actualizacion por:</label>
                <input type="text" class="form-control" readonly id="ActualizadoPor" name="ActualizadoPor" value="<?php echo $Producto->ActualizadoPor; ?>" maxlength="60">
            </div>
        </div>
    </div>

  <br>

    <input type="hidden" name="ID_Prod_POS" id="id" value="<?php echo $Producto->ID_Prod_POS; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>
<script src="js/ActualizacionDePresentaciones.js"></script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
