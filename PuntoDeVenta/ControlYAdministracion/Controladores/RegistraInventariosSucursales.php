<?php
include_once 'db_connect.php';

$contador = count($_POST["IdBasedatos"]);
$ProContador = 0;
$query = "INSERT INTO InventariosSucursales (`ID_Prod_POS`, `Cod_Barra`, `Nombre_Prod`, `Contabilizado`, `StockEnMomento`, `ExistenciasAjuste`, `Precio_Venta`, `Precio_C`,`AgregadoPor`,`FechaInventario`,`Fk_Sucursal`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["IdBasedatos"][$i]) || !empty($_POST["CodBarras"][$i]) || !empty($_POST["NombreDelProducto"][$i])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?,?,?,?,?,?,?,?,?)";
        $values[] = $_POST["IdBasedatos"][$i];
        $values[] = $_POST["CodBarras"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["Contabilizado"][$i];
        $values[] = $_POST["StockActual"][$i];
        $values[] = $_POST["Diferencia"][$i];
        $values[] = $_POST["PrecioVenta"][$i]; // Precio de venta
        $values[] = $_POST["PrecioCompra"][$i]; // Precio de compra
        $values[] = $_POST["AgregoElVendedor"][$i];
        $values[] = $_POST["FechaDeInventario"][$i];
        $values[] = $_POST["Sucursal"][$i];
        $valueTypes .= 'sssssssssss'; // Ajustar tipos según corresponda
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
            $response['message'] = 'Error en la consulta de inserción: ' . mysqli_error($conn);
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
