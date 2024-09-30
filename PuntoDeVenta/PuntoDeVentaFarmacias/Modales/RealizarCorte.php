<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;
$fk_sucursal = isset($_POST['fk_sucursal']) ? $_POST['fk_sucursal'] : null;
$id_h_o_d = isset($_POST['id_h_o_d']) ? $_POST['id_h_o_d'] : null;

// Verificar que las variables POST están definidas
if (!$fk_caja || !$fk_sucursal || !$id_h_o_d) {
    echo "Faltan parámetros necesarios.";
    exit;
}

// CONSULTA 1
$sql1 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID ASC LIMIT 1";
$query1 = $conn->query($sql1);
$Especialistas = null;
if ($query1 && $query1->num_rows > 0) {
    $Especialistas = $query1->fetch_object();
}

// CONSULTA 2
$sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID DESC LIMIT 1";
$query2 = $conn->query($sql2);
$Especialistas2 = null;
if ($query2 && $query2->num_rows > 0) {
    $Especialistas2 = $query2->fetch_object();
}

// CONSULTA 3
$sql3 = "SELECT Venta_POS_ID, Fk_Caja, Turno, Fecha_venta, Fk_sucursal, AgregadoPor, Turno, ID_H_O_D,
                COUNT(DISTINCT Folio_Ticket) AS Total_tickets, 
                COUNT(DISTINCT FolioSignoVital) AS Total_Folios, 
                SUM(Importe) AS VentaTotal  
         FROM Ventas_POS 
         WHERE Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' AND Fk_Caja = '$fk_caja'";
$query3 = $conn->query($sql3);
$Especialistas3 = null;
if ($query3 && $query3->num_rows > 0) {
    $Especialistas3 = $query3->fetch_object();
}

// CONSULTA 14
$sql14 = "SELECT 
            IFNULL(Servicios_POS.Servicio_ID, '0000') AS Servicio_ID, 
            IFNULL(Servicios_POS.Nom_Serv, 'No tiene servicio especificado') AS Nom_Serv, 
            Ventas_POS.Fk_sucursal, 
            Ventas_POS.ID_H_O_D, 
            Ventas_POS.Fecha_venta,
            Ventas_POS.AgregadoPor, 
            Ventas_POS.Fk_Caja, 
            Ventas_POS.AgregadoEl, 
            Sucursales.ID_Sucursal, 
            Sucursales.Nombre_Sucursal, 
            SUM(Ventas_POS.Importe) AS totaldeservicios 
         FROM 
            Ventas_POS
         LEFT JOIN 
            Servicios_POS 
            ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         INNER JOIN 
            Sucursales 
            ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
         WHERE 
            Ventas_POS.Fk_Caja = '$fk_caja' 
            AND Ventas_POS.ID_H_O_D = '$id_h_o_d'
         GROUP BY 
            Servicio_ID, 
            Nom_Serv";

$query14 = $conn->query($sql14);
$Especialistas14 = [];
if ($query14 && $query14->num_rows > 0) {
    while ($r = $query14->fetch_object()) {
        $Especialistas14[] = $r;
    }
}

// Consulta 6: Total de venta por crédito en enfermería
$sql6 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCredito 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Enfermería' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query6 = $conn->query($sql6);
$Especialistas6 = null;
if ($query6 && $query6->num_rows > 0) {
    $Especialistas6 = $query6->fetch_object();
}

// Consulta 7: Total de venta por crédito en limpieza
$sql7 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoLimpieza 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Limpieza' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query7 = $conn->query($sql7);
$Especialistas7 = null;
if ($query7 && $query7->num_rows > 0) {
    $Especialistas7 = $query7->fetch_object();
}

// Consulta 11: Total de venta por crédito farmacéutico
$sql11 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoFarmaceutico 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Farmacéutico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query11 = $conn->query($sql11);
$Especialistas11 = null;
if ($query11 && $query11->num_rows > 0) {
    $Especialistas11 = $query11->fetch_object();
}

// Consulta 12: Total de venta por crédito médico
$sql12 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoMedicos 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Médico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query12 = $conn->query($sql12);
$Especialistas12 = null;
if ($query12 && $query12->num_rows > 0) {
    $Especialistas12 = $query12->fetch_object();
}

// Consulta 13: Cortes de cajas POS
$sql13 = "SELECT * FROM Cortes_Cajas_POS 
          WHERE Sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query13 = $conn->query($sql13);
$Especialistas13 = null;
if ($query13 && $query13->num_rows > 0) {
    $Especialistas13 = $query13->fetch_object();
}

