<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Obtener valores de la sesión
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$Id_PvUser = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : '';

$user_id = null;
$sql1 = "SELECT 
    Productos_POS.ID_Prod_POS as IdProdCedis, 
    Productos_POS.Cod_Barra, 
    Productos_POS.Nombre_Prod, 
    Productos_POS.Clave_adicional as Clave_interna, 
    Productos_POS.Clave_Levic as Clave_Levic, 
    Productos_POS.Precio_Venta, 
    Productos_POS.Precio_C, 
    Servicios_POS.Nom_Serv as Nom_Serv, 
    Productos_POS.FkMarca as Marca, 
    Productos_POS.Tipo, 
    Productos_POS.FkCategoria as Categoria, 
    Productos_POS.FkPresentacion as Presentacion, 
    Productos_POS.Proveedor1, 
    Productos_POS.AgregadoPor, 
    Productos_POS.AgregadoEl
FROM 
    Productos_POS
LEFT JOIN 
    Servicios_POS ON Servicios_POS.Servicio_ID = Productos_POS.Tipo_Servicio
WHERE 
    Productos_POS.ID_Prod_POS = ". $_POST["id"];
$query = $conn->query($sql1);
$Producto = null;

if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Producto = $r;
        break;
    }
}

// Obtener las primeras 3 letras del tipo de servicio
$tipoServicio = isset($Producto->Nom_Serv) ? strtoupper(substr($Producto->Nom_Serv, 0, 3)) : '';

// Obtener la primera letra del nombre del producto
$nombreProd = isset($Producto->Nombre_Prod) ? strtoupper(substr($Producto->Nombre_Prod, 0, 1)) : '';

// Obtener el ID_Prod_POS
$idProdPos = isset($Producto->IdProdCedis) ? strtoupper($Producto->IdProdCedis) : '';

// Obtener la fecha actual en formato MMDD
$fechaActual = date('md');

// Concatenar los valores
$codBarra = $tipoServicio . $nombreProd . $idProdPos . $fechaActual . $Fk_Sucursal . $Id_PvUser;
?>

<?php if ($Producto != null): ?>

<form action="javascript:void(0)" method="post" id="ActualizaDatosDeProductos">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto</label>
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo htmlspecialchars($Producto->Nombre_Prod, ENT_QUOTES, 'UTF-8'); ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Cod_Barra">Código de Barra</label>
                <input type="text" class="form-control" id="Cod_Barra" name="Cod_BarraActualiza" value="<?php echo htmlspecialchars($codBarra, ENT_QUOTES, 'UTF-8'); ?>" maxlength="60" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Tipo_Servicio">Tipo de Servicio</label>
                <select id="tiposervicio" class="form-control" name="Tipo_ServicioActualiza">
                    <option value="<?php echo $Producto->Tipo_Servicio; ?>"><?php echo htmlspecialchars($Producto->Nom_Serv, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php
                    $query = $conn->query("SELECT * FROM Servicios_POS WHERE Licencia='".$Producto->Licencia."'");
                    while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.htmlspecialchars($valores["Servicio_ID"], ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($valores["Nom_Serv"], ENT_QUOTES, 'UTF-8').'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <input type="number" hidden name="ID_Prod_POSAct" id="id" value="<?php echo htmlspecialchars($Producto->IdProdCedis, ENT_QUOTES, 'UTF-8'); ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

<script src="js/GeneraLosCodigosDeBarrasSS.js"></script>