<?php
    include_once("db_connect.php");
    include "ControladorUsuario.php";

// Buscar el último FolioRifa de la sucursal
$sql = "SELECT FolioRifa FROM Ventas_POS 
        WHERE Fk_sucursal='" . $row['Fk_Sucursal'] . "' 
        ORDER BY FolioRifa DESC 
        LIMIT 1";

$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$Ticketss = mysqli_fetch_assoc($resultset);

if ($Ticketss && $Ticketss['FolioRifa'] > 0) {
    $monto1 = (int) $Ticketss['FolioRifa'];
} else {
    // Si no hay registros o es 0, empezar desde 0
    $monto1 = 0;
}

$monto2 = 1;
$totalmonto = $monto1 + $monto2;
?>