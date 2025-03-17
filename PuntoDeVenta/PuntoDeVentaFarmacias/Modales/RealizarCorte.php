<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = $_POST['id'] ?? null;
$fk_sucursal = $_POST['fk_sucursal'] ?? null;
$id_h_o_d = $_POST['id_h_o_d'] ?? null;

// Validación de parámetros obligatorios
if (!$fk_caja || !$fk_sucursal || !$id_h_o_d) {
    die("Faltan parámetros necesarios.");
}

// Inicializar todas las variables con valores por defecto
$defaults = [
    'VentaTotal' => 0,
    'Total_tickets' => 0,
    'AgregadoPor' => 'N/A',
    'Turno' => '',
    'Nombre_Sucursal' => 'Sucursal no especificada',
    'totaldeservicios' => 0,
    'VentaTotalCredito' => 0,
    'VentaTotalCreditoLimpieza' => 0,
    'VentaTotalCreditoFarmaceutico' => 0,
    'VentaTotalCreditoMedicos' => 0
];

// CONSULTA 1 - Primer ticket (Manteniendo estructura original)
$sql1 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID ASC LIMIT 1";
$query1 = $conn->query($sql1);
$Especialistas = $query1 && $query1->num_rows > 0 ? $query1->fetch_object() : null;

// CONSULTA 2 - Último ticket (Manteniendo estructura original)
$sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID DESC LIMIT 1";
$query2 = $conn->query($sql2);
$Especialistas2 = $query2 && $query2->num_rows > 0 ? $query2->fetch_object() : null;

// CONSULTA 3 - Totales generales (Agregando COALESCE)
$sql3 = "SELECT 
            COALESCE(COUNT(DISTINCT Folio_Ticket), 0) AS Total_tickets,
            COALESCE(SUM(Importe), 0) AS VentaTotal,
            COALESCE(AgregadoPor, 'N/A') AS AgregadoPor,
            COALESCE(Turno, '') AS Turno
         FROM Ventas_POS 
         WHERE Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query3 = $conn->query($sql3);
$Especialistas3 = $query3 && $query3->num_rows > 0 ? $query3->fetch_object() : (object)$defaults;

// CONSULTA 14 - Servicios (Manteniendo estructura original con COALESCE)
$sql14 = "SELECT 
            COALESCE(Servicios_POS.Servicio_ID, '0000') AS Servicio_ID, 
            COALESCE(Servicios_POS.Nom_Serv, 'No tiene servicio especificado') AS Nom_Serv, 
            Ventas_POS.Fk_sucursal, 
            Ventas_POS.ID_H_O_D, 
            Ventas_POS.Fecha_venta,
            Ventas_POS.AgregadoPor, 
            Ventas_POS.Fk_Caja, 
            Ventas_POS.Turno, 
            Ventas_POS.AgregadoEl, 
            Sucursales.ID_Sucursal, 
            COALESCE(Sucursales.Nombre_Sucursal, 'Sucursal no especificada') AS Nombre_Sucursal, 
            COALESCE(SUM(Ventas_POS.Importe), 0) AS totaldeservicios 
         FROM Ventas_POS
         LEFT JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         INNER JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
         WHERE Ventas_POS.Fk_Caja = '$fk_caja' 
         AND Ventas_POS.ID_H_O_D = '$id_h_o_d'
         GROUP BY Servicio_ID, Nom_Serv";
$query14 = $conn->query($sql14);
$Especialistas14 = $query14 && $query14->num_rows > 0 ? $query14->fetch_all(MYSQLI_ASSOC) : [];

// Consultas de créditos (6,7,11,12 - Manteniendo estructura original)
$creditos = [
    6 => ['forma' => 'Crédito Enfermería', 'var' => 'Especialistas6'],
    7 => ['forma' => 'Crédito Limpieza', 'var' => 'Especialistas7'],
    11 => ['forma' => 'Crédito Farmacéutico', 'var' => 'Especialistas11'],
    12 => ['forma' => 'Crédito Médico', 'var' => 'Especialistas12']
];

foreach ($creditos as $num => $config) {
    $sql = "SELECT COALESCE(SUM(Importe), 0) AS VentaTotalCredito 
            FROM Ventas_POS 
            WHERE FormaDePago = '{$config['forma']}'
            AND Fk_sucursal = '$fk_sucursal' 
            AND ID_H_O_D = '$id_h_o_d' 
            AND Fk_Caja = '$fk_caja'";
    $query = $conn->query($sql);
    ${$config['var']} = $query && $query->num_rows > 0 ? $query->fetch_object() : (object)['VentaTotalCredito' => 0];
}

// CONSULTA 13 - Cortes de cajas (Manteniendo estructura original)
$sql13 = "SELECT * FROM Cortes_Cajas_POS 
          WHERE Sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query13 = $conn->query($sql13);
$Especialistas13 = $query13 && $query13->num_rows > 0 ? $query13->fetch_object() : null;

// Consulta de totales (Manteniendo estructura original con COALESCE)
$sql_totales = "SELECT 
    COALESCE(SUM(CASE WHEN FormaDePago = 'Efectivo' THEN Importe ELSE 0 END), 0) AS totalesdepagoEfectivo,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Tarjeta' THEN Importe ELSE 0 END), 0) AS totalesdepagotarjeta,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Efectivo y Tarjeta' THEN Pagos_tarjeta ELSE 0 END), 0) AS complementoTarjeta,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Efectivo y Tarjeta' THEN Importe - Pagos_tarjeta ELSE 0 END), 0) AS complementoEfectivo,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Efectivo y Crédito' THEN Importe ELSE 0 END), 0) AS complementoCreditoEfectivo,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Crédito' THEN Importe ELSE 0 END), 0) AS totalCredito,
    COALESCE(SUM(CASE WHEN FormaDePago = 'Transferencia' THEN Importe ELSE 0 END), 0) AS totalTransferencia,
    COALESCE(SUM(Importe), 0) AS TotalCantidad
