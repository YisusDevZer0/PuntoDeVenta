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
            <input type="number" class="form-control" id="existencia_actual" name="existencia_actual" 
                   value="<?php echo $Especialistas->Existencias_R; ?>" maxlength="60" readonly>
        </div>
    </div>

    <div class="form-group">
        <label>Valor de Ajuste<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="number" class="form-control" id="ajuste" name="ajuste" 
                   maxlength="60" step="any" placeholder="Ej. +50 o -30">
        </div>
    </div>

    <div class="form-group">
        <label>Resultado del Ajuste<span class="text-danger">*</span></label>
        <div class="input-group mb-3">
            <input type="number" class="form-control" id="resultado_ajuste" name="resultado_ajuste" 
                   value="" maxlength="60" readonly>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log("El DOM ha sido cargado correctamente."); // Verificar que el evento se ejecuta

    // Obtener el valor inicial de la existencia desde PHP
    const existenciaInicial = parseFloat(<?php echo $Especialistas->Existencias_R; ?> || 0);
    console.log("Existencia inicial:", existenciaInicial); // Verificar el valor inicial

    // Obtener referencias a los elementos del DOM
    const inputAjuste = document.getElementById('ajuste');
    const resultadoAjuste = document.getElementById('resultado_ajuste');

    // Verificar que los elementos existen
    if (!inputAjuste || !resultadoAjuste) {
        console.error("No se encontraron los elementos del DOM.");
        return;
    }

    console.log("Elementos del DOM encontrados correctamente.");

    // Función para calcular el ajuste
    function calcularAjuste() {
        console.log("Calculando ajuste..."); // Verificar que la función se ejecuta

        // Obtener el valor del ajuste (puede ser positivo o negativo)
        const ajuste = parseFloat(inputAjuste.value) || 0;
        console.log("Valor de ajuste:", ajuste); // Verificar el valor del ajuste

        // Calcular el resultado sumando la existencia inicial con el ajuste
        const resultado = existenciaInicial + ajuste;
        console.log("Resultado del cálculo:", resultado); // Verificar el resultado

        // Mostrar el resultado en el campo correspondiente
        resultadoAjuste.value = resultado.toFixed(2); // Mostrar con 2 decimales
    }

    // Escuchar el evento 'input' en el campo de ajuste
    inputAjuste.addEventListener('input', calcularAjuste);

    // Calcular el ajuste inicialmente (por si hay algún valor precargado)
    calcularAjuste();
});
</script>
<script src="js/ActualizalosMinMax.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>