<?php

include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Validación del ID recibido
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

// Consulta para obtener los datos de Existencias_R
$sql1 = "SELECT Stock_POS.Folio_Prod_Stock, Stock_POS.Cod_Barra, Stock_POS.Nombre_Prod, Stock_POS.Existencias_R AS Existencias_Actuales, 
    Stock_POS.Max_Existencia, Stock_POS.Min_Existencia, Stock_POS.AgregadoEl AS Fecha_Sugerencia, Stock_POS.Estatus, 
    Stock_POS.Fk_sucursal, Stock_POS.ID_H_O_D, Stock_POS.Proveedor1 AS Proveedor, 
    (Stock_POS.Max_Existencia - Stock_POS.Existencias_R) AS Cantidad_Sugerida, 
    Sucursales.Nombre_Sucursal 
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
<form action="javascript:void(0)" method="post" id="AjusteInventarioManualForm">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Folio de Producto</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Código de barras</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Nombre del Producto</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Nombre_Prod; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Existencias Actuales</label>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" disabled readonly value="<?php echo $Especialistas->Existencias_Actuales; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Min Existencia</label>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" disabled readonly value="<?php echo $Especialistas->Min_Existencia; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Max Existencia</label>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" disabled readonly value="<?php echo $Especialistas->Max_Existencia; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Cantidad Sugerida</label>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" disabled readonly value="<?php echo $Especialistas->Cantidad_Sugerida; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha Sugerencia</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Fecha_Sugerencia; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Estatus</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Estatus; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Sucursal (ID)</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Fk_sucursal; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>ID H_O_D</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->ID_H_O_D; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Proveedor</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Proveedor; ?>">
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Folio_Prod_Stock; ?>">
    <input type="hidden" name="ActUsuarioCServ" id="ActUsuarioCServ" value="<?php echo $row['Nombre_Apellidos']?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>

<!-- <script>
    // Definir la función que calcula el ajuste
    function calcularAjuste() {
        // Obtener referencias a los elementos
        const existenciaActual = parseFloat(document.getElementById('existencia_actual').value) || 0;
        const ajuste = parseFloat(document.getElementById('ajuste').value) || 0;
        const resultado = existenciaActual + ajuste;

        // Mostrar el resultado en el campo correspondiente
        document.getElementById('resultado_ajuste').value = resultado;
    }

    // Asignar la función al evento 'input' del campo de ajuste
    document.getElementById('ajuste').addEventListener('input', calcularAjuste);
</script> -->


<script src="js/AgregaEnOrdenCompra.js"></script>

<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>