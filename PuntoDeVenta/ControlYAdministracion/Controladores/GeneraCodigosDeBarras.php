<?php
include "db_connect.php";

// Inicializar respuesta por defecto
$response = array(
    'statusCode' => 201, // Por defecto se establece en 201 para indicar error
    'message' => 'Error en la actualización.'
);

// Validar y limpiar datos
$idProdPos = isset($_POST['ID_Prod_POSAct']) ? intval($_POST['ID_Prod_POSAct']) : 0;
$codBarraActualiza = isset($_POST['Cod_BarraActualiza']) ? trim($_POST['Cod_BarraActualiza']) : '';

// Verificar si los datos necesarios están presentes
if ($idProdPos && !empty($codBarraActualiza)) {
    // Usar consulta preparada para evitar inyección SQL
    $stmt = $conn->prepare("UPDATE `Productos_POS` SET `Cod_Barra` = ? WHERE `ID_Prod_POS` = ?");
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("si", $codBarraActualiza, $idProdPos);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response['statusCode'] = 200;
            $response['message'] = 'Producto actualizado correctamente.';
        } else {
            $response['message'] = 'Error al ejecutar la consulta: ' . $stmt->error;
        }
        
        // Cerrar declaración
        $stmt->close();
    } else {
        $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    }
} else {
    $response['message'] = 'Datos incompletos.';
}

// Cerrar la conexión
$conn->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
