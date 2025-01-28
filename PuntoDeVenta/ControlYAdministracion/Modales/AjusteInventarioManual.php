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
    Stock_POS.Folio_Prod_Stock, Stock_POS.Cod_Barra,Stock_POS.Existencias_R, Stock_POS.Max_Existencia, 
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
        <label>Código de barras</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
        </div>
    </div>

  
    <div class="form-group">
    <label>Existencia Actual<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
        <input type="number" class="form-control" id="existencia_actual" 
               value="100" readonly>
    </div>
</div>

<div class="form-group">
    <label>Valor de Ajuste<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
        <input type="number" class="form-control" id="ajuste" 
               placeholder="Ej. +50 o -30">
    </div>
</div>

<div class="form-group">
    <label>Resultado del Ajuste<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
        <input type="number" class="form-control" id="resultado_ajuste" readonly>
    </div>
</div>

        <label>Justificación<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <textarea class="form-control" id="justificacion" name="justificacion" 
                      maxlength="255"></textarea>
        </div>
    </div>

    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
    <input type="hidden" name="ActUsuarioCServ" id="ActUsuarioCServ" value="<?php echo $row['Nombre_Apellidos']?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<script>
    // Definir la función que calcula el ajuste
    function calcularAjuste() {
        // Obtener referencias a los elementos
        const existenciaActual = parseFloat(document.getElementById('existencia_actual').value) || 0;
        const ajuste = parseFloat(document.getElementById('ajuste').value) || 0;
        const resultado = existenciaActual + ajuste;

        // Mostrar el resultado en el campo correspondiente
        document.getElementById('resultado_ajuste').value = resultado.toFixed(2);
    }

    // Asignar la función al evento 'input' del campo de ajuste
    document.getElementById('ajuste').addEventListener('input', calcularAjuste);
</script>


<script src="js/ActualizalosMinMax.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>