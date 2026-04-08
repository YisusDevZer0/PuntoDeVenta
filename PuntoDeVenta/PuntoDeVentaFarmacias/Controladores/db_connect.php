<?php

require_once __DIR__ . '/../../config/database.php';

/* Database connection start */
$conn = fdp_db_connect(false);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'No podemos conectar a la base de datos: ' . mysqli_connect_error()]);
    exit;
}

?>
