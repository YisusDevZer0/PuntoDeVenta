<?php
include_once 'db_connect.php';

// Verificar si se recibieron todos los datos necesarios
$requiredFields = array('IdProdCedis', 'NumFactura', 'Proveedor', 'Cod_Barra', 'Nombre_Prod', 'Fk_Sucursal', 'Contabilizado', 'Fecha_Caducidad', 'Lote', 'Precio_Venta', 'Precio_C');
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
    $IdProdCedis = mysqli_real_escape_string($conn, $_POST['IdProdCedis']);
    $NumFactura = mysqli_real_escape_string($conn, $_POST['NumFactura']);
    $Proveedor = mysqli_real_escape_string($conn, $_POST['Proveedor']);
    $Cod_Barra = mysqli_real_escape_string($conn, $_POST['Cod_Barra']);
    $Nombre_Prod = mysqli_real_escape_string($conn, $_POST['Nombre_Prod']);
    $Fk_Sucursal = mysqli_real_escape_string($conn, $_POST['Fk_Sucursal']);
    $Contabilizado = mysqli_real_escape_string($conn, $_POST['Contabilizado']);
    $Fecha_Caducidad = mysqli_real_escape_string($conn, $_POST['Fecha_Caducidad']);
    $Lote = mysqli_real_escape_string($conn, $_POST['Lote']);
    $Precio_Venta = mysqli_real_escape_string($conn, $_POST['Precio_Venta']);
    $Precio_C = mysqli_real_escape_string($conn, $_POST['Precio_C']);

    // Consulta de actualización para modificar el registro existente
    $sql_update = "UPDATE Solicitudes_Ingresos 
                   SET 
                       Proveedor='$Proveedor', 
                       Cod_Barra='$Cod_Barra', 
                       Nombre_Prod='$Nombre_Prod', 
                       Fk_Sucursal='$Fk_Sucursal', 
                       Contabilizado='$Contabilizado', 
                       Fecha_Caducidad='$Fecha_Caducidad', 
                       Lote='$Lote', 
                       Precio_Venta='$Precio_Venta', 
                       Precio_C='$Precio_C'
                   WHERE IdProdCedis='$IdProdCedis'";

    if (mysqli_query($conn, $sql_update)) {
        echo json_encode(array("statusCode"=>200)); // Actualización exitosa
    } else {
        echo json_encode(array("statusCode"=>201, "error"=>mysqli_error($conn))); // Error en la actualización
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
