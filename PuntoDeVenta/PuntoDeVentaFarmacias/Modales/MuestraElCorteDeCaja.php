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
               Servicios, Gastos 
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

// CONSULTA 2: Obtener servicios y gastos
$sqlDetalles = "SELECT Servicios, Gastos FROM Cortes_Cajas_POS WHERE Fk_Caja = ?";
$stmtDetalles = $conn->prepare($sqlDetalles);
if (!$stmtDetalles) {
    error_log("Error en la preparación de la consulta de detalles: " . $conn->error);
    echo '<p class="alert alert-danger">Error en la preparación de la consulta de detalles.</p>';
    exit;
}

$stmtDetalles->bind_param("s", $fk_caja);
$stmtDetalles->execute();
$resultDetalles = $stmtDetalles->get_result();

$servicios = [];
$gastos = [];
$totalGastos = 0;

if ($resultDetalles && $resultDetalles->num_rows > 0) {
    $rowDetalles = $resultDetalles->fetch_object();
    error_log("ID de caja consultado: " . $fk_caja);
    error_log("Gastos encontrados en BD: " . $rowDetalles->Gastos);
    
    // Procesar servicios
    if (!empty($rowDetalles->Servicios)) {
        $serviciosArray = explode(", ", $rowDetalles->Servicios);
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

    // Procesar gastos
    if (!empty($rowDetalles->Gastos)) {
        error_log("Iniciando procesamiento de gastos...");
        // Primero separar por la última coma que precede a TOTAL GASTOS
        $partes = explode(", TOTAL GASTOS:", $rowDetalles->Gastos);
        if (count($partes) === 2) {
            // Procesar el total
            $totalGastos = floatval(trim(str_replace('$', '', $partes[1])));
            error_log("Total de gastos extraído: " . $totalGastos);

            // Procesar los gastos individuales
            $gastosArray = explode(", ", $partes[0]);
            error_log("Gastos separados: " . print_r($gastosArray, true));
            
            foreach ($gastosArray as $gasto) {
                error_log("Procesando gasto: " . $gasto);
                
                // Ajustar la expresión regular para que coincida exactamente con el formato
                if (preg_match('/^([^:]+): \$([\d.]+) \(Recibe: ([^,]+), Fecha: ([^)]+)\)$/', trim($gasto), $matches)) {
                    error_log("Gasto procesado exitosamente: " . print_r($matches, true));
                    $gastos[] = [
                        'concepto' => trim($matches[1]),
                        'importe' => floatval($matches[2]),
                        'recibe' => trim($matches[3]),
                        'fecha' => trim($matches[4])
                    ];
                } else {
                    error_log("No se pudo procesar el gasto: " . $gasto);
                }
            }
        } else {
            error_log("Formato de gastos no reconocido: " . $rowDetalles->Gastos);
        }
        
        error_log("Gastos finales procesados: " . print_r($gastos, true));
        error_log("Total de gastos final: " . $totalGastos);
    } else {
        error_log("No hay gastos para procesar en la BD");
    }
} else {
    error_log("No se encontraron detalles para la caja: " . $fk_caja);
}

// Cerrar los statements
$stmt->close();
$stmtDetalles->close();

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
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <?php if (!empty($rowDetalles->Gastos)): ?>
                                    <?php 
                                    // Mostrar los gastos directamente de la BD si no se pudieron procesar
                                    $gastosArray = explode(", ", $rowDetalles->Gastos);
                                    foreach ($gastosArray as $gasto) {
                                        if (strpos($gasto, 'TOTAL GASTOS:') === false) {
                                            echo htmlspecialchars($gasto) . "<br>";
                                        }
                                    }
                                    echo "<strong>Total Gastos: $" . number_format($totalGastos, 2) . "</strong>";
                                    ?>
                                <?php else: ?>
                                    No hay gastos registrados
                                <?php endif; ?>
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
