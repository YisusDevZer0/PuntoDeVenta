<?php
include_once 'db_connect.php';

$Cod_Barra = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Cod_Barra']))));
$Clave_adicional = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clave_adicional']))));
$Clave_Levic = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clave_Levic']))));
$Nombre_Prod = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Nombre_Prod']))));
$Precio_Venta = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Precio_Venta']))));
$Precio_C = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Precio_C']))));
$Tipo_Servicio = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tipo_Servicio']))));
$Componente_Activo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Componente_Activo']))));
$Tipo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tipo']))));
$FkCategoria = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['FkCategoria']))));
$FkMarca = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['FkMarca']))));
$FkPresentacion = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['FkPresentacion']))));
$Proveedor1 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Proveedor1']))));
$Proveedor2 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Proveedor2']))));
$RecetaMedica = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['RecetaMedica']))));
$AgregadoPor = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['AgregadoPor']))));
$Licencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Licencia']))));

$Estatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($TipVigencia))));
$CodigoEstatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($CodVig))));
$Sistema = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['SistemaProductos']))));


//include database configuration file
$sql = "SELECT Cod_Barra,Nombre_Prod FROM Productos_POS 
    WHERE Cod_Barra='$Cod_Barra'AND Nombre_Prod='$Nombre_Prod' ";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));

$row = mysqli_fetch_assoc($resultset);

//include database configuration file
if ($row && $row['Cod_Barra'] == $Cod_Barra and $row['Nombre_Prod'] == $Nombre_Prod)
{
    echo json_encode(array("statusCode" => 250));
}