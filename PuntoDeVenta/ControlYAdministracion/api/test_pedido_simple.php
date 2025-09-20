<?php
// Prueba simple de pedido
session_start();

// Verificar sesión
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    echo json_encode(['error' => 'No autorizado - Sesión no válida']);
    exit;
}

include_once "../Controladores/db_connect.php";

// Obtener ID del pedido
$pedido_id = $_REQUEST['pedido_id'] ?? '';

if (empty($pedido_id)) {
    echo json_encode(['error' => 'ID de pedido requerido']);
    exit;
}

// Convertir a entero
$pedido_id = (int)$pedido_id;

try {
    // Consulta simple para verificar que el pedido existe
    $sql = "SELECT id, folio, estado FROM pedidos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
    
    if (!$pedido) {
        echo json_encode(['error' => 'Pedido no encontrado']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'pedido' => $pedido,
        'message' => 'Pedido encontrado correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
