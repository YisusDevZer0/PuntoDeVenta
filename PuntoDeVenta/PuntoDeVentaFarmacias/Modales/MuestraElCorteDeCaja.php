<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;

if (!$fk_caja) {
    echo "Faltan parámetros necesarios.";
    exit;
}

// CONSULTA 1
$sql1 = "SELECT ID_Caja, Fk_Caja, Sucursal, Turno, TotalTickets, Valor_Total_Caja 
         FROM Cortes_Cajas_POS 
         WHERE Fk_Caja = '$fk_caja' 
         ORDER BY ID_Caja ASC LIMIT 1";
$query1 = $conn->query($sql1);
$Especialistas = null;
if ($query1 && $query1->num_rows > 0) {
    $Especialistas = $query1->fetch_object();
}

// CONSULTA 2
$sql2 = "SELECT ID_Caja, Fk_Caja, Sucursal, Turno, TotalTickets, Valor_Total_Caja 
         FROM Cortes_Cajas_POS 
         WHERE Fk_Caja = '$fk_caja' 
         ORDER BY ID_Caja DESC LIMIT 1";
$query2 = $conn->query($sql2);
$Especialistas2 = null;
if ($query2 && $query2->num_rows > 0) {
    $Especialistas2 = $query2->fetch_object();
}

// CONSULTA 3
$sql3 = "SELECT ID_Caja, Fk_Caja, Turno, TotalTickets, Valor_Total_Caja, Empleado, Sucursal 
         FROM Cortes_Cajas_POS 
         WHERE Fk_Caja = '$fk_caja'";
$query3 = $conn->query($sql3);
$Especialistas3 = null;
if ($query3 && $query3->num_rows > 0) {
    $Especialistas3 = $query3->fetch_object();
}

// Consulta 13: Cortes de cajas POS
$sql13 = "SELECT * FROM Cortes_Cajas_POS 
          WHERE Fk_Caja = '$fk_caja'";
$query13 = $conn->query($sql13);
$Especialistas13 = null;
if ($query13 && $query13->num_rows > 0) {
    $Especialistas13 = $query13->fetch_object();
}

// CONSULTA TOTALES
$sql_totales = "SELECT 
    SUM(TotalEfectivo) as totalesdepagoEfectivo,
    SUM(TotalTarjeta) as totalesdepagotarjeta,
    SUM(TotalCreditos) as totalesdepagoCreditos,
    SUM(Valor_Total_Caja) as TotalCantidad
FROM Cortes_Cajas_POS 
WHERE Fk_Caja = '$fk_caja'";
$result_totales = $conn->query($sql_totales);

// Verificar si la consulta se ejecutó correctamente
if ($result_totales) {
    if ($result_totales->num_rows > 0) {
        $row_totales = $result_totales->fetch_assoc();

        $totalesdepagoEfectivo = $row_totales['totalesdepagoEfectivo'];
        $totalesdepagotarjeta = $row_totales['totalesdepagotarjeta'];
        $totalesdepagoCreditos = $row_totales['totalesdepagoCreditos'];
        $TotalCantidad = $row_totales['TotalCantidad'];
    } else {
        echo '<p class="alert alert-danger">No se encontraron datos para mostrar.</p>';
    }
} else {
    echo '<p class="alert alert-danger">Error en la consulta: ' . $conn->error . '</p>';
}

$EspecialistasTotales = null;
if ($result_totales && $result_totales->num_rows > 0) {
    $EspecialistasTotales = $result_totales->fetch_object();
}

?>

<?php if ($result_totales->num_rows > 0): ?>
    <div class="text-center">
        <div class="table-responsive">
            <table id="TotalesFormaPagoCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Forma de pago</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Efectivo"></td>
                        <td><input type="text" class="form-control" name="EfectivoTotal" readonly value="<?php echo $totalesdepagoEfectivo; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Tarjeta"></td>
                        <td><input type="text" class="form-control" name="TarjetaTotal" readonly value="<?php echo $totalesdepagotarjeta; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Créditos"></td>
                        <td><input type="text" class="form-control" name="CreditosTotales" readonly value="<?php echo $totalesdepagoCreditos; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <input type="hidden" name="Sistema" value="Ventas">
    <input type="hidden" name="ID_H_O_D" value="DoctorPez">
  
    <label for="comentarios">Observaciones:</label>
    <textarea class="form-control" id="comentarios" name="comentarios" rows="4" cols="50" placeholder="Escribe tu comentario aquí..."></textarea>
    <br>
    <button type="submit" id="submit" class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
</form>

<?php else: ?>
    <p class="alert alert-danger">No se encontraron datos para mostrar.</p>
<?php endif; ?>
