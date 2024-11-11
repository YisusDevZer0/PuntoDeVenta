<?php
include_once 'db_connect.php';

$contador = count($_POST["NombreDelCliente"]);
$response = array();
$errores = 0;

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["NombreDelCliente"][$i]) || !empty($_POST["medicamento"][$i]) || !empty($_POST["cantidad"][$i])) {
        
        // Preparar la consulta para cada fila sin el campo fecha_creacion
        $query = "INSERT INTO encargos (`nombre_paciente`, `medicamento`, `cantidad`, `precioventa`, `fecha_encargo`, `estado`, `costo`, `abono_parcial`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        // Verificar que la preparación fue exitosa
        if ($stmt) {
            // Asignar valores de cada campo individualmente
            mysqli_stmt_bind_param($stmt, 'ssssssss',
                $_POST["NombreDelCliente"][$i],
                $_POST["NombreDelProducto"][$i],
                $_POST["CantidadVendida"][$i],
                $_POST["PrecioVentaProd"][$i],
                $_POST["FechaDeVenta"][$i],
                $_POST["estado"][$i],
                $_POST["TotalDeVenta"][$i],
                $_POST["iptEfectivoOculto"][$i]
            );

            // Ejecutar la consulta
            if (!mysqli_stmt_execute($stmt)) {
                $errores++;
            }

            // Cerrar cada statement después de la ejecución
            mysqli_stmt_close($stmt);
        } else {
            $errores++;
        }
    }
}

// Verificar el resultado y generar respuesta
if ($errores === 0) {
    $response['status'] = 'success';
    $response['message'] = 'Registro(s) agregado(s) correctamente.';
} else {
    $response['status'] = 'error';
    $response['message'] = "Error al agregar algunos registros. Total de errores: $errores.";
}

echo json_encode($response);

mysqli_close($conn);
?>
