<?php
    include_once("db_connect.php");
    include "ControladorUsuario.php";

// Obtener las 3 primeras letras de la sucursal para el patrón
$primeras_tres_letras = substr($row['Nombre_Sucursal'], 0, 3);
$primeras_tres_letras = strtoupper($primeras_tres_letras);

// Buscar solo tickets que sigan el formato correcto (sin fechas)
$patron_correcto = $primeras_tres_letras . '%';
$sql = "SELECT Folio_Ticket FROM Ventas_POS 
        WHERE Fk_sucursal='" . $row['Fk_Sucursal'] . "' 
        AND Folio_Ticket LIKE '$patron_correcto'
        AND Folio_Ticket NOT LIKE '%-%-%'
        AND Folio_Ticket REGEXP '^[A-Z]{3}[0-9]+$'
        ORDER BY CAST(SUBSTRING(Folio_Ticket, 4) AS UNSIGNED) DESC 
        LIMIT 1";

$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$Ticketss = mysqli_fetch_assoc($resultset);

if ($Ticketss) {
    $monto1 = obtenerValorNumerico($Ticketss['Folio_Ticket']);
} else {
    // Si no hay tickets del formato correcto, empezar desde 0
    $monto1 = 0;
}

$monto2 = 1;
$totalmonto = $monto1 + $monto2;

// Función mejorada para obtener el valor numérico solo del formato correcto
function obtenerValorNumerico($cadena) {
    // Verificar que sea el formato correcto (3 letras + números)
    if (preg_match('/^[A-Z]{3}(\d+)$/', $cadena, $matches)) {
        return (int) $matches[1];
    }
    
    // Si no es el formato esperado, buscar solo números al final
    if (preg_match('/(\d+)$/', $cadena, $matches)) {
        return (int) $matches[1];
    }
    
    return 0;
}
    
  
?>