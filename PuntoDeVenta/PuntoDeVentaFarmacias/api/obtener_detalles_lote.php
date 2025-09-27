<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $idLote = $_GET['id'] ?? null;
    
    if (!$idLote) {
        throw new Exception('ID de lote requerido');
    }
    
    // Obtener detalles del lote
    $sqlLote = "SELECT plc.*, s.nombre as sucursal_nombre,
                DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes
                FROM productos_lotes_caducidad plc
                LEFT JOIN Sucursales s ON plc.sucursal_id = s.id
                WHERE plc.id_lote = ?";
    
    $stmtLote = $con->prepare($sqlLote);
    $stmtLote->bind_param("i", $idLote);
    $stmtLote->execute();
    $resultLote = $stmtLote->get_result();
    
    if ($resultLote->num_rows === 0) {
        throw new Exception('Lote no encontrado');
    }
    
    $lote = $resultLote->fetch_assoc();
    
    // Obtener historial del lote
    $sqlHistorial = "SELECT * FROM caducados_historial 
                     WHERE id_lote = ? 
                     ORDER BY fecha_movimiento DESC";
    
    $stmtHistorial = $con->prepare($sqlHistorial);
    $stmtHistorial->bind_param("i", $idLote);
    $stmtHistorial->execute();
    $resultHistorial = $stmtHistorial->get_result();
    
    $historial = [];
    while ($row = $resultHistorial->fetch_assoc()) {
        $historial[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'lote' => $lote,
        'historial' => $historial
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
