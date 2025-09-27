<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $codigoBarra = $_GET['codigo'] ?? '';
    
    if (empty($codigoBarra)) {
        throw new Exception('Código de barra requerido');
    }
    
    $sql = "SELECT * FROM Stock_POS WHERE cod_barra = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $codigoBarra);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Producto no encontrado');
    }
    
    $producto = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'producto' => $producto
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