FROM Ventas_POS 
WHERE Fk_Caja = '$fk_caja' 
AND Fk_sucursal = '$fk_sucursal' 
AND ID_H_O_D = '$id_h_o_d'";

$result_totales = $conn->query($sql_totales);
$row_totales = $result_totales ? $result_totales->fetch_assoc() : [];
?>

<!-- Manteniendo estructura HTML original con validaciones -->
<form action="javascript:void(0)" method="post" id="FormDeCortes">
    <div class="text-center">
        <h5 class="text-center mt-3">Datos de caja</h5> 
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <label>Sucursal</label>
                    <input type="text" class="form-control" readonly 
                    value="<?= htmlspecialchars($defaults['Nombre_Sucursal']) ?>">
                    <input type="hidden" name="Fk_Caja" value="<?= htmlspecialchars($fk_caja) ?>">
                    <input type="hidden" name="Sucursal" value="<?= htmlspecialchars($fk_sucursal) ?>">
                    <input type="hidden" name="Turno" value="<?= htmlspecialchars($Especialistas3->Turno ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label>Cajero</label>
                    <input type="text" class="form-control" readonly 
                        value="<?= htmlspecialchars($Especialistas3->AgregadoPor ?? $defaults['AgregadoPor']) ?>">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Total de venta</label>
                    <input type="number" class="form-control" readonly 
                        value="<?= htmlspecialchars($Especialistas3->VentaTotal ?? $defaults['VentaTotal']) ?>">
                </div>

                <div class="col-md-6">
                    <label>Total de tickets</label>
                    <input type="text" class="form-control" readonly 
                        value="<?= htmlspecialchars($Especialistas3->Total_tickets ?? $defaults['Total_tickets']) ?>">
                </div>
            </div>
        </div>

        <!-- Tabla de servicios (Manteniendo estructura original) -->
        <?php if (!empty($Especialistas14)) : ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Servicio</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($Especialistas14 as $servicio) : ?>
                    <tr>
                        <td><?= htmlspecialchars($servicio['Nom_Serv']) ?></td>
                        <td>$<?= number_format($servicio['totaldeservicios'] ?? 0, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="alert alert-info">No se encontraron servicios</p>
        <?php endif; ?>

        <!-- Tabla de totales (Manteniendo estructura original) -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Forma de pago</th><th>Total</th></tr></thead>
                <tbody>
                    <tr>
                        <td>Efectivo</td>
                        <td>$<?= number_format(($row_totales['totalesdepagoEfectivo'] ?? 0) + ($row_totales['complementoEfectivo'] ?? 0), 2) ?></td>
                    </tr>
                    <tr>
                        <td>Tarjeta</td>
                        <td>$<?= number_format(($row_totales['totalesdepagotarjeta'] ?? 0) + ($row_totales['complementoTarjeta'] ?? 0), 2) ?></td>
                    </tr>
                    <tr>
                        <td>Créditos</td>
                        <td>$<?= number_format(($row_totales['totalCredito'] ?? 0) + ($row_totales['complementoCreditoEfectivo'] ?? 0), 2) ?></td>
                    </tr>
                    <tr>
                        <td>Transferencias</td>
                        <td>$<?= number_format($row_totales['totalTransferencia'] ?? 0, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Campos ocultos y créditos especiales (Manteniendo estructura original) -->
        <input type="hidden" name="servicios" value='<?= json_encode($Especialistas14) ?>'>
        <input type="hidden" name="Sistema" value="Ventas">
        <input type="hidden" name="ID_H_O_D" value="DoctorPez">

        <!-- Sección de créditos (Manteniendo estructura original) -->
        <div class="row mt-3">
            <div class="col-md-3">
                <label>Crédito Enfermería</label>
                <input type="text" class="form-control" readonly 
                    value="<?= $Especialistas6->VentaTotalCredito ?? 0 ?>">
            </div>
            <div class="col-md-3">
                <label>Crédito Limpieza</label>
                <input type="text" class="form-control" readonly 
                    value="<?= $Especialistas7->VentaTotalCreditoLimpieza ?? 0 ?>">
            </div>
            <div class="col-md-3">
                <label>Crédito Farmacéutico</label>
                <input type="text" class="form-control" readonly 
                    value="<?= $Especialistas11->VentaTotalCreditoFarmaceutico ?? 0 ?>">
            </div>
            <div class="col-md-3">
                <label>Crédito Médico</label>
                <input type="text" class="form-control" readonly 
                    value="<?= $Especialistas12->VentaTotalCreditoMedicos ?? 0 ?>">
            </div>
        </div>

        <textarea class="form-control mt-3" name="comentarios" placeholder="Observaciones..."></textarea>
        <!-- <button type="submit" class="btn btn-warning mt-3" <?= ($row_totales['TotalCantidad'] ?? 0) <= 0 ? 'disabled' : '' ?>>
            Realizar corte <i class="fas fa-money-check-alt"></i>
        </button> -->
        <button type="submit" class="btn btn-warning mt-3" id="btnRealizarCorte">
    Realizar corte <i class="fas fa-money-check-alt"></i>
</button>
    </div>
</form>

<!-- Manteniendo referencia original al JS -->
<script src="js/RealizaCorteDeCaja.js"></script>