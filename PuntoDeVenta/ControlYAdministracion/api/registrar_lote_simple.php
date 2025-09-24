<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Obtener datos del formulario
    $cod_barra = $_POST['codigoBarra'] ?? '';
    $lote = $_POST['lote'] ?? '';
    $fecha_caducidad = $_POST['fechaCaducidad'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $sucursal_id = $_POST['sucursal'] ?? 0;
    $usuario_registro = $_POST['usuarioRegistro'] ?? 1;
    $observaciones = $_POST['observaciones'] ?? '';
    
    // Validar datos requeridos
    if (empty($cod_barra) || empty($lote) || empty($fecha_caducidad) || $cantidad <= 0 || $sucursal_id <= 0) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    // Verificar si las tablas existen
    $checkTable = $con->query("SHOW TABLES LIKE 'productos_lotes_caducidad'");
    if ($checkTable->num_rows === 0) {
        throw new Exception('Las tablas del módulo de caducados no han sido creadas. Ejecute el script SQL primero.');
    }
    
    // Buscar el producto en Stock_POS
    $sql_producto = "SELECT sp.Folio_Prod_Stock, sp.Nombre_Prod, sp.Precio_Venta, sp.Precio_C 
                     FROM Stock_POS sp 
                     WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?";
    $stmt_producto = $con->prepare($sql_producto);
    $stmt_producto->bind_param("si", $cod_barra, $sucursal_id);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();
    
    if ($result_producto->num_rows === 0) {
        throw new Exception('Producto no encontrado en la sucursal especificada');
    }
    
    $producto = $result_producto->fetch_assoc();
    
    // Verificar si ya existe un lote con el mismo código
    $sql_verificar = "SELECT id_lote FROM productos_lotes_caducidad 
                      WHERE cod_barra = ? AND lote = ? AND sucursal_id = ? AND estado != 'retirado'";
    $stmt_verificar = $con->prepare($sql_verificar);
    $stmt_verificar->bind_param("ssi", $cod_barra, $lote, $sucursal_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    
    if ($result_verificar->num_rows > 0) {
        throw new Exception('Ya existe un lote activo con este código en la sucursal');
    }
    
    // Insertar el nuevo lote
    $sql_insert = "INSERT INTO productos_lotes_caducidad 
                   (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, fecha_ingreso, 
                    cantidad_inicial, cantidad_actual, sucursal_id, precio_compra, precio_venta, 
                    estado, usuario_registro, observaciones) 
                   VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, 'activo', ?, ?)";
    
    $stmt_insert = $con->prepare($sql_insert);
    $stmt_insert->bind_param("issssiiddiis", 
        $producto['Folio_Prod_Stock'],
        $cod_barra,
        $producto['Nombre_Prod'],
        $lote,
        $fecha_caducidad,
        $cantidad,
        $cantidad,
        $sucursal_id,
        $producto['Precio_C'],
        $producto['Precio_Venta'],
        $usuario_registro,
        $observaciones
    );
    
    if ($stmt_insert->execute()) {
        $id_lote = $con->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Lote registrado exitosamente',
            'id_lote' => $id_lote
        ]);
    } else {
        throw new Exception('Error al insertar el lote: ' . $con->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
