<?php

/* Database connection start */
$servername = "localhost";
$username = "u858848268_devpezer0";
$password = "F9+nIIOuCh8yI6wu4!08";
$dbname = "u858848268_doctorpez";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("No podemos conectar a la base de datos: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("algo salio mal, no podemos conectarnos a la base de datos %s\n", mysqli_connect_error());
    exit();
}

?>