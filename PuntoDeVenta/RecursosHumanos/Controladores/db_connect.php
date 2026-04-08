<?php

require_once __DIR__ . '/../../config/app.php';

$servername = '217.196.54.32';
$username = 'u858848268_DevelopPez';
$password = 'DevelopFDP2602';
$dbname = 'u858848268_Develop';
$port = 3306;

$conn = mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$conn) {
    die('No podemos conectar a la base de datos: ' . mysqli_connect_error());
}
$sqlSetTimeZone = "SET time_zone = '-6:00'";
mysqli_query($conn, $sqlSetTimeZone);

?>
