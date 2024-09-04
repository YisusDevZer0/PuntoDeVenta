<?php
include_once 'db_connect.php';

// Verifica que la conexión a la base de datos se haya realizado correctamente
if (!$conn) {
    $response['status'] = 'error';
    $response['message'] = 'Error de conexión a la base de datos: ' . mysqli_connect_error();
    echo json_encode($response);
    exit();
}

$contador = count($_POST["IdBasedatos"]);
$ProContador = 0;
$query = "INSERT INTO IngresosFarmacias (`ID_Prod_POS`, `NumFactura`, `Proveedor`, `Cod_Barra`, `Nombre_Prod`, `Fk_Sucursal`, `Contabilizado`, `Fecha_Caducidad`, `Lote`, `PrecioMaximo`, `Precio_Venta`, `Precio_C`, `AgregadoPor`, `AgregadoEl`, `FechaInventario`,`Estatus`,`NumOrden`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["IdBasedatos"][$i]) || !empty($_POST["CodBarras"][$i]) || !empty($_POST["NombreDelProducto"][$i])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
        $values[] = $_POST["IdBasedatos"][$i];
        $values[] = $_POST["FacturaNumber"][$i];
        $values[] = $_POST["Proveedor"][$i];
        $values[] = $_POST["CodBarras"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["FkSucursal"][$i];
        $values[] = $_POST["Contabilizado"][$i];
        $values[] = $_POST["FechaCaducidad"][$i];
        $values[] = $_POST["Lote"][$i];
        $values[] = $_POST["PrecioMaximo"][$i];
        $values[] = $_POST["PrecioVenta"][$i]; // Precio de venta
        $values[] = $_POST["PrecioCompra"][$i]; // Precio de compra
        $values[] = $_POST["AgregoElVendedor"][$i];
        $values[] = $_POST["AgregadoEl"][$i];
        $values[] = $_POST["FechaDeInventario"][$i];
        $values[] = $_POST["Estatusdesolicitud"][$i]; 
        $values[] = $_POST["NumberOrden"][$i]; 
        $valueTypes .= 'sssssssssssssssss'; // Ajustar tipos según corresponda
    }
}

$response = array();

if ($ProContador != 0) {
    $query .= implode(', ', $placeholders);
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Enlace de parámetros
        mysqli_stmt_bind_param($stmt, $valueTypes, ...$values);

        // Ejecución de consulta
        $resultadocon = mysqli_stmt_execute($stmt);

        if ($resultadocon) {
            $response['status'] = 'success';
            $response['message'] = 'Registro(s) agregado(s) correctamente.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error en la consulta de inserción: ' . mysqli_stmt_error($stmt);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error en la preparación de la consulta: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se encontraron registros para agregar.';
}

echo json_encode($response);

mysqli_close($conn);
?>
