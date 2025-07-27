<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $pedido_id = isset($_POST['pedido_id']) ? intval($_POST['pedido_id']) : 0;
    
    if (!$pedido_id) {
        throw new Exception('ID de pedido requerido');
    }

    // Obtener historial del pedido
    $sql = "SELECT 
                ph.accion,
                ph.observaciones,
                ph.fecha_cambio,
                u.Nombre_Apellidos as usuario
            FROM pedido_historial ph
            LEFT JOIN Usuarios_PV u ON ph.usuario_id = u.Id_PvUser
            WHERE ph.pedido_id = ?
            ORDER BY ph.fecha_cambio DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $historial = [];
    while ($row = $result->fetch_assoc()) {
        $historial[] = [
            'accion' => $row['accion'],
            'observaciones' => $row['observaciones'],
            'fecha' => $row['fecha_cambio'],
            'usuario' => $row['usuario'] ?: 'Sistema'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'historial' => $historial
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener historial: ' . $e->getMessage()
    ]);
}
?> 