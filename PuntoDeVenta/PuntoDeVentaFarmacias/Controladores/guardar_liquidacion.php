<?php
include "../Controladores/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar los datos enviados desde AJAX
    $FkCaja = $_POST['FkCaja'];
    $Turno = $_POST['Turno'];
    $SaldoPrevio = $_POST['SaldoPrevio'];
    $Abono = $_POST['Abono'];
    $CobradoPor = $_POST['CobradoPor'];
    $FormaPago = $_POST['FormaPago'];
    $NumTicket = $_POST['NumTicket'];
    $TicketNuevo = $_POST['TicketNuevo'];

    // Obtener la fecha y hora actual
    $FechaHora = date("Y-m-d H:i:s");

    // Preparar la consulta SQL
    $sql = "INSERT INTO AbonosCreditosLiquidaciones 
        (FkCaja, Turno, SaldoPrevio, Abono, CobradoPor, FormaPago, NumTicket, TicketNuevo, FechaHora) 
        VALUES 
        ('$FkCaja', '$Turno', '$SaldoPrevio', '$Abono', '$CobradoPor', '$FormaPago', '$NumTicket', '$TicketNuevo', '$FechaHora')";

    // Ejecutar la consulta y verificar si se realizó correctamente
    if ($conn->query($sql) === TRUE) {
        echo "Liquidación registrada con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "Acceso denegado";
}
?>
