<?php
include "../Controladores/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos enviados por AJAX
    $FkCaja = $_POST['FkCaja'];
    $Turno = $_POST['Turno'];
    $SaldoPrevio = $_POST['AbonoPendiente'];
    $Abono = $_POST['Abono'];
    $CobradoPor = $_POST['CobradoPor'];
    $FormaPago = $_POST['FormaPago'];
    $NumTicket = $_POST['NumTicket'];
    $TicketNuevo = $_POST['TicketNuevo'];

    // Formato de fecha y hora
    $FechaHora = date("Y-m-d H:i:s");

    // Inserción en la tabla de liquidaciones
    $sql = "INSERT INTO AbonosCreditosLiquidaciones 
        (FkCaja, Turno, SaldoPrevio, Abono, CobradoPor, FormaPago, NumTicket, TicketNuevo, FechaHora) 
        VALUES 
        ('$FkCaja', '$Turno', '$SaldoPrevio', '$Abono', '$CobradoPor', '$FormaPago', '$NumTicket', '$TicketNuevo', '$FechaHora')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Liquidación registrada con éxito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
?>
