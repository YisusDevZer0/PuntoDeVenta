<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Consultas/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Datos no válidos');
    }
    
    // Validar datos requeridos
    $required_fields = ['cod_barra', 'lote', 'fecha_caducidad', 'cantidad', 'sucursal_id', 'usuario_registro'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Campo requerido: $field");
        }
    }
    
    // Buscar el producto en Stock_POS
    $sql_producto = "SELECT sp.Folio_Prod_Stock, sp.Nombre_Prod, sp.Precio_Venta, sp.Precio_C 
                     FROM Stock_POS sp 
                     WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?";
    $stmt_producto = $conn->prepare($sql_producto);
    $stmt_producto->bind_param("si", $data['cod_barra'], $data['sucursal_id']);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();
    
    if ($result_producto->num_rows === 0) {
        throw new Exception('Producto no encontrado en la sucursal especificada');
    }
    
    $producto = $result_producto->fetch_assoc();
    
    // Verificar si ya existe un lote con el mismo código
    $sql_verificar = "SELECT id_lote FROM productos_lotes_caducidad 
                      WHERE cod_barra = ? AND lote = ? AND sucursal_id = ? AND estado != 'retirado'";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ssi", $data['cod_barra'], $data['lote'], $data['sucursal_id']);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    
    if ($result_verificar->num_rows > 0) {
        throw new Exception('Ya existe un lote activo con este código en la sucursal');
    }
    
    // Insertar nuevo lote
    $sql_insert = "INSERT INTO productos_lotes_caducidad 
                   (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, fecha_ingreso, 
                    cantidad_inicial, cantidad_actual, sucursal_id, proveedor, precio_compra, precio_venta, 
                    estado, usuario_registro, observaciones) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
    
    $stmt_insert = $conn->prepare($sql_insert);
    $fecha_ingreso = date('Y-m-d');
    $proveedor = $data['proveedor'] ?? null;
    $precio_compra = $data['precio_compra'] ?? null;
    $observaciones = $data['observaciones'] ?? null;
    
    $stmt_insert->bind_param("isssssiiisssis", 
        $producto['Folio_Prod_Stock'],
        $data['cod_barra'],
        $producto['Nombre_Prod'],
        $data['lote'],
        $data['fecha_caducidad'],
        $fecha_ingreso,
        $data['cantidad'],
        $data['cantidad'],
        $data['sucursal_id'],
        $proveedor,
        $precio_compra,
        $producto['Precio_Venta'],
        $data['usuario_registro'],
        $observaciones
    );
    
    if ($stmt_insert->execute()) {
        $id_lote = $conn->insert_id;
        
        // Registrar en historial
        $sql_historial = "INSERT INTO caducados_historial 
                         (id_lote, tipo_movimiento, cantidad_nueva, fecha_caducidad_nueva, 
                          sucursal_origen, usuario_movimiento, observaciones) 
                         VALUES (?, 'registro', ?, ?, ?, ?, ?)";
        
        $stmt_historial = $conn->prepare($sql_historial);
        $observaciones_historial = "Registro inicial del lote: " . $data['lote'];
        $stmt_historial->bind_param("iisiiis", 
            $id_lote,
            $data['cantidad'],
            $data['fecha_caducidad'],
            $data['sucursal_id'],
            $data['usuario_registro'],
            $observaciones_historial
        );
        $stmt_historial->execute();
        
        // Actualizar stock en Stock_POS si es necesario
        $sql_update_stock = "UPDATE Stock_POS 
                             SET Lote = ?, Fecha_Caducidad = ?, Existencias_R = Existencias_R + ? 
                             WHERE Folio_Prod_Stock = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ssii", 
            $data['lote'], 
            $data['fecha_caducidad'], 
            $data['cantidad'],
            $producto['Folio_Prod_Stock']
        );
        $stmt_update_stock->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Lote registrado exitosamente',
            'id_lote' => $id_lote
        ]);
        
    } else {
        throw new Exception('Error al registrar el lote: ' . $conn->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
