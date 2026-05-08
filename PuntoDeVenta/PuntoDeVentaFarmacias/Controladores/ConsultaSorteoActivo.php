<?php
// Controlador para verificar si hay un sorteo activo para la sucursal actual
include_once 'db_connect.php';

header('Content-Type: application/json');

$sucursal = isset($_GET['sucursal']) ? intval($_GET['sucursal']) : 0;

if ($sucursal == 0) {
    echo json_encode(['activo' => false]);
    exit;
}

// Buscar sorteo activo que aplique a esta sucursal
$sql = "SELECT s.* FROM Sorteos s
        WHERE s.Activo = 1 
        AND CURDATE() BETWEEN s.Fecha_Inicio AND s.Fecha_Fin
        AND (
            s.Aplica_Todas_Sucursales = 1
            OR EXISTS (
                SELECT 1 FROM Sorteo_Sucursales ss 
                WHERE ss.Fk_Sorteo = s.ID_Sorteo AND ss.Fk_Sucursal = ?
            )
        )
        ORDER BY s.ID_Sorteo DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sucursal);
$stmt->execute();
$result = $stmt->get_result();
$sorteo = $result->fetch_assoc();

if ($sorteo) {
    // Calcular el siguiente folio de rifa para este sorteo en esta sucursal
    $sqlFolio = "SELECT MAX(CAST(sp.FolioRifa AS UNSIGNED)) as UltimoFolio 
                 FROM Sorteo_Participaciones sp 
                 WHERE sp.Fk_Sorteo = ? AND sp.Fk_Sucursal = ?";
    $stmtFolio = $conn->prepare($sqlFolio);
    $stmtFolio->bind_param("ii", $sorteo['ID_Sorteo'], $sucursal);
    $stmtFolio->execute();
    $resFolio = $stmtFolio->get_result()->fetch_assoc();
    
    $siguienteFolio = ($resFolio && $resFolio['UltimoFolio']) 
        ? intval($resFolio['UltimoFolio']) + 1 
        : intval($sorteo['Folio_Inicio']);
    
    $stmtFolio->close();
    
    echo json_encode([
        'activo' => true,
        'sorteo' => [
            'id' => $sorteo['ID_Sorteo'],
            'nombre' => $sorteo['Nombre_Sorteo'],
            'descripcion' => $sorteo['Descripcion'],
            'prefijo' => $sorteo['Prefijo_Folio'],
            'siguiente_folio' => $siguienteFolio
        ]
    ]);
} else {
    echo json_encode(['activo' => false]);
}

$stmt->close();
mysqli_close($conn);
?>
