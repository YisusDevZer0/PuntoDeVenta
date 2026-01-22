<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Controladores/db_connect.php";
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
    
    mysqli_begin_transaction($conn);
    
    if ($id_historial > 0) {
        // Actualizar lote existente
        $sql_historial = "SELECT * FROM Historial_Lotes WHERE ID_Historial = ?";
        $stmt_historial = mysqli_prepare($conn, $sql_historial);
        if (!$stmt_historial) {
            throw new Exception('Error al preparar consulta: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_historial, "i", $id_historial);
        mysqli_stmt_execute($stmt_historial);
        $result_historial = mysqli_stmt_get_result($stmt_historial);
        $historial_actual = mysqli_fetch_assoc($result_historial);
        mysqli_stmt_close($stmt_historial);
        
        if (!$historial_actual) {
            throw new Exception('Lote no encontrado');
        }
        
        // Registrar movimiento
        $sql_movimiento = "INSERT INTO Gestion_Lotes_Movimientos (
            ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Anterior, Lote_Nuevo,
            Fecha_Caducidad_Anterior, Fecha_Caducidad_Nueva, Cantidad,
            Tipo_Movimiento, Usuario_Modifico, Observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)";
        
        $stmt_mov = mysqli_prepare($conn, $sql_movimiento);
        if (!$stmt_mov) {
            throw new Exception('Error al preparar movimiento: ' . mysqli_error($conn));
        }
        $cod_barra_historial = $historial_actual['Cod_Barra'] ?? $cod_barra;
        $cantidad_mov = $cantidad > 0 ? $cantidad : $historial_actual['Existencias'];
        mysqli_stmt_bind_param(
            $stmt_mov,
            "isissssis",
            $historial_actual['ID_Prod_POS'],
            $cod_barra_historial,
            $historial_actual['Fk_sucursal'],
            $historial_actual['Lote'],
            $lote_nuevo,
            $historial_actual['Fecha_Caducidad'],
            $fecha_caducidad_nueva,
            $cantidad_mov,
            $usuario,
            $observaciones
        );
        mysqli_stmt_execute($stmt_mov);
        mysqli_stmt_close($stmt_mov);
        
        // Actualizar Historial_Lotes
        $sql_update = "UPDATE Historial_Lotes SET
            Lote = ?,
            Fecha_Caducidad = ?,
            Existencias = ?,
            Usuario_Modifico = ?,
            Fecha_Registro = NOW()
        WHERE ID_Historial = ?";
        
        $cantidad_final = $cantidad > 0 ? $cantidad : $historial_actual['Existencias'];
        $stmt_update = mysqli_prepare($conn, $sql_update);
        if (!$stmt_update) {
            throw new Exception('Error al preparar actualización: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_update, "ssisi", $lote_nuevo, $fecha_caducidad_nueva, $cantidad_final, $usuario, $id_historial);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
        
        // Actualizar Stock_POS si el lote coincide
        $sql_stock = "UPDATE Stock_POS SET
            Lote = ?,
            Fecha_Caducidad = ?
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?";
        
        $stmt_stock = mysqli_prepare($conn, $sql_stock);
        if (!$stmt_stock) {
            throw new Exception('Error al preparar actualización de stock: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_stock, "ssiis", $lote_nuevo, $fecha_caducidad_nueva, 
            $historial_actual['ID_Prod_POS'], $historial_actual['Fk_sucursal'], 
            $historial_actual['Lote']);
        mysqli_stmt_execute($stmt_stock);
        mysqli_stmt_close($stmt_stock);
        
    } else {
        // Crear nuevo lote
        // Primero obtener información del producto
        $sql_producto = "SELECT ID_Prod_POS, Fk_sucursal FROM Stock_POS WHERE Cod_Barra = ? LIMIT 1";
        $stmt_prod = mysqli_prepare($conn, $sql_producto);
        if (!$stmt_prod) {
            throw new Exception('Error al preparar consulta de producto: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_prod, "s", $cod_barra);
        mysqli_stmt_execute($stmt_prod);
        $result_prod = mysqli_stmt_get_result($stmt_prod);
        $producto = mysqli_fetch_assoc($result_prod);
        mysqli_stmt_close($stmt_prod);
        
        if (!$producto) {
            throw new Exception('Producto no encontrado');
        }
        
        // Verificar si ya existe un lote igual
        $sql_existe = "SELECT ID_Historial FROM Historial_Lotes 
                      WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?";
        $stmt_existe = mysqli_prepare($conn, $sql_existe);
        if (!$stmt_existe) {
            throw new Exception('Error al preparar verificación: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_existe, "iis", $producto['ID_Prod_POS'], $producto['Fk_sucursal'], $lote_nuevo);
        mysqli_stmt_execute($stmt_existe);
        $result_existe = mysqli_stmt_get_result($stmt_existe);
        $existe = mysqli_fetch_assoc($result_existe);
        mysqli_stmt_close($stmt_existe);
        
        if ($existe) {
            throw new Exception('Ya existe un lote con ese número');
        }
        
        // Insertar en Historial_Lotes
        $sql_insert = "INSERT INTO Historial_Lotes (
            ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso,
            Existencias, Usuario_Modifico
        ) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
        
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        if (!$stmt_insert) {
            throw new Exception('Error al preparar inserción: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_insert, "iissis", 
            $producto['ID_Prod_POS'], 
            $producto['Fk_sucursal'], 
            $lote_nuevo, 
            $fecha_caducidad_nueva,
            $cantidad,
            $usuario
        );
        mysqli_stmt_execute($stmt_insert);
        $id_historial_nuevo = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_insert);
        
        // Activar automáticamente el control de lotes en Stock_POS para este producto-sucursal
        // Verificar si existe la columna primero (compatibilidad hacia atrás)
        $sql_check_column = "SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'";
        $result_column = mysqli_query($conn, $sql_check_column);
        if ($result_column && mysqli_num_rows($result_column) > 0) {
            // La columna existe, actualizar el stock de este producto en esta sucursal
            $sql_activar = "UPDATE Stock_POS 
                           SET Control_Lotes_Caducidad = 1 
                           WHERE ID_Prod_POS = ? AND Fk_sucursal = ? 
                           AND (Control_Lotes_Caducidad IS NULL OR Control_Lotes_Caducidad = 0)";
            $stmt_activar = mysqli_prepare($conn, $sql_activar);
            if ($stmt_activar) {
                mysqli_stmt_bind_param($stmt_activar, "ii", $producto['ID_Prod_POS'], $producto['Fk_sucursal']);
                mysqli_stmt_execute($stmt_activar);
                mysqli_stmt_close($stmt_activar);
            }
        }
        if ($result_column) {
            mysqli_free_result($result_column);
        }
        
        // Registrar movimiento
        $sql_movimiento = "INSERT INTO Gestion_Lotes_Movimientos (
            ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo,
            Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)";
        
        $stmt_mov = mysqli_prepare($conn, $sql_movimiento);
        if (!$stmt_mov) {
            throw new Exception('Error al preparar movimiento nuevo: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_mov, "isissis",
            $producto['ID_Prod_POS'],
            $cod_barra,
            $producto['Fk_sucursal'],
            $lote_nuevo,
            $fecha_caducidad_nueva,
            $cantidad,
            $usuario,
            $observaciones
        );
        mysqli_stmt_execute($stmt_mov);
        mysqli_stmt_close($stmt_mov);
    }
    
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Lote actualizado correctamente']);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>
