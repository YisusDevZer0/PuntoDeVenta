<?php
include "../Controladores/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FkCaja = $_POST['FkCaja'];
    $Turno = $_POST['Turno'];
    $SaldoPrevio = $_POST['AbonoPendiente'];
    $Abono = $_POST['Abonado'];
    $NuevoSaldo = $_POST['NuevoSaldo']; // Asegúrate de calcular esto si es necesario
    $CobradoPor = $_POST['CobradoPor'];
    $FormaPago = $_POST['FormaPago'];
    $NumTicket = $_POST['TicketAnterior'];
    $TicketNuevo = $_POST['TicketNuevo']; // Esto dependerá de tu lógica

    $FechaHora = date("Y-m-d H:i:s");

    // Asegúrate de que se calculen correctamente SaldoPrevio y Abono
    $sql = "INSERT INTO AbonosCreditosLiquidaciones 
        (FkCaja, Turno, SaldoPrevio, Abono, CobradoPor, FormaPago, NumTicket, TicketNuevo, FechaHora) 
        VALUES 
        ('$FkCaja', '$Turno', '$SaldoPrevio', '$Abono', '$CobradoPor', '$FormaPago', '$NumTicket', '$TicketNuevo', '$FechaHora')";

    if ($conn->query($sql) === TRUE) {
        echo "Abono registrado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Acceso denegado";
}
?>
