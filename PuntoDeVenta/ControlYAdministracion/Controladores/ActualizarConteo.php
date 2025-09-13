<?php
include_once "db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
$existenciaFisica = isset($_POST['existenciaFisica']) ? $_POST['existenciaFisica'] : '';
$estado = isset($_POST['estado']) ? $_POST['estado'] : '';
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

if (empty($folio) || empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

// Validar existencia física si se proporciona
if (!empty($existenciaFisica) && (!is_numeric($existenciaFisica) || $existenciaFisica < 0)) {
    echo json_encode(['success' => false, 'message' => 'La existencia física debe ser un número positivo']);
    exit;
}

$sql = "UPDATE ConteosDiarios SET 
    ExistenciaFisica = ?,
    EnPausa = ?,
    AgregadoEl = NOW()
WHERE Folio_Ingreso = ? AND Cod_Barra = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("iiis", $existenciaFisica, $estado, $folio, $codigo);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Conteo actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el conteo especificado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el conteo: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
}

$conn->close();
?>
