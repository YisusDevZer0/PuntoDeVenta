<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../dbconect.php";

try {
    $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';
    $sucursal = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : 0;
    
    if (empty($codigo) || $sucursal <= 0) {
        throw new Exception('Código de barras y sucursal son requeridos');
    }
    
    $sql = "SELECT 
                sp.Folio_Prod_Stock,
                sp.Cod_Barra,
                sp.Nombre_Prod as nombre,
                sp.Precio_Venta as precio_venta,
                sp.Precio_C as precio_compra,
                sp.Existencias_R as stock,
                sp.Fk_sucursal,
                s.Nombre_Sucursal as sucursal_nombre
            FROM Stock_POS sp
            LEFT JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
            WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $codigo, $sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Producto no encontrado en la sucursal especificada');
    }
    
    $producto = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'producto' => [
            'folio_stock' => $producto['Folio_Prod_Stock'],
            'codigo' => $producto['Cod_Barra'],
            'nombre' => $producto['nombre'],
            'precio_venta' => $producto['precio_venta'],
            'precio_compra' => $producto['precio_compra'],
            'stock' => $producto['stock'],
            'sucursal_id' => $producto['Fk_sucursal'],
            'sucursal_nombre' => $producto['sucursal_nombre']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($con)) {
        $con->close();
    }
}
?>