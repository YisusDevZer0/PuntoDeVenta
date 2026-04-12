<?php
/**
 * Conexión mysqli sin cargar config/app.php (evita 500 por DOCUMENT_ROOT en endpoints AJAX).
 * Mismas credenciales que db_connect.php del módulo.
 */
if (isset($conn) && $conn instanceof mysqli) {
    return;
}

$servername = '217.196.54.32';
$username = 'u858848268_DevelopPez';
$password = 'DevelopFDP2602';
$dbname = 'u858848268_Develop';
$port = 3306;

$conn = @mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'No podemos conectar a la base de datos: ' . mysqli_connect_error(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

mysqli_query($conn, "SET time_zone = '-6:00'");
