<?php
include_once 'db_connect.php';

// Verificar si se recibieron todos los datos necesarios
$requiredFields = array('ID_Prod_POS', 'NumFactura', 'Proveedor', 'Cod_Barra', 'Nombre_Prod', 'Fk_Sucursal', 'Contabilizado', 'Fecha_Caducidad', 'Lote', 'PrecioMaximo', 'Precio_Venta', 'Precio_C', 'PrecioVentaAutorizado', 'AgregadoPor', 'AgregadoEl', 'FechaInventario', 'Estatus', 'NumOrden', 'SolicitadoPor');
$missingFields = array();
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $errorMessage = "Faltan los siguientes campos: " . implode(', ', $missingFields);
    echo json_encode(array("statusCode"=>500, "error"=>$errorMessage)); // Error de datos faltantes
} else {
    // Escapar y asignar los valores recibidos
    $ID_Prod_POS = mysqli_real_escape_string($conn, $_POST['ID_Prod_POS']);
    $NumFactura = mysqli_real_escape_string($conn, $_POST['NumFactura']);
    $Proveedor = mysqli_real_escape_string($conn, $_POST['Proveedor']);
    $Cod_Barra = mysqli_real_escape_string($conn, $_POST['Cod_Barra']);
    $Nombre_Prod = mysqli_real_escape_string($conn, $_POST['Nombre_Prod']);
    $Fk_Sucursal = mysqli_real_escape_string($conn, $_POST['Fk_Sucursal']);
    $Contabilizado = mysqli_real_escape_string($conn, $_POST['Contabilizado']);
    $Fecha_Caducidad = mysqli_real_escape_string($conn, $_POST['Fecha_Caducidad']);
    $Lote = mysqli_real_escape_string($conn, $_POST['Lote']);
    $PrecioMaximo = mysqli_real_escape_string($conn, $_POST['PrecioMaximo']);
    $Precio_Venta = mysqli_real_escape_string($conn, $_POST['Precio_Venta']);
    $Precio_C = mysqli_real_escape_string($conn, $_POST['Precio_C']);
    $PrecioVentaAutorizado = mysqli_real_escape_string($conn, $_POST['PrecioVentaAutorizado']);
    $AgregadoPor = mysqli_real_escape_string($conn, $_POST['AgregadoPor']);
    $AgregadoEl = mysqli_real_escape_string($conn, $_POST['AgregadoEl']);
    $FechaInventario = mysqli_real_escape_string($conn, $_POST['FechaInventario']);
    $Estatus = mysqli_real_escape_string($conn, $_POST['Estatus']);
    $NumOrden = mysqli_real_escape_string($conn, $_POST['NumOrden']);
    $SolicitadoPor = mysqli_real_escape_string($conn, $_POST['SolicitadoPor']);

    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT ID_Prod_POS, NumFactura FROM IngresosAutorizados WHERE ID_Prod_POS='$ID_Prod_POS' AND NumFactura='$NumFactura'";
    $resultset = mysqli_query($conn, $sql);

    if($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode"=>250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `IngresosAutorizados`(`ID_Prod_POS`, `NumFactura`, `Proveedor`, `Cod_Barra`, `Nombre_Prod`, `Fk_Sucursal`, `Contabilizado`, `Fecha_Caducidad`, `Lote`, `PrecioMaximo`, `Precio_Venta`, `Precio_C`, `PrecioVentaAutorizado`, `AgregadoPor`, `AgregadoEl`, `FechaInventario`, `Estatus`, `NumOrden`, `SolicitadoPor`) 
                VALUES ('$ID_Prod_POS', '$NumFactura', '$Proveedor', '$Cod_Barra', '$Nombre_Prod', '$Fk_Sucursal', '$Contabilizado', '$Fecha_Caducidad', '$Lote', '$PrecioMaximo', '$Precio_Venta', '$Precio_C', '$PrecioVentaAutorizado', '$AgregadoPor', '$AgregadoEl', '$FechaInventario', '$Estatus', '$NumOrden', '$SolicitadoPor')";

        if(mysqli_query($conn, $sql_insert)) {
            echo json_encode(array("statusCode"=>200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode"=>201, "error"=>mysqli_error($conn))); // Error en la inserción
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
