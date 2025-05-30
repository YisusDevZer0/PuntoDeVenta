<?php

/* Database connection start */
$servername = "localhost";
$username = "u858848268_devpezer0";
$password = "F9+nIIOuCh8yI6wu4!08";
$dbname = "u858848268_doctorpez";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'No podemos conectar a la base de datos: ' . mysqli_connect_error()]);
    exit;
}
// Establecer la zona horaria
$sqlSetTimeZone = "SET time_zone = '-6:00'";
mysqli_query($conn, $sqlSetTimeZone);

// Resto de tu código aquí...
?>