<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal_id = $row['Fk_Sucursal'];
    
    // Estadísticas generales usando tabla existente
    $sql = "SELECT 
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pedidos_espera,
                SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as pedidos_completados,
                SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as pedidos_cancelados,
                SUM(total_estimado) as total_ventas
            FROM pedidos 
            WHERE sucursal_id = ? AND tipo_origen = 'admin'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    
    // Total de hoy
    $sqlHoy = "SELECT SUM(total_estimado) as total_hoy
                FROM pedidos 
                WHERE sucursal_id = ? 
                  AND tipo_origen = 'admin'
                  AND DATE(fecha_creacion) = CURDATE()";
    
    $stmtHoy = $conn->prepare($sqlHoy);
    $stmtHoy->bind_param("i", $sucursal_id);
    $stmtHoy->execute();
    $resultHoy = $stmtHoy->get_result();
    $statsHoy = $resultHoy->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'total_pedidos' => $stats['total_pedidos'] ?? 0,
        'pedidos_espera' => $stats['pedidos_espera'] ?? 0,
        'pedidos_completados' => $stats['pedidos_completados'] ?? 0,
        'pedidos_cancelados' => $stats['pedidos_cancelados'] ?? 0,
        'total_ventas' => $stats['total_ventas'] ?? 0,
        'total_hoy' => $statsHoy['total_hoy'] ?? 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
?> 