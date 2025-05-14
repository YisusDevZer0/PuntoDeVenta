<?php
include_once 'db_connect.php';

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
    $ValorTotalCaja = mysqli_real_escape_string($conn, $_POST['VentaTotal']);
    $TotalTickets = mysqli_real_escape_string($conn, $_POST['TicketVentasTotal']);
    $TotalEfectivo = mysqli_real_escape_string($conn, $_POST['EfectivoTotal']);
    $TotalTarjeta = mysqli_real_escape_string($conn, $_POST['TarjetaTotal']);
    $TotalCreditos = mysqli_real_escape_string($conn, $_POST['CreditosTotales']);
    $TotalTransferencias= mysqli_real_escape_string($conn, $_POST['TotalTransferencias']);
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $ID_H_O_D = mysqli_real_escape_string($conn, $_POST['ID_H_O_D']);
    $FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
    $Comentarios = mysqli_real_escape_string($conn, $_POST['comentarios']);

    // Decodificar el JSON de servicios
    $servicios = isset($_POST['servicios']) ? json_decode($_POST['servicios'], true) : [];
    // Decodificar el JSON de gastos
    $gastos = isset($_POST['gastos']) ? json_decode($_POST['gastos'], true) : [];

    // Concatenar servicios en un string
    $serviciosString = '';
    foreach ($servicios as $servicio) {
        $nombreServicio = mysqli_real_escape_string($conn, $servicio['nombre']);
        $totalServicio = mysqli_real_escape_string($conn, $servicio['total']);
        $serviciosString .= "$nombreServicio: $totalServicio, "; // Agregar al string
    }
    $serviciosString = rtrim($serviciosString, ', '); // Eliminar la última coma y espacio

    // Concatenar gastos en un string
    $gastosString = '';
    if (isset($gastos['detalle']) && is_array($gastos['detalle'])) {
        foreach ($gastos['detalle'] as $gasto) {
            $concepto = mysqli_real_escape_string($conn, $gasto['concepto']);
            $importe = mysqli_real_escape_string($conn, $gasto['importe']);
            $recibe = mysqli_real_escape_string($conn, $gasto['recibe']);
            $fecha = mysqli_real_escape_string($conn, $gasto['fecha']);
            $gastosString .= "$concepto: $$importe (Recibe: $recibe, Fecha: $fecha), ";
        }
        // Agregar el total al final del string
        $totalGastos = isset($gastos['total']) ? mysqli_real_escape_string($conn, $gastos['total']) : 0;
        $gastosString .= "TOTAL GASTOS: $$totalGastos";
    }
    $gastosString = rtrim($gastosString, ', '); // Eliminar la última coma y espacio

    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Fk_Caja, Turno FROM Cortes_Cajas_POS WHERE Fk_Caja='$FkCaja' AND Turno='$Turno'";
    $resultset = mysqli_query($conn, $sql);

    if ($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode" => 250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `Cortes_Cajas_POS`(`Fk_Caja`, `Empleado`, `Sucursal`, `Turno`, `TotalTickets`, `Valor_Total_Caja`, `TotalEfectivo`, `TotalTarjeta`, `TotalCreditos`, `TotalTransferencias`, `Hora_Cierre`, `Sistema`, `ID_H_O_D`, `Comentarios`, `Servicios`, `Gastos`) 
                       VALUES ('$FkCaja', '$Empleado', '$Sucursal', '$Turno', '$TotalTickets', '$ValorTotalCaja', '$TotalEfectivo', '$TotalTarjeta', '$TotalCreditos', '$TotalTransferencias', NOW(), '$Sistema', '$ID_H_O_D', '$Comentarios', '$serviciosString', '$gastosString')";

        if (mysqli_query($conn, $sql_insert)) {
            echo json_encode(array("statusCode" => 200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode" => 201, "error" => mysqli_error($conn))); // Error en la inserción
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
