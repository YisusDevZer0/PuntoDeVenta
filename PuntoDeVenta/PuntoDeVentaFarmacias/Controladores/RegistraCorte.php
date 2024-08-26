<?php
include_once 'db_connect.php';

// Verificar si se recibieron todos los datos necesarios
$requiredFields = array('Sucursal', 'Turno', 'Cajero', 'VentaTotal', 'TicketVentasTotal', 'EfectivoTotal', 'TarjetaTotal', 'CreditosTotales', 'Sistema', 'ID_H_O_D');
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
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $ID_H_O_D = mysqli_real_escape_string($conn, $_POST['ID_H_O_D']);
    $FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
    $Comentarios = mysqli_real_escape_string($conn, $_POST['comentarios']);
    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Fk_Caja, Turno FROM Cortes_Cajas_POS WHERE Fk_Caja='$FkCaja' AND Turno='$Turno'";
    $resultset = mysqli_query($conn, $sql);

    if($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode" => 250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `Cortes_Cajas_POS`(`Fk_Caja`, `Empleado`, `Sucursal`, `Turno`, `TotalTickets`, `Valor_Total_Caja`, `TotalEfectivo`, `TotalTarjeta`, `TotalCreditos`, `Hora_Cierre`, `Sistema`, `ID_H_O_D`,`Comentarios`) 
                       VALUES ('$FkCaja', '$Empleado', '$Sucursal', '$Turno', '$TotalTickets', '$ValorTotalCaja', '$TotalEfectivo', '$TotalTarjeta', '$TotalCreditos', NOW(), '$Sistema', '$ID_H_O_D','$Comentarios')";

        if(mysqli_query($conn, $sql_insert)) {
            echo json_encode(array("statusCode" => 200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode" => 201, "error" => mysqli_error($conn))); // Error en la inserción
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
