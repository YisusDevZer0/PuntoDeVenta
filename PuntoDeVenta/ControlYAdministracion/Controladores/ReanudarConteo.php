<?php
include_once "db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

if (empty($folio) || empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

$sql = "UPDATE ConteosDiarios SET EnPausa = 0 WHERE Folio_Ingreso = ? AND Cod_Barra = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Conteo reanudado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el conteo especificado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al reanudar el conteo: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
}

$conn->close();
?>
