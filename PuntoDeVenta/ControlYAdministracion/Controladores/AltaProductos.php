<?php
include_once 'db_connection.php';

$TipVigencia = 'Vigente';
$CodVig = 'background-color:#2BBB1D!important;';

$Cod_Barra = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['CodBarraP']))));
$Clave_adicional = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clav']))));
$Nombre_Prod = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['NombreProd']))));
$Precio_Venta = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PV']))));
$Precio_C = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PC']))));
$Min_Existencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['MinE']))));
$Max_Existencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['MaxE']))));
$Lote_Med = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['LoteProd']))));
$Fecha_Caducidad = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['FechaCad']))));
$Stock = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['ExistenciaCedis']))));
$Tipo_Servicio = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['TipoServicio']))));
$Componente_Activo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['ComponenteActivo']))));
$Tipo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tip']))));
$FkCategoria = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Categoria']))));
$FkMarca = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Marca']))));
$FkPresentacion = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Presentacion']))));
$Proveedor1 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['RevProvee1']))));
$Proveedor2 = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['RevProvee2']))));
$RecetaMedica = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Receta']))));
$Estatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($TipVigencia))));
$CodigoEstatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($CodVig))));
$AgregadoPor = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['AgregaProductosBy']))));
$Sistema = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['SistemaProductos']))));

$ID_H_O_D = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['EmpresaProductos']))));

//include database configuration file
$sql = "SELECT Cod_Barra,Nombre_Prod,Precio_Venta,Precio_C,Max_Existencia,Min_Existencia,Tipo,Tipo_Servicio,FkCategoria,FkMarca,FkPresentacion,ID_H_O_D, Proveedor2,Proveedor1, Estatus,Sistema,AgregadoPor,Lote_Med,Fecha_Caducidad,Stock FROM Productos_POS 
    WHERE Cod_Barra='$Cod_Barra'AND Nombre_Prod='$Nombre_Prod'  AND Precio_Venta='$Precio_Venta' AND Precio_C='$Precio_C'
    AND Max_Existencia='$Max_Existencia' AND Min_Existencia='$Min_Existencia' AND Tipo='$Tipo' AND Tipo_Servicio='$Tipo_Servicio' AND FkCategoria='$FkCategoria' AND FkMarca='$FkMarca' AND FkPresentacion='$FkPresentacion' AND
    Proveedor1='$Proveedor1' AND  Proveedor2='$Proveedor2' AND ID_H_O_D='$ID_H_O_D'  AND Lote_Med='$Lote_Med' AND Fecha_Caducidad='$Fecha_Caducidad' ";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

//include database configuration file
if ($row && $row['Cod_Barra'] == $Cod_Barra and $row['Nombre_Prod'] == $Nombre_Prod and $row['Precio_Venta'] == $Precio_Venta and $row['Precio_C'] == $Precio_C
    and $row['Max_Existencia'] == $Max_Existencia and $row['Min_Existencia'] == $Min_Existencia and $row['Tipo'] == $Tipo AND $row['Tipo_Servicio'] == $Tipo_Servicio and $row['FkCategoria'] == $FkCategoria and $row['FkMarca'] == $FkMarca and
    $row['FkPresentacion'] == $FkPresentacion  and $row['Proveedor1'] == $Proveedor1 and $row['Proveedor2'] == $Proveedor2 and $row['ID_H_O_D'] == $ID_H_O_D
    and $row['Lote_Med'] == $Lote_Med and $row['Fecha_Caducidad'] == $Fecha_Caducidad and $row['Stock'] == $Stock) {
    echo json_encode(array("statusCode" => 250));
} else {
    $sql = "INSERT INTO `Productos_POS`( `Cod_Barra`,`Clave_adicional`,`Nombre_Prod`,`Precio_Venta`,`Precio_C`,`Min_Existencia`,`Max_Existencia`,
    `Lote_Med`,`Fecha_Caducidad`,`Tipo_Servicio`,`Componente_Activo`,`Tipo`,`FkCategoria`,`FkMarca`,`FkPresentacion`, `Proveedor1`,`Proveedor2`,`RecetaMedica`,`Estatus`,`CodigoEstatus`,`Sistema`,`AgregadoPor`,`ID_H_O_D`) 
            VALUES ('$Cod_Barra','$Clave_adicional','$Nombre_Prod','$Precio_Venta','$Precio_C','$Min_Existencia','$Max_Existencia',
    '$Lote_Med','$Fecha_Caducidad','$Tipo_Servicio','$Componente_Activo','$Tipo','$FkCategoria','$FkMarca','$FkPresentacion', '$Proveedor1','$Proveedor2','$RecetaMedica','$Estatus','$CodigoEstatus','$Sistema','$AgregadoPor','$ID_H_O_D')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}
?>
