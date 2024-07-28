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
    // Obtener datos del formulario
    $idProdPos = isset($_POST['ID_Prod_POS']) ? $conn->real_escape_string($_POST['ID_Prod_POS']) : '';
    $codBarraActualiza = isset($_POST['Cod_BarraActualiza']) ? $conn->real_escape_string($_POST['Cod_BarraActualiza']) : '';

    // Verificar si los campos requeridos están presentes
    if (!empty($idProdPos) && !empty($codBarraActualiza)) {
        // Construir consulta SQL para actualizar el producto
        $sql = "UPDATE Productos_POS SET Cod_Barra='$codBarraActualiza' WHERE ID_Prod_POS='$idProdPos'";

        // Ejecutar la consulta
        if ($conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = 'Producto actualizado correctamente.';
        } else {
            $response['message'] = 'Error al actualizar el producto: ' . $conn->error;
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
