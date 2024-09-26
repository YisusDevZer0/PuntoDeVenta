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
                <input type="text" class="form-control" id="Cod_Barra" name="Cod_Barra" value="<?php echo $Producto->Cod_Barra; ?>" maxlength="6000">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Clave_adicional">Clave Adicional</label>
                <input type="text" class="form-control" id="Clave_adicional" name="Clave_adicional" value="<?php echo $Producto->Clave_adicional; ?>" >
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
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo $Producto->Nombre_Prod; ?>" >
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
              
                <select id = "tip" class = "form-control" name = "Tip">
                                               <option  value="<?php echo $Producto->Tipo; ?>"><?php echo $Producto->Tipo; ?></option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM TipProd_POS");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Tipo_Prod"].'">'.$valores["Nom_Tipo_Prod"].'</option>';
          }
        ?>  </select> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkCategoria">Categoría</label>
              
                <select id = "categoria" class = "form-control" name = "Categoria">
                                               <option value="<?php echo $Producto->FkCategoria; ?>"><?php echo $Producto->FkCategoria; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Categorias_POS");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Cat"].'">'.$valores["Nom_Cat"].'</option>';
          }
        ?>  </select>
           
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkMarca">Marca</label>
               
                <select id = "marca" class = "form-control" name = "Marca">
                                               <option value="<?php echo $Producto->FkMarca; ?>" ><?php echo $Producto->FkMarca; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Marcas_POS ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Marca"].'">'.$valores["Nom_Marca"].'</option>';
          }
        ?>  </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="FkPresentacion">Presentación</label>
               
                <select id = "presentacion" class = "form-control" name = "Presentacion">
                                               <option value="<?php echo $Producto->FkPresentacion; ?>"><?php echo $Producto->FkPresentacion; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Presentaciones ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Presentacion"].'">'.$valores["Nom_Presentacion"].'</option>';
          }
        ?>  </select> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Proveedor1">Proveedor 1</label>
              
                <select id = "proveedor" class = "form-control" name = "Proveedor">
                                               <option value="<?php echo $Producto->Proveedor1; ?>"><?php echo $Producto->Proveedor1; ?></option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Proveedores");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Proveedor2">Proveedor 2</label>
                
                <select id = "proveedor2" class = "form-control" name = "Prov2">
                                               <option value="<?php echo $Producto->Proveedor2; ?>"><?php echo $Producto->Proveedor2; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Proveedores");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select>
            
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
                <input type="text" class="form-control" readonly  value="<?php echo $Producto->ActualizadoPor; ?>" maxlength="60">
                <input type="text" class="form-control" readonly id="ActualizadoPor" name="ActualizadoPor" value="<?php echo $row['Nombre_Apellidos']?>">
            </div>
        </div>
    </div>

  <br>

    <input type="hidden" name="ID_Prod_POS" id="id" value="<?php echo $Producto->ID_Prod_POS; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>
<script src="js/ActualizaDataDeProductos.js"></script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
