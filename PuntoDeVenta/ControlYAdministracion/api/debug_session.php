<?php
// Debug de sesión
session_start();

header('Content-Type: application/json');

echo json_encode([
    'session_status' => session_status(),
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'headers' => getallheaders()
]);
?>
