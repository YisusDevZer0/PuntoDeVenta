<?php
include_once "db_connect.php";

$sql = "UPDATE ConteosDiarios SET EnPausa = 0 WHERE EnPausa = 1";

$result = $conn->query($sql);

if ($result) {
    $conteosAfectados = $conn->affected_rows;
    echo json_encode([
        'success' => true, 
        'message' => "Se reanudaron $conteosAfectados conteos correctamente",
        'conteosAfectados' => $conteosAfectados
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al reanudar los conteos: ' . $conn->error]);
}

$conn->close();
?>
