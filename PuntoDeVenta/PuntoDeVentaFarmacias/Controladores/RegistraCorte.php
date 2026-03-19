<?php
include_once 'db_connect.php';

/**
 * Normaliza montos recibidos desde formularios:
 * - Elimina símbolos de moneda/espacios
 * - Soporta formatos con coma o punto como separador decimal
 * - Devuelve float seguro para guardar en BD
 */
function normalizarMonto($valor)
{
    if ($valor === null || $valor === '') {
        return 0.0;
    }

    $valor = trim((string)$valor);
    $valor = str_replace(['$', ' '], '', $valor);

    // Si tiene coma y punto, asumimos que la coma es separador de miles.
    if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
        $valor = str_replace(',', '', $valor);
    } elseif (strpos($valor, ',') !== false) {
        // Si solo tiene coma, la tratamos como separador decimal.
        $valor = str_replace(',', '.', $valor);
    }

    // Limpieza final: permitir solo dígitos, punto y signo negativo.
    $valor = preg_replace('/[^0-9.\-]/', '', $valor);

    if ($valor === '' || $valor === '-' || !is_numeric($valor)) {
        return 0.0;
    }

    return (float)$valor;
}

// Verificar si se recibieron todos los datos necesarios
$requiredFields = array('Sucursal', 'Turno', 'Cajero', 'VentaTotal', 'TicketVentasTotal', 'EfectivoTotal', 'TarjetaTotal', 'CreditosTotales', 'Sistema', 'ID_H_O_D', 'servicios', 'gastos');
$missingFields = array();
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $errorMessage = "Faltan los siguientes campos: " . implode(', ', $missingFields);
    echo json_encode(array("statusCode" => 500, "error" => $errorMessage)); // Error de datos faltantes
} else {
    // Escapar y asignar los valores recibidos
    $Sucursal = mysqli_real_escape_string($conn, $_POST['Sucursal']);
    $Turno = mysqli_real_escape_string($conn, $_POST['Turno']);
    $Empleado = mysqli_real_escape_string($conn, $_POST['Cajero']);
    $ValorTotalCaja = normalizarMonto($_POST['VentaTotal'] ?? 0);
    $TotalTickets = (int)($_POST['TicketVentasTotal'] ?? 0);
    $TotalEfectivo = normalizarMonto($_POST['EfectivoTotal'] ?? 0);
    $TotalTarjeta = normalizarMonto($_POST['TarjetaTotal'] ?? 0);
    $TotalCreditos = normalizarMonto($_POST['CreditosTotales'] ?? 0);
    $TotalTransferencias = normalizarMonto($_POST['TotalTransferencias'] ?? 0);
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $ID_H_O_D = mysqli_real_escape_string($conn, $_POST['ID_H_O_D']);
    $FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
    $Comentarios = mysqli_real_escape_string($conn, $_POST['comentarios']);

    // Decodificar el JSON de servicios
    $servicios = isset($_POST['servicios']) ? json_decode($_POST['servicios'], true) : [];

    // Concatenar servicios en un string
    $serviciosString = '';
    foreach ($servicios as $servicio) {
        $nombreServicio = mysqli_real_escape_string($conn, $servicio['nombre']);
        $totalServicio = mysqli_real_escape_string($conn, $servicio['total']);
        $serviciosString .= "$nombreServicio: $totalServicio, "; // Agregar al string
    }
    $serviciosString = rtrim($serviciosString, ', '); // Eliminar la última coma y espacio

    // Decodificar y guardar datos del acordeón como JSON
    $gastos = isset($_POST['gastos']) ? $_POST['gastos'] : '';
    $abonos = isset($_POST['abonos']) ? $_POST['abonos'] : '';
    $encargos = isset($_POST['encargos']) ? $_POST['encargos'] : '';
    $pagos_servicios = isset($_POST['pagos_servicios']) ? $_POST['pagos_servicios'] : '';
    $desglose_total_json = isset($_POST['desglose_total_json']) ? $_POST['desglose_total_json'] : '';
    
    // Combinar gastos con desglose total y pagos de servicios en un solo JSON
    $gastosArray = json_decode($gastos, true);
    if (!is_array($gastosArray)) {
        $gastosArray = [];
    }
    
    // Agregar pagos de servicios al array de gastos
    if (!empty($pagos_servicios)) {
        $pagosServiciosArray = json_decode($pagos_servicios, true);
        if (is_array($pagosServiciosArray)) {
            $gastosArray['pagos_servicios'] = $pagosServiciosArray;
        }
    }
    
    // Agregar desglose total al array de gastos
    if (!empty($desglose_total_json)) {
        $desgloseArray = json_decode($desglose_total_json, true);
        if (is_array($desgloseArray)) {
            $gastosArray['desglose_total'] = $desgloseArray;
        }
    }
    
    // Escapar los JSON strings para evitar problemas con comillas
    $gastosString = mysqli_real_escape_string($conn, json_encode($gastosArray));
    $abonosString = mysqli_real_escape_string($conn, $abonos);
    $encargosString = mysqli_real_escape_string($conn, $encargos);

    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Fk_Caja, Turno FROM Cortes_Cajas_POS WHERE Fk_Caja='$FkCaja' AND Turno='$Turno'";
    $resultset = mysqli_query($conn, $sql);

    if ($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode" => 250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `Cortes_Cajas_POS`
            (`Fk_Caja`, `Empleado`, `Sucursal`, `Turno`, `TotalTickets`, `Valor_Total_Caja`, `TotalEfectivo`, `TotalTarjeta`, `TotalCreditos`, `TotalTransferencias`, `Hora_Cierre`, `Sistema`, `ID_H_O_D`, `Comentarios`, `Servicios`, `Gastos`, `Abonos`, `Encargos`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        if (!$stmt_insert) {
            echo json_encode(array("statusCode" => 201, "error" => $conn->error));
            mysqli_close($conn);
            exit;
        }

        // Tipos: Fk_Caja(s), Empleado(s), Sucursal(s), Turno(s), TotalTickets(i),
        //        ValorTotal(d), Efectivo(d), Tarjeta(d), Creditos(d), Transferencias(d),
        //        Sistema(s), ID_H_O_D(s), Comentarios(s), Servicios(s), Gastos(s), Abonos(s), Encargos(s)
        $stmt_insert->bind_param(
            "ssssidddddsssssss",
            $FkCaja,
            $Empleado,
            $Sucursal,
            $Turno,
            $TotalTickets,
            $ValorTotalCaja,
            $TotalEfectivo,
            $TotalTarjeta,
            $TotalCreditos,
            $TotalTransferencias,
            $Sistema,
            $ID_H_O_D,
            $Comentarios,
            $serviciosString,
            $gastosString,
            $abonosString,
            $encargosString
        );

        if ($stmt_insert->execute()) {
            echo json_encode(array("statusCode" => 200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode" => 201, "error" => $stmt_insert->error)); // Error en la inserción
        }

        $stmt_insert->close();
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
