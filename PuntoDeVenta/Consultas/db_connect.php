<?php

/* Database connection start */
$servername = "localhost";
$username = "somosgr1_SHWEB";
$password = "yH.0a-v?T*1R";
$dbname = "somosgr1_Sistema_Hospitalario";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("No podemos conectar a la base de datos: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("algo salio mal, no podemos conectarnos a la base de datos %s\n", mysqli_connect_error());
    exit();
}

?>