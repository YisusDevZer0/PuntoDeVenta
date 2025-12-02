<?php
// Buscar el último FolioRifa de la sucursal
$sqlRifa = "SELECT FolioRifa FROM Ventas_POS 
        WHERE Fk_sucursal='" . $row['Fk_Sucursal'] . "' 
        ORDER BY FolioRifa DESC 
        LIMIT 1";

$resultsetRifa = mysqli_query($conn, $sqlRifa) or die("database error:" . mysqli_error($conn));
$RifaResult = mysqli_fetch_assoc($resultsetRifa);

if ($RifaResult && $RifaResult['FolioRifa'] > 0) {
    $montoRifa = (int) $RifaResult['FolioRifa'];
} else {
    // Si no hay registros o es 0, empezar desde 0
    $montoRifa = 0;
}

$totalmontoRifa = $montoRifa + 1;
?>