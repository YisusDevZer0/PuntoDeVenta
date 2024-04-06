<?php
include_once 'db_connect.php';

$Concepto_Categoria = mysqli_real_escape_string($conn, $_POST['Concepto_Categoria']);
$Importe_Total = mysqli_real_escape_string($conn, $_POST['Importe_Total']);
$Empleado = mysqli_real_escape_string($conn, $_POST['Empleado']);
$FkSucursal = mysqli_real_escape_string($conn, $_POST['Fk_sucursal']);
$FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
$Recibe = mysqli_real_escape_string($conn, $_POST['Recibe']);
$Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
$AgregadoPor = mysqli_real_escape_string($conn, $_POST['AgregadoPor']); // Corregido aquí
$Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);


