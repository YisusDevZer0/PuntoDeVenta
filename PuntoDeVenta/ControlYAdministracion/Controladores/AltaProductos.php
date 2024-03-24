<?php
include_once 'db_connect.php';


$Cod_Barra = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['CodBarraP']))));
$Clave_adicional = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clav']))));
$Nombre_Prod = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['NombreProd']))));
$Precio_Venta = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PV']))));
$Precio_C = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PC']))));
$Tipo_Servicio = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['TipoServicio']))));
$Componente_Activo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['ComponenteActivo']))));
$Tipo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tip']))));
$FkCategoria = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Categoria']))));
$FkMarca = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Marca']))));
$FkPresentacion = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Presentacion']))));
$Proveedor1 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['RevProvee1']))));
$Proveedor2 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['RevProvee2']))));
$RecetaMedica = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Receta']))));

$AgregadoPor = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['AgregaProductosBy']))));
$Sistema = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['SistemaProductos']))));

$ID_H_O_D = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['EmpresaProductos']))));

//include database configuration file
$sql = "SELECT Cod_Barra,Nombre_Prod,Precio_Venta,Precio_C,Tipo,Tipo_Servicio,FkCategoria,FkMarca,FkPresentacion,Licencia, Proveedor2,Proveedor1, Sistema,AgregadoPor FROM Productos_POS 
    WHERE Cod_Barra='$Cod_Barra'AND Nombre_Prod='$Nombre_Prod'  AND Precio_Venta='$Precio_Venta' AND Precio_C='$Precio_C'
    AND Tipo='$Tipo' AND Tipo_Servicio='$Tipo_Servicio' AND FkCategoria='$FkCategoria' AND FkMarca='$FkMarca' AND FkPresentacion='$FkPresentacion' AND
    Proveedor1='$Proveedor1' AND  Proveedor2='$Proveedor2' ";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

//include database configuration file
if ($row && $row['Cod_Barra'] == $Cod_Barra and $row['Nombre_Prod'] == $Nombre_Prod and $row['Precio_Venta'] == $Precio_Venta and $row['Precio_C'] == $Precio_C
    and $row['Tipo'] == $Tipo AND $row['Tipo_Servicio'] == $Tipo_Servicio and $row['FkCategoria'] == $FkCategoria and $row['FkMarca'] == $FkMarca and
    $row['FkPresentacion'] == $FkPresentacion  and $row['Proveedor1'] == $Proveedor1 and $row['Proveedor2'] == $Proveedor2) {
    echo json_encode(array("statusCode" => 250));
} else {
    $sql = "INSERT INTO `Productos_POS`( `Cod_Barra`,`Clave_adicional`,`Nombre_Prod`,`Precio_Venta`,`Precio_C`,
    `Tipo_Servicio`,`Componente_Activo`,`Tipo`,`FkCategoria`,`FkMarca`,`FkPresentacion`, `Proveedor1`,`Proveedor2`,`RecetaMedica`,`Sistema`,`AgregadoPor`,`Licencia`) 
            VALUES ('$Cod_Barra','$Clave_adicional','$Nombre_Prod','$Precio_Venta','$Precio_C','$Tipo_Servicio','$Componente_Activo','$Tipo','$FkCategoria','$FkMarca','$FkPresentacion', '$Proveedor1','$Proveedor2','$RecetaMedica','$Sistema','$AgregadoPor','$Licencia')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}
?>
