<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $idLote = $_GET['id'] ?? null;
    
    if (!$idLote) {
        throw new Exception('ID de lote requerido');
    }
    
    // Obtener detalles del lote
    $sql = "SELECT 
                plc.*,
                s.Nombre_Sucursal as sucursal,
                DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes
            FROM productos_lotes_caducidad plc
            LEFT JOIN Sucursales s ON plc.sucursal_id = s.Id_Sucursal
            WHERE plc.id_lote = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $idLote);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Lote no encontrado');
    }
    
    $lote = $result->fetch_assoc();
    
    // Obtener historial del lote
    $sqlHistorial = "SELECT 
                        tipo_movimiento,
                        cantidad_anterior,
                        cantidad_nueva,
                        fecha_caducidad_anterior,
                        fecha_caducidad_nueva,
                        sucursal_origen,
                        sucursal_destino,
                        usuario_movimiento,
                        fecha_movimiento,
                        observaciones
                    FROM caducados_historial
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