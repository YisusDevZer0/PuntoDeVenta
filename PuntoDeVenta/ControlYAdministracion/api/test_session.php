<?php
// Verificar sesión y autenticación
session_start();

// Configurar headers para JSON
header('Content-Type: application/json');

try {
    // Verificar si hay sesión activa
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesión no válida',
            'redirect' => 'Expiro.php'
        ]);
        exit;
    }

    // Si llegamos aquí, la sesión es válida
    echo json_encode([
        'status' => 'success',
        'message' => 'Sesión válida',
        'usuario_id' => $_SESSION['usuario_id'],
        'session_id' => session_id()
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
