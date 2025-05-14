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

// CONSULTA 1: Obtener la información completa del corte, excepto los servicios y gastos
$sql = "SELECT ID_Caja, Fk_Caja, Empleado, Sucursal, Turno, TotalTickets, 
               Valor_Total_Caja, TotalEfectivo, TotalTarjeta, TotalCreditos, 
               TotalTransferencias, Hora_Cierre, Sistema, ID_H_O_D, Comentarios,
               Servicios, Gastos 
        FROM Cortes_Cajas_POS 
        WHERE Fk_Caja = '$fk_caja'";
$query = $conn->query($sql);

$datosCorte = null;

if ($query && $query->num_rows > 0) {
    $datosCorte = $query->fetch_object();
} else {
    echo '<p class="alert alert-danger">No se encontraron datos para mostrar.</p>';
    exit;
}

// CONSULTA 2: Obtener servicios y gastos
$sqlDetalles = "SELECT Servicios, Gastos FROM Cortes_Cajas_POS WHERE Fk_Caja = '$fk_caja'";
$queryDetalles = $conn->query($sqlDetalles);

$servicios = [];
$gastos = [];

if ($queryDetalles && $queryDetalles->num_rows > 0) {
    $resultDetalles = $queryDetalles->fetch_object();
    
    // Procesar servicios
    if (!empty($resultDetalles->Servicios)) {
        $serviciosArray = explode(", ", $resultDetalles->Servicios);
        foreach ($serviciosArray as $servicio) {
            $servicioPartes = explode(": ", $servicio);
            if (count($servicioPartes) === 2) {
                $servicios[] = [
                    'nombre' => $servicioPartes[0],
                    'total' => $servicioPartes[1]
                ];
            }
        }
    }

    // Procesar gastos
    if (!empty($resultDetalles->Gastos)) {
        // Dividir la cadena de gastos en partes
        $gastosArray = explode(", ", $resultDetalles->Gastos);
        $totalGastos = 0;

        foreach ($gastosArray as $gasto) {
            // Verificar si es el total de gastos
            if (strpos($gasto, 'TOTAL GASTOS:') !== false) {
                $totalPartes = explode("TOTAL GASTOS: $", $gasto);
                if (count($totalPartes) === 2) {
                    $totalGastos = floatval($totalPartes[1]);
                }
                continue;
            }

            // Procesar cada gasto individual
            if (preg_match('/^(.*?): \$(\d+\.?\d*) \(Recibe: (.*?), Fecha: (.*?)\)$/', $gasto, $matches)) {
                $gastos[] = [
                    'concepto' => $matches[1],
                    'importe' => floatval($matches[2]),
                    'recibe' => $matches[3],
                    'fecha' => $matches[4]
                ];
            }
        }
    }
} else {
    echo '<p class="alert alert-danger">No se encontraron servicios para mostrar.</p>';
}
?>

<?php if ($datosCorte): ?>
    <div class="text-center">
        <h4>Mostrando el desglose del corte</h4>
        <p><strong>Sucursal:</strong> <?php echo $datosCorte->Sucursal; ?></p>
        <p><strong>Cajero:</strong> <?php echo $datosCorte->Empleado; ?></p>
        <p><strong>Total de ventas:</strong> $<?php echo number_format($datosCorte->Valor_Total_Caja, 2); ?></p>
        <p><strong>Total de tickets:</strong> <?php echo $datosCorte->TotalTickets; ?></p>

        <!-- Tabla de formas de pago -->
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
                        <td><input type="text" class="form-control" name="EfectivoTotal" readonly value="$<?php echo number_format($datosCorte->TotalEfectivo, 2); ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Tarjeta"></td>
                        <td><input type="text" class="form-control" name="TarjetaTotal" readonly value="$<?php echo number_format($datosCorte->TotalTarjeta, 2); ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Créditos"></td>
                        <td><input type="text" class="form-control" name="CreditosTotales" readonly value="$<?php echo number_format($datosCorte->TotalCreditos, 2); ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Transferencias"></td>
                        <td><input type="text" class="form-control" name="TransferenciasTotales" readonly value="$<?php echo number_format($datosCorte->TotalTransferencias, 2); ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Desglose de servicios -->
        <div class="table-responsive">
            <table id="ServiciosCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre del servicio</th>
                        <th>Total del servicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($servicio['nombre']); ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($servicio['total']); ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No hay servicios disponibles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Desglose de gastos -->
        <div class="table-responsive">
            <table id="GastosCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Importe</th>
                        <th>Recibe</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($gastos)): ?>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($gasto['concepto']); ?>"></td>
                                <td><input type="text" class="form-control" readonly value="$<?php echo number_format($gasto['importe'], 2); ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($gasto['recibe']); ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($gasto['fecha']); ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-info">
                            <td colspan="1"><strong>Total Gastos:</strong></td>
                            <td colspan="3"><strong>$<?php echo number_format($totalGastos, 2); ?></strong></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay gastos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <label for="comentarios">Observaciones:</label>
    <textarea class="form-control" id="comentarios" readonly name="comentarios" rows="4" cols="50"><?php echo htmlspecialchars($datosCorte->Comentarios); ?></textarea>
    <br>
   
    <input type="hidden" name="Sistema" value="<?php echo htmlspecialchars($datosCorte->Sistema); ?>">
    <input type="hidden" name="ID_H_O_D" value="<?php echo htmlspecialchars($datosCorte->ID_H_O_D); ?>">
<!-- 
    <button class="btn btn-primary" type="submit">Realizar corte</button> -->

<?php else: ?>
    <p class="alert alert-danger">No se encontraron datos para mostrar.</p>
<?php endif; ?>
