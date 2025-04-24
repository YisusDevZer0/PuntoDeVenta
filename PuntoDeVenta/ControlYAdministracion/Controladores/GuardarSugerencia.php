<?php
header('Content-Type: application/json');
include "db_connection.php";
include_once "ControladorUsuario.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del POST
    $idProducto = $_POST['idProducto'];
    $codigoBarras = $_POST['codigoBarras'];
    $nombreProducto = $_POST['nombreProducto'];
    $existenciaActual = $_POST['existenciaActual'];
    $cantidadSugerida = $_POST['cantidadSugerida'];
    
    // Preparar la consulta SQL
    $sql = "INSERT INTO Sugerencias_Pedidos (
        ID_Prod_POS, 
        Cod_Barra, 
        Nombre_Prod, 
        Existencia_Actual, 
        Cantidad_Sugerida,
        ID_H_O_D,
        Fk_Sucursal,
        Usuario_Genera
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    if ($stmt = $conn->prepare($sql)) {
        // Vincular parámetros
        $stmt->bind_param(
            "sssiisss",
            $idProducto,
            $codigoBarras,
            $nombreProducto,
            $existenciaActual,
            $cantidadSugerida,
            $row['ID_H_O_D'],
            $row['Fk_Sucursal'],
            $row['Nombre_Apellidos']
        );

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Sugerencia guardada exitosamente"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Error al guardar la sugerencia: " . $stmt->error
            ];
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        $response = [
            "status" => "error",
            "message" => "Error en la preparación de la consulta: " . $conn->error
        ];
    }

    // Cerrar la conexión
    $conn->close();

    // Enviar respuesta
    echo json_encode($response);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Método no permitido"
    ]);
}
?> 