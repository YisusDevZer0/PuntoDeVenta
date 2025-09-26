<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Controladores/ControladorUsuario.php";

$usuario_id = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1;
$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;

try {
    $busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'codigo'; // codigo, nombre, ambos
    
    if (empty($busqueda)) {
        throw new Exception('Término de búsqueda requerido');
    }
    
    $sql = "SELECT 
                sp.Folio_Prod_Stock,
                sp.Cod_Barra,
                sp.Nombre_Prod,
                sp.Precio_Venta,
                sp.Precio_C,
                sp.Existencias_R,
                sp.Fk_sucursal,
                s.Nombre_Sucursal,
                plc.Lote,
                plc.Fecha_Caducidad
            FROM Stock_POS sp
            LEFT JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
            LEFT JOIN productos_lotes_caducidad plc ON sp.ID_Prod_POS = plc.ID_Prod_POS AND sp.Fk_sucursal = plc.Fk_sucursal
            WHERE sp.Fk_sucursal = ? AND sp.Existencias_R > 0";
    
    $params = [$sucursal_id];
    $types = 'i';
    
    if ($tipo === 'codigo' || $tipo === 'ambos') {
        $sql .= " AND sp.Cod_Barra LIKE ?";
        $params[] = "%$busqueda%";
        $types .= 's';
    }
    
    if ($tipo === 'nombre' || $tipo === 'ambos') {
        $sql .= " AND sp.Nombre_Prod LIKE ?";
        $params[] = "%$busqueda%";
        $types .= 's';
    }
    
    $sql .= " ORDER BY sp.Nombre_Prod ASC LIMIT 20";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'id' => $row['Folio_Prod_Stock'],
            'codigo' => $row['Cod_Barra'],
            'nombre' => $row['Nombre_Prod'],
            'precio_venta' => $row['Precio_Venta'],
            'precio_compra' => $row['Precio_C'],
            'stock' => $row['Existencias_R'],
            'sucursal_id' => $row['Fk_sucursal'],
            'sucursal_nombre' => $row['Nombre_Sucursal'],
            'lote' => $row['Lote'],
            'fecha_caducidad' => $row['Fecha_Caducidad']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
