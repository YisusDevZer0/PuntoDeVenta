<?php

/* Database connection start */
$servername = "localhost";
$username = "u858848268_devpezer0";
$password = "F9+nIIOuCh8yI6wu4!08";
$dbname = "u858848268_doctorpez";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("No podemos conectar a la base de datos: " . $e->getMessage());
}

?>