<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Validación del ID recibido
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

$sql1 = "SELECT 
    Stock_POS.Folio_Prod_Stock, Stock_POS.Cod_Barra, Stock_POS.Max_Existencia, 
    Stock_POS.Min_Existencia, Sucursales.Nombre_Sucursal 
FROM Stock_POS
INNER JOIN Sucursales ON Stock_POS.Fk_sucursal = Sucursales.ID_Sucursal
WHERE Stock_POS.Folio_Prod_Stock = ?";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$Especialistas = $result->fetch_object();

?>

<?php if($Especialistas != null): ?>
<form action="javascript:void(0)" method="post" id="ActualizaServicios">
    <div class="form-group">
        <label>Codigo de barras</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
        </div>
    </div>

    <div class="form-group">
        <label>Máximo<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="maxexistencia" name="ActNomServ" 
                   value="<?php echo $Especialistas->Max_Existencia; ?>" maxlength="60">
        </div>
    </div>

    <div class="form-group">
        <label>Mínimo<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="minexistencia" name="ActMinServ" 
                   value="<?php echo $Especialistas->Min_Existencia; ?>" maxlength="60">
        </div>
    </div>

    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<script src="js/ActualizalosMinMax.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

