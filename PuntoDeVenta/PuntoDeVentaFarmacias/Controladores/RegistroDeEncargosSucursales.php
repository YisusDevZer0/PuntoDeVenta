<?php
include_once 'db_connect.php';

$contador = count($_POST["NombreDelProducto"]);
$ProContador = 0;
$query = "INSERT INTO encargos (`nombre_paciente`, `medicamento`, `cantidad`, `precioventa`, `fecha_encargo`, `estado`, `costo`, `abono_parcial`,`NumTicket`,`Fk_Sucursal`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["NombreDelCliente"][$i]) || !empty($_POST["NombreDelProducto"][$i]) || !empty($_POST["CantidadVendida"][$i])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?,?,?)";
        
        $values[] = $_POST["NombreDelCliente"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["CantidadVendida"][$i];
        $values[] = $_POST["PrecioVentaProd"][$i];
        $values[] = $_POST["FechaDeVenta"][$i];
        $values[] = $_POST["estado"][$i];
        $values[] = $_POST["TotalDeVenta"][$i];
        $values[] = $_POST["iptEfectivoOculto"][$i];
        $values[] = $_POST["NumeroDeTickeT"][$i];
        $values[] = $_POST["SucursalEnVenta"][$i];
        $valueTypes .= 'ssssssssss'; // Cada 's' representa un string, ajusta según el tipo de dato de cada campo
    }
}

$response = array();

if ($ProContador != 0) {
    $query .= implode(', ', $placeholders);
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $valueTypes, ...$values);

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
