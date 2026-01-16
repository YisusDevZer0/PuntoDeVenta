<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';

if (empty($cod_barra)) {
    echo json_encode(['success' => false, 'message' => 'Código de barras requerido']);
    exit;
}

$sql = "SELECT ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal 
        FROM Stock_POS 
        WHERE Cod_Barra = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cod_barra);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $producto = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'producto' => $producto
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Producto no encontrado'
    ]);
}

$stmt->close();
$conn->close();
?>
