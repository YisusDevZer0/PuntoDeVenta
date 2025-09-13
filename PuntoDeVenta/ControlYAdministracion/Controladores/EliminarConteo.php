<?php
include_once "db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

if (empty($folio) || empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

$sql = "DELETE FROM ConteosDiarios WHERE Folio_Ingreso = ? AND Cod_Barra = ? AND EnPausa = 1";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Conteo eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el conteo pausado especificado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el conteo: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
}

$conn->close();
?>
