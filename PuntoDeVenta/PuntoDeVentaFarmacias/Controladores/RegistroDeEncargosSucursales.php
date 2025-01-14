<?php
include_once 'db_connect.php';

// Verificar la conexión
if (!$conn) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Error al conectar con la base de datos: ' . mysqli_connect_error()
    ]));
}

$contador = count($_POST["NombreDelProducto"]);
$ProContador = 0;
$query = "INSERT INTO encargos (`nombre_paciente`, `medicamento`, `cantidad`, `precioventa`, `fecha_encargo`, `estado`, `costo`, `abono_parcial`, `NumTicket`, `Fk_Sucursal`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';

for ($i = 0; $i < $contador; $i++) {
    if (
        !empty($_POST["NombreDelCliente"][$i]) &&
        !empty($_POST["NombreDelProducto"][$i]) &&
        !empty($_POST["CantidadVendida"][$i])
    ) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $values[] = htmlspecialchars($_POST["NombreDelCliente"][$i], ENT_QUOTES, 'UTF-8');
        $values[] = htmlspecialchars($_POST["NombreDelProducto"][$i], ENT_QUOTES, 'UTF-8');
        $values[] = (int)$_POST["CantidadVendida"][$i];
        $values[] = (float)$_POST["PrecioVentaProd"][$i];
        $values[] = htmlspecialchars($_POST["FechaDeVenta"][$i], ENT_QUOTES, 'UTF-8');
        $values[] = htmlspecialchars($_POST["estado"][$i], ENT_QUOTES, 'UTF-8');
        $values[] = (float)$_POST["TotalDeVenta"][$i];
        $values[] = (float)$_POST["iptEfectivoOculto"][$i];
        $values[] = htmlspecialchars($_POST["NumeroDeTickeT"][$i], ENT_QUOTES, 'UTF-8');
        $values[] = htmlspecialchars($_POST["SucursalEnVenta"][$i], ENT_QUOTES, 'UTF-8');
        $valueTypes .= 'ssidsddsss';
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

echo json_enco