$sql_totales = "SELECT 
    -- Pagos en efectivo solamente
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagoEfectivo,
    
    -- Pagos con tarjeta solamente
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagotarjeta,
    
    -- Pagos a crédito
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagoCreditos,
    
    -- Parte en efectivo de pagos combinados con efectivo y tarjeta
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta ELSE 0 END) as totalEfectivoDeComb,
    
    -- Parte en tarjeta de pagos combinados con efectivo y tarjeta
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta ELSE 0 END) as totalTarjetaDeComb,
    
    -- Parte en efectivo de pagos combinados con efectivo y crédito
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe - Ventas_POS.Pagos_credito ELSE 0 END) as totalEfectivoDeCreditoComb,
    
    -- Parte a crédito de pagos combinados con efectivo y crédito
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Pagos_credito ELSE 0 END) as totalCreditoDeComb,
    
    -- Pagos con crédito especializado
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Crédito Enfermería' THEN Ventas_POS.Importe ELSE 0 END) as totalCreditoEnfermeria,
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Crédito Limpieza' THEN Ventas_POS.Importe ELSE 0 END) as totalCreditoLimpieza,
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Crédito Farmacéutico' THEN Ventas_POS.Importe ELSE 0 END) as totalCreditoFarmaceutico,
    SUM(CASE WHEN Ventas_POS.FormaDePago = 'Crédito Médico' THEN Ventas_POS.Importe ELSE 0 END) as totalCreditoMedico,
    
    -- Total general de todos los pagos
    SUM(Ventas_POS.Importe) as TotalCantidad
FROM Ventas_POS 
WHERE Ventas_POS.Fk_Caja = '$fk_caja' 
    AND Ventas_POS.Fk_sucursal = '$fk_sucursal' 
    AND Ventas_POS.ID_H_O_D = '$id_h_o_d'
";



$result_totales = $conn->query($sql_totales);

// Verificar si la consulta se ejecutó correctamente
if ($result_totales) {
    // Verificar si hay filas retornadas
    if ($result_totales->num_rows > 0) {
        // Obtener los resultados como un array asociativo
        $row_totales = $result_totales->fetch_assoc();

        // Asignar los valores a variables
        $totalesdepagoEfectivo = $row_totales['totalesdepagoEfectivo'];
        $totalesdepagotarjeta = $row_totales['totalesdepagotarjeta'];
        $totalesdepagoCreditos = $row_totales['totalesdepagoCreditos'];
        $totalEfectivoDeComb = $row_totales['totalEfectivoDeComb'];
        $totalTarjetaDeComb = $row_totales['totalTarjetaDeComb'];
        $totalCreditoEnfermeria = $row_totales['totalCreditoEnfermeria'];
        $totalCreditoLimpieza = $row_totales['totalCreditoLimpieza'];
        $totalCreditoFarmaceutico = $row_totales['totalCreditoFarmaceutico'];
        $totalCreditoMedico = $row_totales['totalCreditoMedico'];
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


<?php if (!empty($Especialistas14)) {
    $especialista = $Especialistas14[0];
} else {
    // Maneja el caso donde $Especialistas14 esté vacío si es necesario
    $especialista = null;
} ?>
    
    <form action="javascript:void(0)" method="post" id="FormDeCortes">
    <div class="text-center">
    <div class="row">
        <div class="col">
            <label for="exampleFormControlInput1">Sucursal</label>
            <input type="text" class="form-control" id="cantidadtotalventasss" step="any" readonly 
                   value="<?php echo $especialista ? $especialista->Nombre_Sucursal : ''; ?>" 
                   aria-describedby="basic-addon1">
            <input type="text" hidden name="Fk_Caja" value="<?php echo $especialista ? $especialista->Fk_Caja : ''; ?>">
            <input type="text" hidden name="Sucursal" value="<?php echo $especialista ? $especialista->Fk_sucursal : ''; ?>">
             <input type="text" hidden name="Turno" value="<?php echo $especialista ? $especialista->Turno : ''; ?>">
              
        </div>
    </div>
</div>

        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Cajero</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="Cajero" step="any" readonly value="<?php echo $Especialistas3->AgregadoPor; ?>" aria-describedby="basic-addon1">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Total de venta</label>
                <input type="number" class="form-control" id="cantidadtotalventassss" name="VentaTotal" step="any" readonly value="<?php echo $Especialistas3->VentaTotal; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Total de tickets</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotal" step="any" readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1">
            </div>
           
        </div>
    </div>

   
    <?php
// Verificar si $Especialistas14 no está vacío
if (!empty($Especialistas14)) {
?>
<div class="table-responsive">
    <table id="TotalesGeneralesCortes" class="table table-hover">
        <thead>
            <tr>
                <th>Nombre Servicio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Recorrer cada objeto en el array $Especialistas14
            foreach ($Especialistas14 as $especialista) {
                echo '<tr>';
                echo '<td><input type="text" class="form-control" readonly value="' . $especialista->Nom_Serv . '"></td>';
                echo '<td><input type="text" class="form-control" readonly value="' . $especialista->totaldeservicios . '"></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<?php
} else {
    // Manejar el caso donde $Especialistas14 esté vacío si es necesario
    echo '<p class="alert alert-danger">No se encontraron servicios para mostrar.</p>';
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
                    <!-- Totales de combinación Efectivo y Tarjeta -->
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Efectivo de Combinación"></td>
                        <td><input type="text" class="form-control" name="TotalEfectivoCombinado" readonly value="<?php echo $totalEfectivoDeComb; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Tarjeta de Combinación"></td>
                        <td><input type="text" class="form-control" name="TotalTarjetaCombinado" readonly value="<?php echo $totalTarjetaDeComb; ?>"></td>
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
<?php endif; ?><script src="js/RealizaCorteDeCaja.js"></script>