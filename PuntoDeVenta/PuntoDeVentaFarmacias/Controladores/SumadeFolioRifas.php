<?php
// Buscar el último FolioRifa de la sucursal (convirtiendo a número para ordenar correctamente)
$sqlRifa = "SELECT FolioRifa, CAST(FolioRifa AS UNSIGNED) as FolioNumerico
        FROM Ventas_POS 
        WHERE Fk_sucursal='" . $row['Fk_Sucursal'] . "' 
        AND FolioRifa IS NOT NULL 
        AND FolioRifa != ''
        ORDER BY CAST(FolioRifa AS UNSIGNED) DESC 
        LIMIT 1";

$resultsetRifa = mysqli_query($conn, $sqlRifa) or die("database error:" . mysqli_error($conn));
$RifaResult = mysqli_fetch_assoc($resultsetRifa);

if ($RifaResult && isset($RifaResult['FolioNumerico']) && $RifaResult['FolioNumerico'] > 0) {
    $montoRifa = (int) $RifaResult['FolioNumerico'];
} else {
    // Si no hay registros o es 0, empezar desde 0
    $montoRifa = 0;
}

$totalmontoRifa = $montoRifa + 1;
?>