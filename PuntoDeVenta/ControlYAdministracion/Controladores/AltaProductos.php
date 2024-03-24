<?php
include_once 'db_connect.php';

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