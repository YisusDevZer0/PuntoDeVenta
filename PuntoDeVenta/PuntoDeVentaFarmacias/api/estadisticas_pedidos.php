<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal_id = $row['Fk_Sucursal'];
    
    // Estadísticas de pedidos por estado
    $sql = "SELECT 
                estado,
                COUNT(*) as total,
                SUM(total_estimado) as total_monto
            FROM pedidos 
            WHERE sucursal_id = ? 
            AND tipo_origen = 'farmacia'
            GROUP BY estado";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $estadisticas = [
        'pendientes' => 0,
        'aprobados' => 0,
        'en_proceso' => 0,
        'completados' => 0,
        'cancelados' => 0,
        'total_monto' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $estado = $row['estado'];
        $total = intval($row['total']);
        $monto = floatval($row['total_monto']);
        
        switch ($estado) {
            case 'pendiente':
                $estadisticas['pendientes'] = $total;
                break;
            case 'aprobado':
                $estadisticas['aprobados'] = $total;
                break;
            case 'en_proceso':
                $estadisticas['en_proceso'] = $total;
                break;
            case 'completado':
                $estadisticas['completados'] = $total;
                break;
            case 'cancelado':
                $estadisticas['cancelados'] = $total;
                break;
        }
        
        $estadisticas['total_monto'] += $monto;
    }
    
    echo json_encode([
        'success' => true,
        'estadisticas' => $estadisticas,
        'sucursal' => $row['Nombre_Sucursal']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
?>
