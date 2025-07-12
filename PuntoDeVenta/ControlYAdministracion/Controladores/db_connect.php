<?php

/* Database connection start */
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'u858848268_devpezer0';
$password = getenv('DB_PASS') ?: 'F9+nIIOuCh8yI6wu4!08';
$dbname   = getenv('DB_NAME') ?: 'u858848268_doctorpez';
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("No podemos conectar a la base de datos: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("algo salio mal, no podemos conectarnos a la base de datos %s\n", mysqli_connect_error());
    exit();
}
// Establecer la zona horaria
$sqlSetTimeZone = "SET time_zone = '-6:00'";
mysqli_query($conn, $sqlSetTimeZone);

// Resto de tu código aquí...
?>