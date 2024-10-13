<?php
include "../Controladores/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FkCaja = $_POST['FkCaja'];
    $Turno = $_POST['Turno'];
    $SaldoPrevio = $_POST['abonoPendiente'];
    $Abono = $_POST['Abonado'];
    $NuevoSaldo = $_POST['NuevoSaldo'];
    $CobradoPor = $_POST['CobradoPor'];
    $FormaPago = $_POST['FormaPago'];
    $NumTicket = $_POST['TicketAnterior'];
    $TicketNuevo = $_POST['TicketNuevo']; // Esto dependerá de tu lógica

    $FechaHora = date("Y-m-d H:i:s");

    $sql = "INSERT INTO AbonosCreditosVentas 
        (FkCaja, Turno, SaldoPrevio, Abono, NuevoSaldo, CobradoPor, FormaPago, NumTicket, TicketNuevo, FechaHora) 
        VALUES 
        ('$FkCaja', '$Turno', '$SaldoPrevio', '$Abono', '$NuevoSaldo', '$CobradoPor', '$FormaPago', '$NumTicket', '$TicketNuevo', '$FechaHora')";

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
