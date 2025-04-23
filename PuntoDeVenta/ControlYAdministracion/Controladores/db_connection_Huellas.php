<?php

/* Database connection start */
$servername = "srv1264.hstgr.io:3306";
$username = "u858848268_HuellasPez";
$password = "4cOJmVNhs~A";
$dbname = "u858848268_SistemaHuellas";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("No podemos conectar a la base de datos: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("algo salio mal, no podemos conectarnos a la base de datos %s\n", mysqli_connect_error());
    exit();
}
$sqlSetTimeZone = "SET time_zone = '-6:00'";
mysqli_query($conn, $sqlSetTimeZone);
?>