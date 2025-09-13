<?php
include_once "db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
$existenciaFisica = isset($_POST['existenciaFisica']) ? $_POST['existenciaFisica'] : '';

if (empty($folio) || empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

// Si no se proporciona existencia física, obtenerla de la base de datos
if (empty($existenciaFisica)) {
    $sqlSelect = "SELECT ExistenciaFisica FROM ConteosDiarios WHERE Folio_Ingreso = ? AND Cod_Barra = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    if ($stmtSelect) {
        $stmtSelect->bind_param("is", $folio, $codigo);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        if ($row = $result->fetch_assoc()) {
            $existenciaFisica = $row['ExistenciaFisica'];
        }
        $stmtSelect->close();
    }
}

// Verificar que existe la existencia física
if ($existenciaFisica === null || $existenciaFisica === '') {
    echo json_encode(['success' => false, 'message' => 'No se puede finalizar un conteo sin existencia física registrada']);
    exit;
}

$sql = "UPDATE ConteosDiarios SET EnPausa = 0 WHERE Folio_Ingreso = ? AND Cod_Barra = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Conteo finalizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el conteo especificado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al finalizar el conteo: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
}

$conn->close();
?>
