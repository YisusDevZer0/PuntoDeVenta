<?php
include_once 'db_connect.php';

$Cod_Barra = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Cod_BarraP']))));
$Clave_adicional = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clav']))));
$Clave_Levic = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clave_Levic']))));
$Nombre_Prod = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['NombreProd']))));
$Precio_Venta = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PV']))));
$Precio_C = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PC']))));
$Tipo_Servicio = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['TipoServicio']))));
$Componente_Activo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['ComponenteActivo']))));
$Tipo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tip']))));
$FkCategoria = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Categoria']))));
$FkMarca = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Marca']))));
$FkPresentacion = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Presentacion']))));
$Proveedor1 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Proveedor']))));
$Proveedor2 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Prov2']))));
$RecetaMedica = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Receta']))));
$AgregadoPor = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['AgregaProductosBy']))));
$Licencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Licencia']))));
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
}else {
    $sql = $sql = "INSERT INTO `Productos_POS`(`Cod_Barra`, `Clave_adicional`, `Clave_Levic`, `Nombre_Prod`, `Precio_Venta`, `Precio_C`, `Tipo_Servicio`, `Componente_Activo`, `Tipo`, `FkCategoria`, `FkMarca`, `FkPresentacion`, `Proveedor1`, `Proveedor2`, `RecetaMedica`, `AgregadoPor`, `AgregadoEl`, `Licencia`) 
    VALUES ('$Cod_Barra','$Clave_adicional','$Clave_Levic','$Nombre_Prod','$Precio_Venta','$Precio_C','$Tipo_Servicio','$Componente_Activo','$Tipo','$FkCategoria','$FkMarca','$FkPresentacion','$Proveedor1','$Proveedor2','$RecetaMedica','$AgregadoPor', NOW(), '$Licencia')";


    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}