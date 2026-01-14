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

try {
    // Obtener datos
    $id_historial = isset($_POST['id_historial']) ? (int)$_POST['id_historial'] : 0;
    $cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';
    $lote_nuevo = isset($_POST['lote_nuevo']) ? trim($_POST['lote_nuevo']) : '';
    $fecha_caducidad_nueva = isset($_POST['fecha_caducidad_nueva']) ? $_POST['fecha_caducidad_nueva'] : '';
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $usuario = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Sistema';
    
    // Validaciones
    if (empty($cod_barra) || empty($lote_nuevo) || empty($fecha_caducidad_nueva)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }
    
    // Validar fecha
    $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha_caducidad_nueva);
    if (!$fecha_valida) {
        echo json_encode(['success' => false, 'message' => 'Fecha de caducidad inválida']);
        exit;
    }
    
    $conn->begin_transaction();
    
    if ($id_historial > 0) {
        // Actualizar lote existente
        $sql_historial = "SELECT * FROM Historial_Lotes WHERE ID_Historial = ?";
        $stmt_historial = $conn->prepare($sql_historial);
        $stmt_historial->bind_param("i", $id_historial);
        $stmt_historial->execute();
        $historial_actual = $stmt_historial->get_result()->fetch_assoc();
        $stmt_historial->close();
        
        if (!$historial_actual) {
            throw new Exception('Lote no encontrado');
        }
        
        // Registrar movimiento
        $sql_movimiento = "INSERT INTO Gestion_Lotes_Movimientos (
            ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Anterior, Lote_Nuevo,
            Fecha_Caducidad_Anterior, Fecha_Caducidad_Nueva, Cantidad,
            Tipo_Movimiento, Usuario_Modifico, Observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)";
        
        $stmt_mov = $conn->prepare($sql_movimiento);
        $stmt_mov->bind_param(
            "isissssis",
            $historial_actual['ID_Prod_POS'],
            $historial_actual['Cod_Barra'] ?? $cod_barra,
            $historial_actual['Fk_sucursal'],
            $historial_actual['Lote'],
            $lote_nuevo,
            $historial_actual['Fecha_Caducidad'],
            $fecha_caducidad_nueva,
            $cantidad > 0 ? $cantidad : $historial_actual['Existencias'],
            $usuario,
            $observaciones
        );
        $stmt_mov->execute();
        $stmt_mov->close();
        
        // Actualizar Historial_Lotes
        $sql_update = "UPDATE Historial_Lotes SET
            Lote = ?,
            Fecha_Caducidad = ?,
            Existencias = ?,
            Usuario_Modifico = ?,
            Fecha_Registro = NOW()
        WHERE ID_Historial = ?";
        
        $cantidad_final = $cantidad > 0 ? $cantidad : $historial_actual['Existencias'];
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssisi", $lote_nuevo, $fecha_caducidad_nueva, $cantidad_final, $usuario, $id_historial);
        $stmt_update->execute();
        $stmt_update->close();
        
        // Actualizar Stock_POS si el lote coincide
        $sql_stock = "UPDATE Stock_POS SET
            Lote = ?,
            Fecha_Caducidad = ?
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?";
        
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("ssiis", $lote_nuevo, $fecha_caducidad_nueva, 
            $historial_actual['ID_Prod_POS'], $historial_actual['Fk_sucursal'], 
            $historial_actual['Lote']);
        $stmt_stock->execute();
        $stmt_stock->close();
        
    } else {
        // Crear nuevo lote
        // Primero obtener información del producto
        $sql_producto = "SELECT ID_Prod_POS, Fk_sucursal FROM Stock_POS WHERE Cod_Barra = ? LIMIT 1";
        $stmt_prod = $conn->prepare($sql_producto);
        $stmt_prod->bind_param("s", $cod_barra);
        $stmt_prod->execute();
        $producto = $stmt_prod->get_result()->fetch_assoc();
        $stmt_prod->close();
        
        if (!$producto) {
            throw new Exception('Producto no encontrado');
        }
        
        // Verificar si ya existe un lote igual
        $sql_existe = "SELECT ID_Historial FROM Historial_Lotes 
                      WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?";
        $stmt_existe = $conn->prepare($sql_existe);
        $stmt_existe->bind_param("iis", $producto['ID_Prod_POS'], $producto['Fk_sucursal'], $lote_nuevo);
        $stmt_existe->execute();
        $existe = $stmt_existe->get_result()->fetch_assoc();
        $stmt_existe->close();
        
        if ($existe) {
            throw new Exception('Ya existe un lote con ese número');
        }
        
        // Insertar en Historial_Lotes
        $sql_insert = "INSERT INTO Historial_Lotes (
            ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso,
            Existencias, Usuario_Modifico
        ) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iissis", 
            $producto['ID_Prod_POS'], 
            $producto['Fk_sucursal'], 
            $lote_nuevo, 
            $fecha_caducidad_nueva,
            $cantidad,
            $usuario
        );
        $stmt_insert->execute();
        $id_historial_nuevo = $conn->insert_id;
        $stmt_insert->close();
        
        // Registrar movimiento
        $sql_movimiento = "INSERT INTO Gestion_Lotes_Movimientos (
            ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo,
            Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)";
        
        $stmt_mov = $conn->prepare($sql_movimiento);
        $stmt_mov->bind_param("isissis",
            $producto['ID_Prod_POS'],
            $cod_barra,
            $producto['Fk_sucursal'],
            $lote_nuevo,
            $fecha_caducidad_nueva,
            $cantidad,
            $usuario,
            $observaciones
        );
        $stmt_mov->execute();
        $stmt_mov->close();
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Lote actualizado correctamente']);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
