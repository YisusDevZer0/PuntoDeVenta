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
} else {
    // Inicializar valores por defecto si no hay resultados
    $Especialistas3 = (object)[
        'VentaTotal' => 0,
        'Total_tickets' => 0,
        'AgregadoPor' => 'N/A',
        'Turno' => ''
    ];
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
            Ventas_POS.Turno, 
            Ventas_POS.AgregadoEl, 
            Sucursales.ID_Sucursal, 
            Sucursales.Nombre_Sucursal, 
            SUM(Ventas_POS.Importe) AS totaldeservicios 
         FROM Ventas_POS
         LEFT JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         INNER JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
         WHERE Ventas_POS.Fk_Caja = '$fk_caja' 
         AND Ventas_POS.ID_H_O_D = '$id_h_o_d'
         GROUP BY Servicio_ID, Nom_Serv";

$query14 = $conn->query($sql14);
$Especialistas14 = [];
if ($query14 && $query14->num_rows > 0) {
    while ($r = $query14->fetch_object()) {
        $Especialistas14[] = $r;
    }
}
// ==================================================
// AQUÍ VA EL NUEVO CÓDIGO
// ==================================================

// Inicializar $servicios con un arreglo vacío o un valor predeterminado
$servicios = [];

if (!empty($Especialistas14)) {
    foreach ($Especialistas14 as $especialista) {
        $servicios[] = [
            'nombre' => $especialista->Nom_Serv,
            'total' => $especialista->totaldeservicios ?? 0,
        ];
    }
} else {
    // Si no hay servicios, asignar un valor predeterminado
    $servicios = [
        [
            'nombre' => 'Sin servicios registrados',
            'total' => 0
        ]
    ];
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
} else {
    $Especialistas6 = (object)['VentaTotalCredito' => 0];
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
} else {
    $Especialistas7 = (object)['VentaTotalCreditoLimpieza' => 0];
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
} else {
    $Especialistas11 = (object)['VentaTotalCreditoFarmaceutico' => 0];
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
} else {
    $Especialistas12 = (object)['VentaTotalCreditoMedicos' => 0];
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

// Consulta de totales
$sql_totales = "SELECT 
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe 
        ELSE 0 
    END) AS totalesdepagoEfectivo,
    
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe 
        ELSE 0 
    END) AS totalesdepagotarjeta,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoTarjeta,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoEfectivo,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoEfectivoCredito,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoCredito,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoEfectivoTransferencia,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) AS complementoTransferencia,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe 
        ELSE 0 
    END) AS totalCredito,

    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Transferencia' THEN Ventas_POS.Importe 
        ELSE 0 
    END) AS totalTransferencia,

    (SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END)
    ) AS totalPagosEnEfectivo,

    (SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END)) AS totalPagosEnTarjeta,

    (SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END)) AS totalPagosEnCreditos,

    (SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Transferencia' THEN Ventas_POS.Importe 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END)) AS totalPagosEnTransferencia,

    SUM(Ventas_POS.Importe) AS TotalCantidad
FROM Ventas_POS 
WHERE Ventas_POS.Fk_Caja = '$fk_caja' AND Ventas_POS.Fk_sucursal = '$fk_sucursal' AND Ventas_POS.ID_H_O_D = '$id_h_o_d'";

$result_totales = $conn->query($sql_totales);

if ($result_totales) {
    if ($result_totales->num_rows > 0) {
        $row_totales = $result_totales->fetch_assoc();
    } else {
        $row_totales = [
            'totalesdepagoEfectivo' => 0,
            'totalesdepagotarjeta' => 0,
            'complementoTarjeta' => 0,
            'complementoEfectivo' => 0,
            'complementoEfectivoCredito' => 0,
            'complementoCredito' => 0,
            'complementoCreditoEfectivo' => 0,
            'totalCredito' => 0,
            'totalTransferencia' => 0,
            'complementoEfectivoTransferencia' => 0,
            'complementoTransferencia' => 0,
            'totalPagosEnEfectivo' => 0,
            'totalPagosEnTarjeta' => 0,
            'totalPagosEnCreditos' => 0,
            'totalPagosEnTransferencia' => 0,
            'TotalCantidad' => 0
        ];
    }
} else {
    echo '<p class="alert alert-danger">Error en la consulta: ' . $conn->error . '</p>';
}

// Asignar valores con defaults
$totalesdepagoEfectivo = $row_totales['totalesdepagoEfectivo'] ?? 0;
$totalesdepagotarjeta = $row_totales['totalesdepagotarjeta'] ?? 0;
$complementoTarjeta = $row_totales['complementoTarjeta'] ?? 0;
$complementoEfectivo = $row_totales['complementoEfectivo'] ?? 0;
$complementoEfectivoCredito = $row_totales['complementoEfectivoCredito'] ?? 0;
$complementoCredito = $row_totales['complementoCredito'] ?? 0;
$complementoCreditoEfectivo = $row_totales['complementoCreditoEfectivo'] ?? 0;
$totalCredito = $row_totales['totalCredito'] ?? 0;
$totalTransferencia = $row_totales['totalTransferencia'] ?? 0;
$complementoEfectivoTransferencia = $row_totales['complementoEfectivoTransferencia'] ?? 0;
$complementoTransferencia = $row_totales['complementoTransferencia'] ?? 0;
$totalPagosEnEfectivo = $row_totales['totalPagosEnEfectivo'] ?? 0;
$totalPagosEnTarjeta = $row_totales['totalPagosEnTarjeta'] ?? 0;
$totalPagosEnCreditos = $row_totales['totalPagosEnCreditos'] ?? 0;
$totalPagosEnTransferencia = $row_totales['totalPagosEnTransferencia'] ?? 0;
$TotalCantidad = $row_totales['TotalCantidad'] ?? 0;

?>

<!-- Mantener todo el HTML original -->
<form action="javascript:void(0)" method="post" id="FormDeCortes">
    <div class="text-center">
        <h5 class="text-center mt-3">Datos de caja</h5> 
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <label for="exampleFormControlInput1">Sucursal</label>
                    <input type="text" class="form-control" id="cantidadtotalventasss" step="any" readonly 
                           value="<?= htmlspecialchars($Especialistas14[0]->Nombre_Sucursal ?? 'Sucursal no especificada') ?>" 
                           aria-describedby="basic-addon1">
                    <input type="text" hidden name="Fk_Caja" value="<?= $fk_caja ?>">
                    <input type="text" hidden name="Sucursal" value="<?= $fk_sucursal ?>">
                    <input type="text" hidden name="Turno" value="<?= $Especialistas3->Turno ?? '' ?>">
                </div>

                <div class="col-md-6">
                    <label for="exampleFormControlInput1">Cajero</label>
                    <input type="text" class="form-control" id="cantidadtotalventassss" name="Cajero" step="any" readonly 
                           value="<?= htmlspecialchars($Especialistas3->AgregadoPor ?? 'N/A') ?>" aria-describedby="basic-addon1">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="exampleFormControlInput1">Total de venta</label>
                    <input type="number" class="form-control" id="cantidadtotalventassss" name="VentaTotal" step="any" readonly 
                           value="<?= $Especialistas3->VentaTotal ?? 0 ?>" aria-describedby="basic-addon1">
                </div>

                <div class="col-md-6">
                    <label for="exampleFormControlInput1">Total de tickets</label>
                    <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotal" step="any" readonly 
                           value="<?= $Especialistas3->Total_tickets ?? 0 ?>" aria-describedby="basic-addon1">
                </div>
            </div>
        </div>

      <!-- Mostrar la tabla de servicios si hay datos -->
<?php if (!empty($Especialistas14)) : ?>
<div class="table-responsive">
    <table id="TotalesGeneralesCortes" class="table table-hover">
        <thead>
            <tr>
                <th>Nombre Servicio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($Especialistas14 as $especialista) : ?>
            <tr>
                <td><input type="text" class="form-control" readonly value="<?= htmlspecialchars($especialista->Nom_Serv) ?>"></td>
                <td><input type="text" class="form-control" readonly value="<?= $especialista->totaldeservicios ?? 0 ?>"></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p class="alert alert-danger">No se encontraron servicios para mostrar.</p>
<?php endif; ?>

<!-- Campo oculto con el valor de servicios -->
<input type="hidden" name="servicios" value='<?= json_encode($servicios) ?>'>

        <!-- Tabla de totales -->
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
                            <td><input type="text" class="form-control" name="EfectivoTotal" readonly value="<?= $totalPagosEnEfectivo ?>"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" readonly value="Tarjeta"></td>
                            <td><input type="text" class="form-control" name="TarjetaTotal" readonly value="<?= $totalPagosEnTarjeta ?>"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" readonly value="Créditos"></td>
                            <td><input type="text" class="form-control" name="CreditosTotales" readonly value="<?= $totalPagosEnCreditos ?>"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" readonly value="Transferencia"></td>
                            <td><input type="text" class="form-control" name="TotalTransferencias" readonly value="<?= $totalPagosEnTransferencia ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Campos ocultos y observaciones -->
        <input type="hidden" name="Sistema" value="Ventas">
        <input type="hidden" name="ID_H_O_D" value="DoctorPez">
        
        <label for="comentarios">Observaciones:</label>
        <textarea class="form-control" id="comentarios" name="comentarios" rows="4" cols="50" placeholder="Escribe tu comentario aquí..."></textarea>
        <br>
        
        <!-- Botón de realizar corte -->
        <button type="submit" id="submit" class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
    </div>
</form>

<script src="js/RealizaCorteDeCaja.js"></script>
<script>
document.getElementById('FormDeCortes').addEventListener('submit', function(e) {
    const total = <?= $TotalCantidad ?>;
    
    if (total <= 0) {
        if (!confirm('¡ADVERTENCIA! Estás realizando un corte con $0 en ventas. ¿Deseas continuar?')) {
            e.preventDefault();
            return;
        }
    }
    
    // Aquí iría la lógica original de envío
});
</script>