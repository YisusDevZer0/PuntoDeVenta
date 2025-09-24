<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Consultas/db_connect.php";

try {
    $id_lote = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id_lote <= 0) {
        throw new Exception('ID de lote inválido');
    }
    
    // Obtener detalles del lote
    $sql_lote = "SELECT 
                    plc.*,
                    s.Nombre_Sucursal as sucursal,
                    DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes
                 FROM productos_lotes_caducidad plc
                 LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                 WHERE plc.id_lote = ?";
    
    $stmt_lote = $conn->prepare($sql_lote);
    $stmt_lote->bind_param("i", $id_lote);
    $stmt_lote->execute();
    $result_lote = $stmt_lote->get_result();
    
    if ($result_lote->num_rows === 0) {
        throw new Exception('Lote no encontrado');
    }
    
    $lote = $result_lote->fetch_assoc();
    
    // Obtener historial del lote
    $sql_historial = "SELECT 
                        ch.*,
                        u.Nombre_Apellidos as usuario_nombre
                      FROM caducados_historial ch
                      LEFT JOIN Usuarios_PV u ON ch.usuario_movimiento = u.Id_PvUser
                      WHERE ch.id_lote = ?
                      ORDER BY ch.fecha_movimiento DESC";
    
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->bind_param("i", $id_lote);
    $stmt_historial->execute();
    $result_historial = $stmt_historial->get_result();
    
    $historial = [];
    while ($row = $result_historial->fetch_assoc()) {
        $historial[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'lote' => $lote,
        'historial' => $historial
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
