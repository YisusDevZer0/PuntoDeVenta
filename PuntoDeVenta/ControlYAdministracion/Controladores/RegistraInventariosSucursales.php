<?php
include_once 'db_connect.php';



// Verificar si $_POST["IdBasedatos"] está definido y es un arreglo antes de contar sus elementos
if(isset($_POST["IdBasedatos"]) && is_array($_POST["IdBasedatos"])) {
    $contador = count($_POST["IdBasedatos"]); 
} else {
    // Manejar el caso en el que $_POST["IdBasedatos"] no está definido o no es un arreglo
    $contador = 0;
}

$ProContador = 0;
$query = "INSERT INTO InventariosStocks_Conteos (`ID_Prod_POS`, `Cod_Barra`, `Nombre_Prod`, `Fk_sucursal`, `Precio_Venta`, `Precio_C`, `Contabilizado`, `StockEnMomento`, `Diferencia`, `Sistema`, `AgregadoPor`,  `ID_H_O_D`,`FechaInventario`,`Tipo_Ajuste`,`Anaquel`,`Repisa`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    // Verificar si los campos relevantes están definidos y no están vacíos antes de procesarlos
    if (!empty($_POST["IdBasedatos"][$i]) || !empty($_POST["CodBarras"][$i]) || !empty($_POST["NombreDelProducto"][$i])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";
        $values[] = $_POST["IdBasedatos"][$i];
        $values[] = $_POST["CodBarras"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["Fk_sucursal"][$i]; // Cambiado el nombre del campo según la nueva data
        $values[] = $_POST["PrecioVenta"][$i];
        $values[] = $_POST["PrecioCompra"][$i];
        $values[] = $_POST["Contabilizado"][$i];
        $values[] = $_POST["StockActual"][$i]; // Cambiado el nombre del campo según la nueva data
        $values[] = $_POST["Diferencia"][$i];
        $values[] = $_POST["Sistema"][$i]; // Agregar el campo 'Sistema' según la nueva data
        $values[] = $_POST["AgregoElVendedor"][$i];
      
        $values[] = $_POST["ID_H_O_D"][$i];
        $values[] = $_POST["FechaInv"][$i]; // Agregar el campo 'ID_H_O_D' según la nueva data
        $values[] = $_POST["Tipodeajusteaplicado"][$i];
        $values[] = $_POST["AnaquelSeleccionado"][$i];
        $values[] = $_POST["RepisaSeleccionada"][$i];
        $valueTypes .= 'ssssssssssssssss'; // Ajustar tipos según corresponda
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
