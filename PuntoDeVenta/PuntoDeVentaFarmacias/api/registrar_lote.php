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
    if (empty($cod_barra) || empty($lote) || empty($fecha_caducidad) || $cantidad <= 0) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    // Buscar el producto en Stock_POS
    $sqlProducto = "SELECT * FROM Stock_POS WHERE cod_barra = ? LIMIT 1";
    $stmtProducto = $con->prepare($sqlProducto);
    $stmtProducto->bind_param("s", $cod_barra);
    $stmtProducto->execute();
    $resultProducto = $stmtProducto->get_result();
    
    if ($resultProducto->num_rows === 0) {
        throw new Exception('Producto no encontrado en el sistema');
    }
    
    $producto = $resultProducto->fetch_assoc();
    
    // Verificar si ya existe un lote activo con el mismo código
    $sqlVerificar = "SELECT id_lote FROM productos_lotes_caducidad 
                     WHERE cod_barra = ? AND lote = ? AND estado = 'activo'";
    $stmtVerificar = $con->prepare($sqlVerificar);
    $stmtVerificar->bind_param("ss", $cod_barra, $lote);
    $stmtVerificar->execute();
    $resultVerificar = $stmtVerificar->get_result();
    
    if ($resultVerificar->num_rows > 0) {
        throw new Exception('Ya existe un lote activo con este código y número de lote');
    }
    
    // Insertar nuevo lote
    $sqlInsert = "INSERT INTO productos_lotes_caducidad 
                  (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, 
                   fecha_ingreso, cantidad_inicial, cantidad_actual, sucursal_id, 
                   proveedor, precio_compra, precio_venta, estado, usuario_registro, observaciones)
                  VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
    
    $stmtInsert = $con->prepare($sqlInsert);
    $stmtInsert->bind_param("issssiiisssis",
        $producto['folio_stock'],
        $cod_barra,
        $producto['nombre_producto'],
        $lote,
        $fecha_caducidad,
        $cantidad,
        $cantidad,
        $sucursal_id,
        $producto['proveedor'],
        $producto['precio_compra'],
        $producto['precio_venta'],
        $usuario_registro,
        $observaciones
    );
    
    if ($stmtInsert->execute()) {
        $idLote = $con->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Lote registrado exitosamente',
            'id_lote' => $idLote
        ]);
    } else {
        throw new Exception('Error al registrar el lote: ' . $con->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
