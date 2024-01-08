<?php
$server = 'localhost';
$username = 'u858848268_devpezer0';
$password = '?4MogZ:C*V';
$database = 'u858848268_doctorpez';

// Crear conexión
$con = mysqli_connect($server, $username, $password, $database);

// Verificar la conexión
if (!$con) {
    die('Error de conexión: ' . mysqli_connect_error());
}
else
{
    echo "Todo ok";
}
?>
