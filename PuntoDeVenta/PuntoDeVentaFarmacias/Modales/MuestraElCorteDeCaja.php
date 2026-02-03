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

// Agregar depuración
error_log("ID de caja recibido: " . $fk_caja);

// CONSULTA 1: Obtener la información completa del corte
$sql = "SELECT ID_Caja, Fk_Caja, Empleado, Sucursal, Turno, TotalTickets, 
               Valor_Total_Caja, TotalEfectivo, TotalTarjeta, TotalCreditos, 
               TotalTransferencias, Hora_Cierre, Sistema, ID_H_O_D, Comentarios,
               Servicios, Gastos, Abonos, Encargos
        FROM Cortes_Cajas_POS 
        WHERE Fk_Caja = ?";

// Usar prepared statement para mayor seguridad
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error en la preparación de la consulta: " . $conn->error);
    echo '<p class="alert alert-danger">Error en la preparación de la consulta.</p>';
    exit;
}

$stmt->bind_param("s", $fk_caja);
$stmt->execute();
$result = $stmt->get_result();

$datosCorte = null;
if ($result && $result->num_rows > 0) {
    $datosCorte = $result->fetch_object();
    error_log("Datos del corte encontrados: " . print_r($datosCorte, true));
} else {
    error_log("No se encontraron datos para la caja: " . $fk_caja);
    echo '<p class="alert alert-danger">No se encontraron datos para mostrar.</p>';
    exit;
}

// Procesar datos del corte
$servicios = [];
$gastos = [];
$abonos = [];
$encargos = [];
$totalGastos = 0;
$desglose_total = null;

// Procesar servicios
if (!empty($datosCorte->Servicios)) {
    $serviciosArray = explode(", ", $datosCorte->Servicios);
    foreach ($serviciosArray as $servicio) {
        $servicioPartes = explode(": ", $servicio);
        if (count($servicioPartes) === 2) {
            $servicios[] = [
                'nombre' => trim($servicioPartes[0]),
                'total' => trim($servicioPartes[1])
            ];
        }
    }
}

// Procesar gastos (ahora viene como JSON con desglose_total)
if (!empty($datosCorte->Gastos)) {
    $gastosData = json_decode($datosCorte->Gastos, true);
    if (is_array($gastosData)) {
        // Obtener el desglose total si existe
        if (isset($gastosData['desglose_total'])) {
            $desglose_total = $gastosData['desglose_total'];
        }
        
        // Obtener los gastos del detalle
        if (isset($gastosData['detalle']) && is_array($gastosData['detalle'])) {
            $gastos = $gastosData['detalle'];
        }
        
        // Obtener el total de gastos
        if (isset($gastosData['total'])) {
            $totalGastos = floatval($gastosData['total']);
        }
    } else {
        // Formato antiguo - intentar procesar como string
        $partes = explode(", TOTAL GASTOS:", $datosCorte->Gastos);
        if (count($partes) === 2) {
            $totalGastos = floatval(trim(str_replace('$', '', $partes[1])));
            $gastosArray = explode(", ", $partes[0]);
            foreach ($gastosArray as $gasto) {
                if (preg_match('/^([^:]+): \$([\d.]+) \(Recibe: ([^,]+), Fecha: ([^)]+)\)$/', trim($gasto), $matches)) {
                    $gastos[] = [
                        'concepto' => trim($matches[1]),
                        'importe' => floatval($matches[2]),
                        'recibe' => trim($matches[3]),
                        'fecha' => trim($matches[4])
                    ];
                }
            }
        }
    }
}

// Procesar abonos
if (!empty($datosCorte->Abonos)) {
    $abonos = json_decode($datosCorte->Abonos, true);
    if (!is_array($abonos)) {
        $abonos = [];
    }
}

// Procesar encargos
if (!empty($datosCorte->Encargos)) {
    $encargos = json_decode($datosCorte->Encargos, true);
    if (!is_array($encargos)) {
        $encargos = [];
    }
}

// Cerrar el statement
$stmt->close();

// Verificación final antes de mostrar
error_log("Verificación final - Número de gastos: " . count($gastos));
error_log("Verificación final - Total de gastos: " . $totalGastos);
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

        <!-- Desglose Total -->
        <?php if ($desglose_total && is_array($desglose_total)): ?>
            <div class="text-center mt-4">
                <h5 class="text-center mb-3">Desglose Total del corte</h5>
                <div class="table-responsive">
                    <table id="DesgloseTotalCortes" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Servicios (Ventas POS)"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['servicios_pos']) ? floatval($desglose_total['servicios_pos']) : 0, 2); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Pagos de Servicios (Costo + Comisión)"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['pagos_servicios']) ? floatval($desglose_total['pagos_servicios']) : 0, 2); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Abonos a Encargos"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['abonos']) ? floatval($desglose_total['abonos']) : 0, 2); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Encargos del Día"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['encargos']) ? floatval($desglose_total['encargos']) : 0, 2); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Gastos del Día"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['gastos']) ? -floatval($desglose_total['gastos']) : 0, 2); ?>" style="color: red;"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="TOTAL GENERAL" style="font-weight: bold;"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo number_format(isset($desglose_total['total_general']) ? floatval($desglose_total['total_general']) : 0, 2); ?>" style="font-weight: bold;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

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
                                <td><?php echo htmlspecialchars($gasto['concepto']); ?></td>
                                <td>$<?php echo number_format($gasto['importe'], 2); ?></td>
                                <td><?php echo htmlspecialchars($gasto['recibe']); ?></td>
                                <td><?php echo htmlspecialchars($gasto['fecha']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-info">
                            <td colspan="1"><strong>Total Gastos:</strong></td>
                            <td colspan="3"><strong>$<?php echo number_format($totalGastos, 2); ?></strong></td>
                        </tr>
                    <?php elseif (!empty($rowDetalles->Gastos)): ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <?php echo nl2br(htmlspecialchars($rowDetalles->Gastos)); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                No hay gastos registrados
                            </td>
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
