<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Validación del ID recibido
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

// Consulta para obtener los datos de Existencias_R
$sql1 = "SELECT 
    Existencias_R.Folio_Prod_Stock, Existencias_R.Cod_Barra, Existencias_R.Existencia_Actual, 
    Existencias_R.Ajuste, Existencias_R.Resultado_Ajuste, Existencias_R.Justificacion, 
    Sucursales.Nombre_Sucursal 
FROM Existencias_R
INNER JOIN Sucursales ON Existencias_R.Fk_sucursal = Sucursales.ID_Sucursal
WHERE Existencias_R.Folio_Prod_Stock = ?";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$Especialistas = $result->fetch_object();

?>

<?php if($Especialistas != null): ?>
<form action="javascript:void(0)" method="post" id="ActualizaServicios">
    <div class="form-group">
        <label>Código de barras</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
        </div>
    </div>

    <div class="form-group">
        <label>Existencia Actual<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="existencia_actual" name="existencia_actual" 
                   value="<?php echo $Especialistas->Existencia_Actual; ?>" maxlength="60" readonly>
        </div>
    </div>

    <div class="form-group">
        <label>Valor de Ajuste<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="ajuste" name="ajuste" 
                   value="<?php echo $Especialistas->Ajuste; ?>" maxlength="60">
        </div>
    </div>

    <div class="form-group">
        <label>Resultado del Ajuste<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="resultado_ajuste" name="resultado_ajuste" 
                   value="<?php echo $Especialistas->Resultado_Ajuste; ?>" maxlength="60" readonly>
        </div>
    </div>

    <div class="form-group">
        <label>Justificación<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <textarea class="form-control" id="justificacion" name="justificacion" 
                      maxlength="255"><?php echo $Especialistas->Justificacion; ?></textarea>
        </div>
    </div>

    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
    <input type="hidden" name="ActUsuarioCServ" id="ActUsuarioCServ" value="<?php echo $row['Nombre_Apellidos']?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<script src="js/ActualizalosMinMax.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>