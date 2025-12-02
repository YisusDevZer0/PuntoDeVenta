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

// Debug: Guardar en log para verificar
$debug = "=== FOLIO RIFA DEBUG " . date("Y-m-d H:i:s") . " ===\n";
$debug .= "Último FolioRifa en BD: " . ($RifaResult ? $RifaResult['FolioRifa'] : 'ninguno') . "\n";
$debug .= "FolioNumerico: " . ($RifaResult && isset($RifaResult['FolioNumerico']) ? $RifaResult['FolioNumerico'] : 'ninguno') . "\n";
$debug .= "montoRifa: " . $montoRifa . "\n";
$debug .= "totalmontoRifa (siguiente): " . $totalmontoRifa . "\n\n";
file_put_contents(__DIR__ . "/../../debug_folio_rifa.log", $debug, FILE_APPEND);
?>