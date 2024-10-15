<?php
include "../Controladores/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Depuración: Mostrar los valores que llegan
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $FkCaja = $_POST['FkCaja'];
    $Turno = $_POST['Turno'];
    $SaldoPrevio = $_POST['SaldoPrevio'];
    $Abono = $_POST['Abono'];
    $NuevoSaldo = $_POST['NuevoSaldo'];
    $CobradoPor = $_POST['CobradoPor'];
    $FormaPago = $_POST['FormaPago'];
    $NumTicket = $_POST['NumTicket'];
    $TicketNuevo = $_POST['TicketNuevo'];
    $Sucursal = $_POST['sucursalcobro']; 

    // Validar si se recibieron correctamente
    if (empty($SaldoPrevio) || empty($Abono) || empty($CobradoPor)) {
        echo "Error: Faltan datos. Revise los campos.";
        exit;
    }

    $FechaHora = date("Y-m-d H:i:s");

    $sql = "INSERT INTO AbonosCreditosVentas 
        (FkCaja, Turno, SaldoPrevio, Abono, NuevoSaldo, CobradoPor, FormaPago, NumTicket, TicketNuevo, FechaHora,Sucursal) 
        VALUES 
        ('$FkCaja', '$Turno', '$SaldoPrevio', '$Abono', '$NuevoSaldo', '$CobradoPor', '$FormaPago', '$NumTicket', '$TicketNuevo', '$FechaHora','$Sucursal')";

    if ($conn->query($sql) === TRUE) {
        echo "Abono registrado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Acceso denegado";
}

