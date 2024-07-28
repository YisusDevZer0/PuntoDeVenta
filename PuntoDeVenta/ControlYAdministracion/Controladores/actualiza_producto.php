<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Inicializar respuesta por defecto
$response = array(
    'success' => false,
    'message' => 'Error en la solicitud.'
);

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario y limpiarlos
    $idProdPos = isset($_POST['ID_Prod_POSAct']) ? intval($_POST['ID_Prod_POSAct']) : 0;
    $codBarraActualiza = isset($_POST['Cod_BarraActualiza']) ? trim($_POST['Cod_BarraActualiza']) : '';

    // Verificar si los campos requeridos están presentes
    if ($idProdPos && !empty($codBarraActualiza)) {
        // Consulta preparada
        $sql = "UPDATE Productos_POS SET Cod_Barra=? WHERE ID_Prod_POS=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Enlazar parámetros
            $stmt->bind_param("si", $codBarraActualiza, $idProdPos);
            // Ejecutar consulta
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Producto actualizado correctamente.';
            } else {
                $response['message'] = 'Error al actualizar el producto: ' . $stmt->error;
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
}

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
