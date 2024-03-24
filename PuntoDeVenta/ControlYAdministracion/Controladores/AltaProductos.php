<?php
include_once 'db_connect.php';

$Cod_Barra = mysqli_real_escape_string($conn, $_POST['CodBarraP']);
$Clave_adicional = mysqli_real_escape_string($conn, $_POST['Clav']);
$Clave_Levic = ''; // Puede asignarse un valor si es necesario
$Nombre_Prod = mysqli_real_escape_string($conn, $_POST['NombreProd']);
$Precio_Venta = mysqli_real_escape_string($conn, $_POST['PV']);
$Precio_C = mysqli_real_escape_string($conn, $_POST['PC']);
$Tipo_Servicio = mysqli_real_escape_string($conn, $_POST['TipoServicio']);
$Componente_Activo = mysqli_real_escape_string($conn, $_POST['ComponenteActivo']);
$Tipo = mysqli_real_escape_string($conn, $_POST['Tip']);
$FkCategoria = mysqli_real_escape_string($conn, $_POST['Categoria']);
$FkMarca = mysqli_real_escape_string($conn, $_POST['Marca']);
$FkPresentacion = mysqli_real_escape_string($conn, $_POST['Presentacion']);
$Proveedor1 = mysqli_real_escape_string($conn, $_POST['Proveedor']);
$Proveedor2 = mysqli_real_escape_string($conn, $_POST['Prov2']);
$RecetaMedica = mysqli_real_escape_string($conn, $_POST['Receta']);
$Estatus = ''; // Puede asignarse un valor si es necesario
$AgregadoPor = mysqli_real_escape_string($conn, $_POST['AgregaProductosBy']);
$Licencia = mysqli_real_escape_string($conn, $_POST['EmpresaProductos']);

// Consulta para verificar la existencia de un registro duplicado
$sql_check_duplicate = "SELECT Cod_Barra, Licencia FROM Productos_POS WHERE Cod_Barra=? AND Licencia=?";
$stmt_check_duplicate = mysqli_prepare($conn, $sql_check_duplicate);
mysqli_stmt_bind_param($stmt_check_duplicate, "ss", $Cod_Barra, $Licencia);
mysqli_stmt_execute($stmt_check_duplicate);
$result_check_duplicate = mysqli_stmt_get_result($stmt_check_duplicate);

if (mysqli_num_rows($result_check_duplicate) > 0) {
    // El registro ya existe
    echo json_encode(array("statusCode" => 250));
} else {
    // Consulta de inserción para agregar un nuevo registro
    $sql_insert = "INSERT INTO `Productos_POS`(`Cod_Barra`, `Clave_adicional`, `Clave_Levic`, `Nombre_Prod`, `Precio_Venta`, `Precio_C`, `Tipo_Servicio`, `Componente_Activo`, `Tipo`, `FkCategoria`, `FkMarca`, `FkPresentacion`, `Proveedor1`, `Proveedor2`, `RecetaMedica`, `AgregadoPor`, `Licencia`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssssddddsssssssss", $Cod_Barra, $Clave_adicional, $Clave_Levic, $Nombre_Prod, $Precio_Venta, $Precio_C, $Tipo_Servicio, $Componente_Activo, $Tipo, $FkCategoria, $FkMarca, $FkPresentacion, $Proveedor1, $Proveedor2, $RecetaMedica, $AgregadoPor, $Licencia);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        // Inserción exitosa
        echo json_encode(array("statusCode" => 200));
    } else {
        // Error en la inserción
        echo json_encode(array("statusCode" => 201, "error" => mysqli_error($conn)));
    }
}

// Cerrar la conexión
mysqli_stmt_close($stmt_check_duplicate);
mysqli_stmt_close($stmt_insert);
mysqli_close($conn);
?>
