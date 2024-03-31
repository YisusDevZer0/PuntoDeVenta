<?php
    include_once("db_connect.php");
    include "ControladorUsuario.php";
$sql = "SELECT * FROM Ventas_POS  WHERE Fk_sucursal='" . $row['Fk_Sucursal'] . "' ORDER BY Venta_POS_ID DESC LIMIT 1";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$Ticketss = mysqli_fetch_assoc($resultset);

$monto1 = obtenerValorNumerico($Ticketss['Folio_Ticket']);
$monto2 = 1;
$totalmonto = $monto1 + $monto2;



// Función para obtener el valor numérico de una cadena con letras antes del valor
function obtenerValorNumerico($cadena) {
    // Usamos una expresión regular para extraer los dígitos de la cadena
    preg_match_all('/\d+/', $cadena, $matches);

    // Unimos los dígitos encontrados en un solo número
    return (int) implode('', $matches[0]);
}
    
  
?>