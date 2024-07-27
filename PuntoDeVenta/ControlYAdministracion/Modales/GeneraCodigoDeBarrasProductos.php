<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT 
    Productos_POS.ID_Prod_POS, 
    Productos_POS.Cod_Barra, 
    Productos_POS.Nombre_Prod, 
    Productos_POS.Clave_adicional, 
    Productos_POS.Clave_Levic, 
    Productos_POS.Precio_Venta, 
    Productos_POS.Precio_C, 
    Servicios_POS.Nom_Serv, 
    Productos_POS.FkMarca, 
    Productos_POS.Tipo, 
    Productos_POS.FkCategoria, 
    Productos_POS.FkPresentacion, 
    Productos_POS.Proveedor1, 
    Productos_POS.AgregadoPor, 
    Productos_POS.AgregadoEl
FROM 
    Productos_POS
LEFT JOIN 
    Servicios_POS ON Servicios_POS.Servicio_ID = Productos_POS.Tipo_Servicio AND
Productos_POS.ID_Prod_POS = ". $_POST["id"];
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
                <input type="text" class="form-control" id="Cod_Barra" name="Cod_Barra" value="<?php echo $Producto->Cod_Barra; ?>" maxlength="60" readonly>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const nombreProductoElem = document.getElementById('Nombre_Prod');
    const tipoServicioElem = document.getElementById('tiposervicio');
    const codBarraElem = document.getElementById('Cod_Barra');

    function generateCodigoBarra() {
        const nombreProducto = nombreProductoElem.value;
        const tipoServicio = tipoServicioElem.selectedOptions[0].text;

        if (!nombreProducto || !tipoServicio) {
            return; // No hacer nada si los campos están vacíos
        }

        const nombreProductoShort = nombreProducto.slice(0, 4).toUpperCase();
        const tipoServicioShort = tipoServicio.slice(0, 4).toUpperCase();
        
        const today = new Date();
        const month = ('0' + (today.getMonth() + 1)).slice(-2);
        const year = today.getFullYear().toString().slice(-2);
        
        const codigoBarra = tipoServicioShort + nombreProductoShort + month + year;

        codBarraElem.value = codigoBarra;
    }

    // Generar código de barras al cargar la página si los campos ya tienen datos
    generateCodigoBarra();

    // Generar código de barras cuando cambien los campos
    nombreProductoElem.addEventListener('input', generateCodigoBarra);
    tipoServicioElem.addEventListener('change', generateCodigoBarra);
});
</script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
