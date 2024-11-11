<?php
include_once 'db_connect.php';

$contador = count($_POST["NombreDelCliente"]);
$ProContador = 0;
$query = "INSERT INTO encargos (`nombre_paciente`, `medicamento`, `cantidad`, `precioventa`, `fecha_encargo`, `estado`, `fecha_creacion`, `costo`, `abono_parcial`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["NombreDelCliente"][$i]) || !empty($_POST["medicamento"][$i]) || !empty($_POST["cantidad"][$i])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $values[] = $_POST["NombreDelCliente"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["CantidadVendida"][$i];
        $values[] = $_POST["PrecioVentaProd"][$i];
        $values[] = $_POST["FechaDeVenta"][$i];
        $values[] = $_POST["estado"][$i];
        $values[] = $_POST["fecha_creacion"][$i];
        $values[] = $_POST["TotalDeVenta"][$i];
        $values[] = $_POST["iptEfectivoOculto"][$i];

        $valueTypes .= 'sssssssss'; // Cambia el tipo según sea necesario: 's' para strings, 'i' para enteros, 'd' para decimales
    }
}

$response = array();

if ($ProContador != 0) {
    $query .= implode(', ', $placeholders);
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Usa `call_user_func_array` para pasar los valores de forma correcta
        $bindNames[] = &$valueTypes;
        foreach ($values as $key => $value) {
            $bindNames[] = &$values[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindNames);

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
